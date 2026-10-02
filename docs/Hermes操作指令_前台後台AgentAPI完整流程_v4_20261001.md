# Hermes 操作指令：美邁官網 前台↔後台↔Agent API 完整流程（v4，2026-10-01 實測版）

> 這份是給 Hermes（老闆的 AI 代理）的**操作指令**，可以整份貼進 Hermes 的 system prompt／技能。
> 內容全部以 2026-10-01 在真實 Laravel＋Nuxt 環境**實際跑過**的結果為準（100 步 Agent API 測試、33 個前台頁面、27 個後台頁面）。
> 舊的三份文件仍然有效（《美邁後台與AgentAPI完整操作手冊_v3》、《Hermes技能_美邁後台API操作_SKILL_v3》、《網站地圖與前後台對接對照_v3》），**有衝突時以這一份為準**。

---

## 0. 一分鐘看懂整個系統

```
老闆／Hermes ──(Agent API, X-Agent-Key)──▶ 後台 Laravel（admin.meimai.com.tw）
                                              │  資料存 MySQL，圖片存 storage/files/1/
                                              ▼
前台 Nuxt（clarion.meimai.com.tw）──(公開 API /api/…)──▶ 每次有人打開頁面就即時讀後台資料
```

- **後台改了＝前台馬上變**（前台是即時讀取，重新整理就看得到；`POST /publish` 會回「不需要重新產生」）。唯一例外：**新功能的前台程式**要工程師部署新版才會出現（例如這次的類別開關、標誌、SEO 覆蓋），部署之後就不用再管。
- Agent API 走的是**跟後台畫面一模一樣的控制器**，驗證規則、欄位限制與後台按鈕完全相同，沒有第二套邏輯。
- 每一次寫入都有稽核紀錄（`GET /audit`），含改前快照，改壞了可以照 `before` 退回。

---

## 1. 連線與回應格式

| 項目 | 內容 |
|---|---|
| 正式站 | `https://admin.meimai.com.tw/api/agent/...` |
| 本機測試 | 老闆給的本機後台網址 + `/api/agent/...` |
| 金鑰 | HTTP header `X-Agent-Key: mmk_…`（或 `Authorization: Bearer mmk_…`）。金鑰有 scope：`products`、`banners`、`cases`、`dealers`、`resources`、`settings`、`files`（上傳）、`publish`、`*`（全部）。`GET /me` 看自己有哪些。 |
| 格式 | 送 JSON（`Content-Type: application/json`），圖片上傳用 multipart。回應都是 JSON。 |
| 速率 | **每把金鑰每分鐘 120 次**。超過回 `429 {"message":"超過每分鐘 120 次上限，請稍後再試"}` → 等 20 秒再重試同一個請求（實測有效）。批次作業請自己節流（例如每次請求間隔 0.5 秒）。 |

**回應長這樣（實測）**

| 動作 | 成功回應 |
|---|---|
| 列表 `GET /{資源}/all?page=1&per_page=500` | `{"items":{"current_page":1,"data":[…],"per_page":500,"last_page":1,"total":N},"is_search":false}`；**`per_page` 最多 500**，要全部就帶 500 |
| 單筆 `GET /{資源}/{id}` | `{"item":{…}}` |
| 新增 `POST /{資源}` | `{"message":"新增成功","id":12,"item":{…}}`（圖片類另有 `saved_images`） |
| 修改 `PATCH /{資源}/{id}` | `{"message":"更新成功","saved_images":{"img":"https://…"},"warnings":[…]}`；**有 `warnings` 就代表圖沒存成功，要重做** |
| 刪除 `DELETE /{資源}/{id}` | `{"message":"刪除成功"}` |
| 切換 `PATCH /{id}/status`、`/{id}/top`、`/{id}/pinned`、`/{id}/home` | `{"message":"…成功"}`（每打一次就切換一次，所以先 GET 看現況再決定要不要打） |
| 排序 `PATCH /{資源}/all/sort` | body `{"items":[{"id":3,"sort":1},{"id":7,"sort":2}]}` → `{"message":"排序更新成功。"}` |

**錯誤**

| 狀態碼 | 意思 | 怎麼辦 |
|---|---|---|
| 401／403 | 金鑰錯或沒這個 scope | 回報老闆，不要猜 |
| 422 | 欄位驗證沒過，`message` 會寫清楚是哪個欄位（例：`img 的網址找不到檔案：…`、`縣市請給代碼 0～21 或名稱…`） | 照訊息修正後重送 |
| **400 `查無資料`** | id 不存在（**不是 404**，所有資源都這樣） | 當成「已刪除／沒有這筆」 |
| 429 | 每分鐘上限 | 等 20 秒重試 |
| 500 | 伺服器錯誤 | 把 `message` 原文回報老闆，**不要重試第三次** |

---

## 2. 每一件任務的標準流程（SOP，不要跳步）

```
① 看懂需求 → 確認是哪個資源、哪一筆（名稱→id）、要改哪些欄位
② GET 讀現況，把「改前的值」記下來（回報要寫前→後）
③ 有圖片 → 先 POST /upload（或直接在寫入時給圖），拿到站內網址
④ 寫入（POST／PATCH／DELETE／sort／status）
⑤ GET 讀回來核對：值真的變了？saved_images 有網址？沒有 warnings？
⑥ 打前台公開 API 或開前台頁面確認（網址對照見第 9 章）
⑦ 回報老闆：資源＋id、改前→改後、前台網址、不確定的事
```

**三件絕對不做**：①不自己決定移動、改名、刪除圖庫（`files/1/…`、`photos/1/…`）的資料夾或檔案（資料庫存的是網址字串，動了會全站斷圖，2026-09 發生過 799 筆）；老闆明確要求才做，做之前先 usage 回報，並依 3.1.1 的 update_references／force 規則；②不改公司名稱、地址、電話（要跟前台寫死的一致，先問老闆）；③不批次刪除——刪東西一次一筆，先 GET 確認是哪筆，並把 `item` 原文留在回報裡。

---

## 3. 圖片：上傳、規格、怎麼填

### 3.1 兩種給圖方式（擇一）

**A. 先上傳再填網址**（要指定資料夾、一次很多張時用）

```
POST /api/agent/upload   (multipart/form-data)
  file     = 檔案（單檔）     或  files[] = 多檔（最多 20 個）
  type     = files（預設，檔案庫 files/1）或 images（圖片庫 photos/1，CKEditor「插入圖片」那個）
  folder   = 該庫底下的相對路徑，可多層，沒有會自動建；不填放 Agent
  name     = 單檔時可指定檔名（不含副檔名）
→ 單檔 {"message":"上傳成功","name":"x.png","url":"https://admin…/storage/files/1/Banner/2026/x.png","path":"…","size":123}
→ 多檔 {"items":[{name,url,path,size},…],"errors":[]}
```
- 允許 jpg／jpeg／png／webp／gif／svg／pdf，單檔 20MB。不允許的類型回 422；`folder` 含 `..` 或 `\` 回 422。
- 同名檔自動變 `-2`、`-3`，不會覆蓋別人的圖。
- **上傳前先確認目的地**：老闆說「圖片庫／CKEditor／插入圖片／編輯器裡的圖」＝`type=images`（網址 `/storage/photos/1/…`）；說「檔案庫／下載檔／產品圖欄位」＝預設 files（網址 `/storage/files/1/…`）。有疑義就問一次，不要自己猜；上傳後檢查回傳 `url` 前綴與意圖一致，不一致要回報。`type=images` 只收 jpg／jpeg／png／webp／gif（不收 svg、pdf）。**不要再用瀏覽器 Dropzone／CDP 上傳**，API 現在兩個庫都能傳，快很多且有稽核紀錄。
- **回傳的 `url` 原封不動填進欄位**：不要自己改網域、不要 URL 編碼、不要截前段。
- 資料夾命名建議：`Banner/2026`、`ListBanner`、`Home`、`Products/2026`、`Frames/<車廠>`、`Cases/2026`、`Logo`、`SEO`、`Downloads`。

### 3.1.1 圖片庫／檔案庫管理（列表、預覽、下載、改名、搬移、縮放、裁剪、刪除）
`type=files`（檔案庫 files/1，預設）或 `type=images`（圖片庫 photos/1，CKEditor 插入圖片）；`path`＝該庫底下的相對路徑（不含 files/1、photos/1）。金鑰要有 `files:read`（讀）與 `files:write`（寫）。
```
GET    /api/agent/files/list?type=&folder=&sort=name|time|size|type&order=asc|desc&q=&page=&per_page=   列表（資料夾在前；有 url、thumb_url、size、modified）
GET    /api/agent/files/info?type=&path=        預覽：網址、縮圖、尺寸、大小、格式、哪些紀錄在用
GET    /api/agent/files/usage?type=&path=       這個檔案／資料夾在資料庫哪些紀錄還在用
GET    /api/agent/files/download?type=&path=    下載
POST   /api/agent/files/folder   {type,path}                       建資料夾
POST   /api/agent/files/rename   {type,path,new_name,update_references?}   改名（不能改副檔名）
POST   /api/agent/files/move     {type,path,to_folder,update_references?}  搬移
POST   /api/agent/files/resize   {type,path,width?,height?,keep_ratio?,allow_upscale?,save_as?,overwrite?}  縮放（預設等比、不放大、另存新檔）
POST   /api/agent/files/crop     {type,path,x,y,width,height,save_as?,overwrite?}  裁剪
DELETE /api/agent/files          {type,path,recursive?,force?}     刪除（移到垃圾桶，可由工程師還原）
```
- 畫面上的「縮圖顯示／列表顯示」只是顯示方式，API 的 list 兩種資料都給；「確認」是畫面選圖按鈕，API 不需要，直接把 `url` 填進欄位。
- **刪除、搬移、改名一律先老闆同意**。老闆沒明確說，不准自己動。做之前先 `usage`（或 `info`）看有沒有紀錄在用，把結果回報給老闆。
- 系統自己也會擋：還有紀錄在用就回 409 並列出是哪些紀錄。搬移／改名確定要做 → 加 `update_references=1`（系統會把資料庫裡的舊網址一併換成新網址）；刪除確定要做 → 加 `force=1`，前台對應的圖會斷，通常應先把那些紀錄換成別張圖。
- 縮放、裁剪預設另存新檔（檔名加 `-寬x高` 或 `-crop`），原檔不動；`overwrite=1` 才覆蓋，原檔備份在垃圾桶。
- 做完檢查回傳的 `url` 是否在預期的庫（`/storage/photos/1/` 或 `/storage/files/1/`）。

**B. 直接在寫入請求裡給圖**（最省事）：任何圖片欄位都可以放 ①`https://…` 外部圖片網址（伺服器自己下載）②`data:image/png;base64,…` ③multipart 直接帶檔（欄位名同圖片欄位）④已經在站內的網址。伺服器會存進 `files/1/AgentImport/年月/`，檔名用欄位名（`img.png`、`img-2.png`…）。

### 3.2 圖片規格總表

| 圖 | 資源.欄位 | 電腦版 | 手機版 | 製作重點 |
|---|---|---|---|---|
| 首頁輪播 | `banner.img`／`img_mobile` | **1920×1080（16:9）** | **1080×2160（1:2 直式）** | 文字放中間 60%；最下方約 190px 會蓋漸層＋車型查詢卡，**底部不放字**；手機沒傳會用電腦版（兩側被裁 35%） |
| 產品頁／各頁頂端 Banner | `list_banner.img`／`img_mobile` | **1920×480（4:1）** | **1080×608（16:9）** | 左半邊留白（文字疊左側）；文字用 `kicker／title／description` 欄位填，不要烙在圖上 |
| 首頁滿版區塊 | `home_section.img`／`img_mobile` | **2560×1440** | **1080×1920** | 主體置中；可另設 `bg_color` |
| 九類商品主圖 | `*.img` | 1200×1200 正方形 | 同左 | 白底或去背，主體佔 80%，300KB 內 |
| 安卓車框 | `car_frame.imgArr` | 橫式（建議 1200×900） | 同左 | 主圖白底或去背；完工照實拍；見 5.3 |
| 安裝案例 | `install_case.img` | 1600×1000（16:10） | 同左 | 實拍，主體置中 |
| 左上角標誌 | `website.logo_cobrand_dark`／`logo_cobrand_white` | 橫式約 1750×300 | 同左 | 透明背景；dark＝白底用（深色字）、white＝深色底用（白字） |
| 歌樂專區標誌 | `logo_clarion_dark`／`logo_clarion_white` | 橫式約 1260×330 | 同左 | 同上，**只用 Clarion 原廠 VIS 原檔** |
| 頁尾標誌 | `logo_footer` | 橫式約 1750×300 | 同左 | 白字透明背景 |
| 分頁小圖示 | `logo_favicon` | 512×512 | 同左 | PNG，四周留邊；只用這一張，不會自動縮成多尺寸 |
| 分享預覽圖 | `seo.og_image` | 1200×630 | 同左 | 四周各留 10% |
| 浮水印 | `watermark/{0-3}` | PNG 透明 20×10～4000×4000 | — | 只影響之後存檔的車框圖 |

---

## 4. 資源總表（名稱 → 前台哪裡 → 必填欄位 → 特殊規則）

| 資源 | scope | 前台顯示位置 | 必填 | 特殊規則（實測） |
|---|---|---|---|---|
| `car_media` 多媒體安卓機／車型專用機 | products | type 0 `/mm/me`、1 `/mm/oem`、2 `/clarion/gl`、3 `/clarion/oem`；詳情 `/multimediaDetail/{id}` | type,name,img,memo,content,is_top,status | `memo_in`（詳情摘要＝SEO description）選填；列表可 `?name=` 搜尋 |
| `car_head_unit` 車用主機 1/2DIN | products | `/headUnit`、`/headUnitDetail/{id}` | type(0=1DIN,1=2DIN),name,img,content,is_top,status | |
| `car_dashcam` 行車記錄器 | products | brand 0 `/mm/dashcam`、1 `/clarion/dashcam` | brand,name,img,content,is_top,status | |
| `car_camera` 鏡頭 | products | brand 0 `/mm/camera`、1 `/clarion/camera` | brand,name,img,content,is_top,status | |
| `car_audio_accessories` 汽車音響 | products | `/audioAccessories`、`/clarion/audioAccessoriesDetail/{id}` | type(0喇叭 1高音 2重低音 3擴大機 4DSP),name,img,content,is_top,status | |
| `car_headrest` 頭枕螢幕 | products | `/headrest` | name,img,content,is_top,status | 類別開關預設**關**，開了才有選單 |
| `car_portable` 可攜式 | products | `/portable` | 同上 | 同上 |
| `car_fitting` 車用配件 | products | `/fitting` | name,img,material,power,content,is_top,status | |
| `car_blind_spot` 盲點偵測 | products | `/safety`、`/safetyDetail/{id}` | name,img,content,is_top,status | `memo_in` 選填 |
| `car_blind_spot_format` 盲點適用車款 | products | `/safetyDetail/{id}` 的適用車款表 | car_brand_id,style,year,spc,status | 網址是 **`/car_blind_spot_format/{盲點商品id}/…`**（例 `POST /car_blind_spot_format/5`、`GET /car_blind_spot_format/5/all`） |
| `car_frame` 安卓車框 | products | `/carFrame`（車廠→車款→年份）、`/carFrameDetail/{id}`、首頁車型查詢 | car_brand_id,car_id,year_start,year_end,size,status | 圖片是 `imgArr`，見 5.3 |
| `car_brand` 車廠 | products | 車框／案例／盲點的下拉、首頁快搜 | name,status | 名稱格式「TOYOTA 豐田」 |
| `car` 車款 | products | 車框與案例的車款下拉 | car_brand_id,name,year_start,year_end,status | 車款必須屬於該車廠 |
| `recommend_product` 首頁精選 | products | 首頁「精選商品」 | product_type,product_id,status | `GET /recommend_product/options` 看可選類型；商品停用／刪除就不會顯示；`sort` 不填自動排最後 |
| `banner` 首頁輪播 | banners | 首頁頂端 | img,status | `url` 要是完整網址；`name` 後台辨識用 |
| `list_banner` 各頁頂端 Banner | banners | 各列表／總覽／品牌故事等頁 | — | kv 型：`PATCH /list_banner/{page_key}/{type_key}`；清單用 `GET /list_banner/all`（`multimedia` 的 type_key 是 0～3＝car_media 的 type；沒 type 的頁用 `default`）；顏色必須 `#RRGGBB` |
| `home_section` 首頁滿版區塊 | banners | 首頁三個區塊 | — | kv 型：`PATCH /home_section/{zone1|zone2|zone3}`，欄位 content(HTML)/img/img_mobile/bg_color |
| `install_case` 導入事例 | cases | `/cases`、`/cases/{id}`；`is_home=1` 最多 3 筆上首頁 | category(0-8),name,img,status,car_brand_id,car_id | `installed_at` 日期 `YYYY-MM-DD`；`dealer` 是文字不是 id；`/{id}/pinned`、`/{id}/home` 切換 |
| `dealer` 經銷據點 | dealers | `/partner` | name,county,address,tel,status | **`county` 可以直接給縣市名稱（桃園市）或代碼 0～21**，GET 回來是代碼；錯的名稱回 422 並列出可用縣市 |
| `resource_category` 資源分類 | resources | `/download` 的分類 | name,status | |
| `resource` 資源檔案 | resources | `/download` | resource_category_id,name,url,status | `url` 要完整網址；PDF 可先 `/upload` 再把 url 填進來 |
| `website` 網站設定 | settings | 頁尾、全站色彩、標誌、類別開關、SEO／GEO | — | 見第 6 章 |
| `qa`、`about` | settings | `/qa`、`/about` 內文 | — | `content` 是整段 HTML，整份取代 |
| `watermark` 浮水印 | settings | 不直接顯示；車框存檔時蓋上 | — | `GET /watermark/all`；換圖 `POST /watermark/{0-3}`（multipart file）；還原 `DELETE /watermark/{key}` |

通用欄位：`status` 1 顯示／0 隱藏；`is_top` 1 置頂；`sort` **數字小的在前**（新資料自動排最後，不用自己算）；`content` 是 HTML。

---

## 5. 逐步流程（照做，每一條都實測過）

### 5.1 新增一個商品（九類通用）
1. `GET /car_media/all?per_page=500&name=GL-500` 確認沒有同名（避免重複建）。
2. 上傳主圖：`POST /upload` folder=`Products/2026` → 拿 `url`。
3. `POST /car_media` body：
   ```json
   {"type":2,"name":"GL-500","img":"<upload url>","memo":"列表一句話","memo_in":"詳情摘要（給 Google 的 description）",
    "size":"10","hard_drive":"64G","ram":"4G","resolution":"1280x720","price":"25800",
    "content":"<h3>GL-500</h3><p>規格…</p>","is_top":0,"status":1}
   ```
   回 `{"message":"新增成功","id":7,…}`。缺必填會 422 並指出欄位。
4. `GET /car_media/7` 核對；開 `https://clarion.meimai.com.tw/multimediaDetail/7` 與列表頁確認。
5. 要放首頁精選：`POST /recommend_product {"product_type":"car_media","product_id":7,"status":1}`。

### 5.2 修改商品（只送要改的欄位）
- `PATCH /car_media/7 {"price":"19800"}` → 其他欄位（名稱、圖、內文）**全部保留**（實測）。
- 換圖：`PATCH /car_media/7 {"img":"<新 url 或 data:image/…base64>"}`，回應 `saved_images.img` 要有網址。
- 停用／啟用：先 `GET` 看 `status`，需要才 `PATCH /car_media/7/status`（每打一次切一次）。置頂同理 `/{id}/top`。
- 刪除：`DELETE /car_media/7`；之後 `GET /car_media/7` 會回 **400 查無資料**＝已刪。刪前先把 `item` 內容留在回報裡。

### 5.3 安卓車框（圖片最特殊，照這個寫）
1. 先確認車廠、車款 id：`GET /car_brand/all?per_page=500`、`GET /car/all?per_page=500`（車款要屬於該車廠）。沒有就先 `POST /car_brand {"name":"TOYOTA 豐田","status":1}`、`POST /car {"car_brand_id":1,"name":"Corolla Altis","year_start":2019,"year_end":2023,"status":1}`。
2. 上傳圖到 `Frames/<車廠>`。
3. 新增：
   ```json
   {"car_brand_id":1,"car_id":1,"year_start":2019,"year_end":2023,"size":"9","name":"黑色面板","content":"<p>…</p>","status":1,
    "imgArr":[["主圖1","主圖2",""],["配件圖","",""],["完工照","",""],["概觀","",""]]}
   ```
   `imgArr[0]`=車框主圖（第 1 張必備）、`[1]`=配件、`[2]`=實際安裝完工照、`[3]`=概觀；每組最多 3 張。
4. **修改某一格**：沒送的格子、或送 `null` 的格子＝保留原圖；送 `""`＝刪掉那一格。
   - 只換主圖第 3 張：`PATCH /car_frame/12 {"imgArr":[[null,null,"<新 url>"]]}`（實測三張都保留、第 3 張換新）。
   - **不要**用 `""` 當佔位，那會把前面的圖清掉。
   - 刪單張：`PATCH /car_frame/12/img {"type":"img1","index":0}`（img=主圖、img1=配件、img2=完工照、img3=概觀）。
5. 網址對不到檔案會回 **422 並指出是哪一格**（`imgArr.0.1 的網址找不到檔案…`），不會再默默清空。
6. 列表 `GET /car_frame/all` 的 `img1/img2/img3` 只回張數；要看網址用 `GET /car_frame/{id}`。
7. 浮水印選填 `watermarkArr`（同結構）：0 不加、1 左上、2 左下、3 右上、4 右下、5 置中、-1 全圖。

### 5.4 排序（任何列表）
1. `GET /{資源}/all?per_page=500` 取得目前順序（列表已經是「置頂在前、再依 sort 小→大」）。
2. 重新編號後整份送：`PATCH /{資源}/all/sort {"items":[{"id":5,"sort":1},{"id":3,"sort":2},…]}`（**每一筆都要在裡面**，數字小的在前；置頂與一般是兩個群組，各自排）。
3. 再 GET 一次確認。資源下載 `resource` 是同一分類內排序。

### 5.5 首頁輪播 Banner
1. 上傳電腦版 1920×1080 與手機版 1080×2160 到 `Banner/2026`。
2. `POST /banner {"name":"2026 秋季 GL","url":"https://clarion.meimai.com.tw/clarion/gl","img":"<電腦版 url>","img_mobile":"<手機版 url>","status":1}`。
3. 順序用 5.4；關掉用 `/{id}/status`。前台首頁重新整理即可看到。

### 5.6 各頁頂端 Banner（list_banner）
1. `GET /list_banner/all` 找 `page_key`／`type_key`（例：GL 列表＝`multimedia`／`2`；關於我們＝`about`／`default`；產品分類頁＝`productLanding`／`landingAudio` 等）。
2. `PATCH /list_banner/multimedia/2 {"img":"<1920×480>","img_mobile":"<1080×608>","kicker":"CLARION","title":"GL 系列 多媒體安卓機","description":"…","title_color":"#FFFFFF"}`。顏色格式錯會 422。
3. 沒送的欄位沿用；文字留空＝用頁面原本文字。

### 5.7 首頁滿版區塊
`GET /home_section/all` → `PATCH /home_section/zone1 {"content":"<h2>…</h2><p>…</p>","img":"<2560×1440>","img_mobile":"<1080×1920>","bg_color":"#0D1016"}`。

### 5.8 導入事例（安裝案例）
`POST /install_case {"category":0,"name":"Altis 升級 GL-700","img":"<1600×1000>","status":1,"installed_at":"2026-09-20","is_pinned":1,"is_home":1,"car_brand_id":1,"car_id":1,"product":"GL-700 Ultra","dealer":"桃園某某音響","need":"想換大螢幕","work":"拆原廠主機、安裝車框與主機"}`。
首頁最多顯示 3 筆 `is_home=1`；用 `/{id}/home`、`/{id}/pinned` 切換。

### 5.9 經銷據點
`POST /dealer {"name":"桃園某某汽車音響","county":"桃園市","address":"…","tel":"03-…","status":1}`。縣市給名稱即可（臺／台都認）。

### 5.10 資源下載
1. `GET /resource_category/all` 找分類 id（沒有就 `POST /resource_category {"name":"產品型錄","memo":"","status":1}`）。
2. PDF 先 `POST /upload`（folder=`Downloads`）拿 url。
3. `POST /resource {"resource_category_id":1,"name":"GL 系列型錄","url":"<pdf url>","status":1}`。

### 5.11 常見問題／品牌故事內文
`PATCH /qa {"content":"<h3>Q…</h3><p>A…</p>"}`、`PATCH /about {"content":"<p>…</p>"}`——整段 HTML 整份取代，所以先 `GET /qa/all` 把原文拿回來改，不要只送一段。

---

## 6. 網站設定 `website`（標誌、類別開關、SEO／GEO 全在這裡）

`GET /website/all` → `item.content` 長這樣：

```json
{"copyright":"…","address":"…","tel":"…","email":"…","facebook":"…","instagram":"","youtube":"",
 "theme_dark":"#0D1016","theme_accent":"#007ABE","theme_bg2":"…","theme_footer":"…",
 "logo_favicon":"<url>","logo_cobrand_dark":"<url>","logo_cobrand_white":"<url>","logo_clarion_dark":"<url>","logo_clarion_white":"<url>","logo_footer":"<url>",
 "categories":{"clarion":[{"key":"gl","name":"GL 安卓機","on":1},…],"mm":[{"key":"frame","name":"","on":1},…]},
 "seo":{"company_zh":"…","company_en":"…","phone_intl":"+886-3-2170098","addr_region":"桃園市","addr_locality":"桃園區","addr_street":"國信街35號",
        "og_image":"<url>","gsc":"","bing":"","flags":{"org":1,"product":1,"ai_bots":1},
        "pages":{"/":{"title":"…","description":"…"},"/clarion/gl":{…}},
        "faq":[{"q":"…","a":"…"}]}}
```

**寫入規則（部署第 129 項＋10/01 修正後）**
- `PATCH /website {"content":{…}}`：**頂層沒送的鍵沿用**；`seo` 再往下合併一層（只送 `seo.company_en` 不會清掉其他）；`seo.pages` 以網址為單位合併；`seo.flags` 逐個合併；`seo.faq` 有送就整份取代；`categories` 只送 `clarion` 不會清掉 `mm`。
- **保守做法永遠正確**：先 GET 整份、只改自己的鍵、整份送回。
- 刪掉某頁的 SEO 覆蓋：`{"content":{"seo":{"pages":{"/about":null}}}}`。
- `categories` 每個品牌的清單是整份取代：**順序＝清單順序**，缺的類別前台用預設補在後面；只接受這些 key——clarion：`gl,oem,audio,camera,din,dvr,headrest,portable`；mm：`android,oem,frame,safety,dvr,camera,fitting`。打錯的 key 會被**靜默丟掉**，所以改完一定 GET 回來看。
- `logo_*` 只接受上面 6 個鍵，其他（例如打錯的 `logo_header`）靜默丟掉；要還原預設就送 `""`。
- `seo.pages` 的 key 是前台路徑（`/`、`/clarion/gl`、`/about`…，共 24 個可覆蓋頁見後台 SEO 頁清單）；`title` 建議 10～30 字、`description` 60～110 字。
- `flags.ai_bots` 設 0 → 前台 `/robots.txt` 會擋 GPTBot／ClaudeBot／PerplexityBot 等 AI 爬蟲。
- 這一區全部實測：改完前台重新整理即生效（選單、404 擋頁、標誌、favicon、`<title>`、`description`、`og:image`、JSON-LD、FAQ、robots.txt）。

### 6.1 流程：關閉／開啟一個類別、改名、調順序
1. `GET /website/all` 取 `categories`。
2. 改 `on`（1 顯示 0 隱藏）、`name`（留空＝預設名）、或調整陣列順序。
3. `PATCH /website {"content":{"categories":{"clarion":[…整份 clarion 清單…]}}}`。
4. 驗證：前台選單；關掉的類別直接輸入網址會是 404。
   **注意**：關掉美邁的 `frame`（安卓車框）會讓首頁「車型查詢」連結失效，先問老闆。

### 6.2 流程：換標誌／小圖示
上傳到 `Logo` → `PATCH /website {"content":{"logo_clarion_dark":"<url>","logo_clarion_white":"<url>"}}` → 前台 Clarion 專區左上角立即換；favicon 同理 `logo_favicon`。LOGO 一律用原廠 VIS 原檔。

### 6.3 流程：某一頁的 Google 標題與說明
`PATCH /website {"content":{"seo":{"pages":{"/clarion/gl":{"title":"GL 系列安卓機","description":"…"}}}}}` → `curl -s https://clarion.meimai.com.tw/clarion/gl | grep '<title>'` 確認（前台會自動接網站後綴；首頁 `/` 不接後綴）。

### 6.4 流程：公司資料（GEO）、FAQ、分享圖、驗證碼
`seo.company_zh/company_en/phone_intl/addr_*` 影響 JSON-LD Organization；`seo.faq` 顯示在 `/about` 並產生 FAQPage 結構化資料（只套中文版）；`seo.og_image` 全站預設分享圖；`seo.gsc`／`seo.bing` 輸出成 `<meta name="google-site-verification">`／`msvalidate.01`。

---

## 7. 稽核與退回
- `GET /audit?page=1&resource=car_media&record_id=12`：每筆寫入的時間、方法、資源、改前快照 `before`。
- 改壞了：拿 `before` 的欄位用 PATCH 送回去（圖片欄位照 `before` 的網址原樣送）。

---

## 8. 常見錯誤對照（實測）

| 症狀 | 原因 | 解法 |
|---|---|---|
| `422 img 的網址找不到檔案` | 自己改了網址、URL 編碼、或檔案不存在 | 用 upload 回傳的 url 原樣送 |
| 車框 PATCH 後前面的圖不見 | 用 `""` 當佔位 | 用 `null` 佔位或不送 |
| `429` | 一分鐘超過 120 次 | 等 20 秒重試，批次要節流 |
| `400 查無資料` | id 不存在 | 先 GET 列表找 id |
| `422 縣市請給代碼…` | 縣市名稱打錯 | 照訊息列出的縣市名稱 |
| `422 … 顏色` | 顏色不是 `#RRGGBB` | 改成 6 碼十六進位 |
| PATCH 回 200 但有 `warnings` | 圖沒存成功 | 重新上傳、重送 |
| 類別改了前台沒變 | key 打錯被丟掉／前台還是舊程式 | GET 回來核對；確認工程師已部署新版前台 |
| 盲點適用車款 405 | 網址少了盲點商品 id | `/car_blind_spot_format/{盲點id}/all` |

---

## 9. 前台驗證網址（改完去看）

| 改了什麼 | 看哪裡 |
|---|---|
| 首頁輪播、精選、案例、滿版區塊 | `https://clarion.meimai.com.tw/` |
| car_media type 0/1/2/3 | `/mm/me`、`/mm/oem`、`/clarion/gl`、`/clarion/oem`；詳情 `/multimediaDetail/{id}` |
| 車用主機／音響／鏡頭／行車記錄器／頭枕／可攜／配件／盲點 | `/headUnit`、`/audioAccessories`、`/clarion/camera`、`/mm/camera`、`/clarion/dashcam`、`/mm/dashcam`、`/headrest`、`/portable`、`/fitting`、`/safety` |
| 車框 | `/carFrame`、`/carFrameDetail/{id}` |
| 案例、經銷、下載、FAQ、關於 | `/cases`、`/partner`、`/download`、`/qa`、`/about` |
| 類別開關／標誌／favicon／SEO | 任一頁重新整理；標題用「檢視原始碼」看 `<title>`；`/robots.txt`、`/sitemap.xml` |
| 公開 API（不用金鑰） | `/api/website`、`/api/banner`、`/api/recommend_products`、`/api/headrest`… 直接 GET 看資料 |

前台是**即時讀取**，不需要「重新產生」；`POST /publish` 只是保險，會回 `needed:false`。

---

## 10. 回報格式（每次任務結束）

```
【做了什麼】car_media #7 GL-500：price 25800 → 19800；img 換新（saved_images.img = https://…）
【讀回核對】GET /car_media/7 ✔ price=19800、img 有值、無 warnings
【前台】https://clarion.meimai.com.tw/multimediaDetail/7 ✔ 已顯示新價格
【未做／待老闆決定】首頁精選要不要一起加？
```

---

## 11. 這次（2026-10-01）新增或修正、Hermes 要知道的差異

1. 新增三個後台功能，全部可用 Agent API 改：**產品類別開關**（`categories`）、**全站標誌圖片**（`logo_*`）、**SEO／GEO**（`seo`）。前台需工程師部署新版程式後生效。
2. 全部列表改成「**排序數字小的在前**」，新資料自動排最後；`per_page` 上限 500。
3. `website` PATCH 會合併（含 `seo` 子層與 `categories` 品牌層）；`seo.pages` 永遠是物件 `{}`。
4. 車框 `imgArr` 可用 `null` 佔位保留原圖。
5. `dealer.county` 可給縣市名稱。
6. `car_media`、`car_blind_spot` 的 `memo_in` 選填（以前不送會 500）。
7. 前台修掉每頁的 hydration 警告（FontAwesome SSR）與首頁不吃 SEO 覆蓋的問題。
8. 刪除後 GET 回 `400 查無資料`（不是 404）。

## 12. 排序號碼怎麼編（10/01 晚間補）

- 號碼＝這一筆在清單裡的位置：**第 1 頁 1～500、第 2 頁 501～1000**，以此類推（每頁 500 筆；目前所有可排序清單都不到 500 筆，所以就是 1～N）。
- 置頂那一組排前面、拿小號碼；一般組接著編。經銷據點／Banner／資源分類是「啟用在前、停用在後」。
- 在後台拖曳 ☰ 或按 ↑↓，**整頁會自動重新編號**，不會再出現 0,0,0,1,1,1 這種重複號碼；搜尋中（畫面上只有一部分資料）才只動同一組、沿用原本號碼。
- Hermes 用 `PATCH /{資源}/all/sort` 時也照這個規則：整份清單、`sort` 從 1 開始連號、置頂在前。
- 舊資料的重複號碼由 migration `2026_10_02_000006` 一次重編（顯示順序不變）。
