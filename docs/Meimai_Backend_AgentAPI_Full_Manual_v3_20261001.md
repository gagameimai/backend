# 美邁官網後台與 Agent API 完整操作手冊 v3（給 Hermes）

> **v3（2026-10-01 整理）**：在 2026-09-30 版之上，加入本機新增的後台功能（產品類別開關、全站標誌圖片、SEO／GEO 設定、產品頁 Banner 文字與顏色、首頁區塊底色、全站色彩、資源只貼連結、排序統一）。新內容集中在**第 14～18 章**（含流程、圖片尺寸、前後台連結總表、部署檢查）；第 1～13 章中與新規則不同的地方已就地更正並標示「v3 更正」。**第 14.0 章先確認新功能是否已部署再寫入。**

版本：2026-10-01（後端修正已部署版）　依據：本機 backend（Laravel 8）與 frontend-v2（Nuxt 3）程式碼逐檔閱讀。
每一條都是照程式寫的；程式裡看不出來、需要在正式站驗證的地方，會標「【待驗證】」。

> **Hermes 開工順序（每次都照這個，不要重新摸索）**
> 1. `GET /api/agent/me` 確認金鑰與權限。
> 2. `GET /api/agent/schema` 讀「欄位、驗證規則、白話用途（docs）、圖片欄位（image_fields）」。
> 3. 照本手冊第 9 章的標準作業流程操作。
> 4. 寫入後一定讀回驗證（第 10 章），回報時附「讀回的資料」而不是只說成功。
> 5. 改完資料，前台要重新產生才會更新（第 1 章）。


## 0. 2026-10-01 部署後：舊版 vs 新版 問題集中表（先看這章）

後端工程師已於 2026-10-01 合併並部署下列修正（依老闆告知；Hermes 開工時仍要照 0.2 做一次「版本確認」，確認正式站真的是新版再用新行為）。

### 0.1 舊版有什麼問題 → 新版怎麼處理 → Hermes 要改掉的習慣

| # | 舊版問題（9/30 之前） | 新版行為（2026-10-01 部署） | Hermes 要改的做法 |
|---|---|---|---|
| 1 | 車框圖片網址被默默清空：網域或 `%20` 編碼對不上、沒送的 `imgArr` 格子都被清成空字串 | 自動修正網域與編碼；沒送的格子保留；對不到檔案回 422 並指出哪一格 | PATCH 車框只送要改的格子；看到 422 就照訊息修網址，不要再「整份重送」 |
| 2 | 所有圖片欄位只收「以 APP_URL/storage 開頭且沒編碼」的站內網址 | 收站內網址、外部 `https://` 圖片、`data:image/...;base64`、multipart 檔案；外部圖自動下載存到 `files/1/AgentImport/年月/` | 可以直接把生成好的圖網址填進 `img`，不必先 upload；要指定資料夾才用 `POST /api/agent/upload` |
| 3 | `PATCH /{id}` 沒送的欄位被清空，所以必須先 GET 再整份送回 | 伺服器先讀現有資料再合併，沒送的欄位沿用 | 只送要改的欄位 |
| 4 | `list_banner`／`home_section` 送 `img` 會把 `img_mobile` 清成 null（反之亦然） | 合併，只改送的那一張 | 同上 |
| 5 | `POST` 新增成功只回「新增成功」，不知道新 id | 回 `{"message":"新增成功","id":N,"item":{…}}` | 直接用回傳的 `id`，不要再用 `GET all?name=` 猜 |
| 6 | `PATCH …/{id}/status`（top、pinned、home 同）是「切換」，body 送什麼都沒用，重試一次就切回去 | 帶 body（例 `{"status":1}`）＝直接設定；已經是那個值回 `{"unchanged":true}`；不帶 body 仍是切換 | 一律帶 body 設定，不要用切換 |
| 7 | 新增／更新不收 `sort`，只能用 `PATCH …/all/sort` | POST／PATCH 可帶 `sort`（`install_case`、`recommend_product` 除外，維持原邏輯） | 單筆排序直接帶 `sort`；整批重排才用 `all/sort` |
| 8 | 寫入後不知道圖片有沒有真的存進去 | 回應附 `saved_images`；存進去是空的附 `warnings` | 看到 `warnings` 就是沒成功，要重做並回報 |
| 9 | 欄位意義看不懂，每次重新摸索 | `GET /api/agent/schema` 每個資源有 `docs`（後台叫什麼、顯示在前台哪裡、每個欄位做什麼）、`image_fields`、頂層 `image_input`、`common_fields` | 不確定就讀 schema 的 docs，不要猜 |
| 10 | 後台改完前台不會更新，也沒辦法觸發 | `POST /api/agent/publish`（需 `publish` 權限；工程師未設定觸發網址時回 501） | 寫完資料呼叫 publish；回 501 就回報「後台已改、前台待工程師重新產生」 |
| 11 | 導入事例首頁上限 3 筆只在 `/home` 端點檢查，POST／PATCH 直接設 `is_home=1` 會繞過 | create／update 也檢查，超過回 400「首頁最多只能放 3 筆」 | 先把舊的一筆 `is_home=0` 再設新的 |
| 12 | `car_media`／`car_fitting`／`car_blind_spot` 的 `all/sort` 寫到 `car_frame` 資料表 | 各寫正確資料表（盲點資料表沒有 `sort` 欄位，該端點仍不能用） | 盲點偵測不要用排序端點 |
| 13 | `config/list_banner` 標籤錯：fitting 標「影像・安全」、safety 標「盲點偵測」 | 改為 車用配件（/fitting）、影像・安全（/safety） | 照 `GET /api/agent/list_banner/all` 回的名稱對頁面 |
| 14 | 浮水印設定沒有 API | 新增 `watermark` 資源（`GET all`、`POST {key}`、`DELETE {key}`） | 見 7.20 |
| 15 | `car_blind_spot` 的 schema 列了 `PATCH …/{id}/spc`，但後端沒有這個方法，打了會 500 | 已從 schema 移除 | 不要打 spc |
| 16 | 前台商品列表置頂反向（`is_top` 小→大，置頂反而在最後；9 支 API） | 置頂在前 | 設 `is_top=1` 後，前台列表（重新產生後）會排最前 |
| 17 | 停用商品的詳情頁仍打得開（前台詳情 API 不檢查 status） | 10 支詳情 API 加 `status=1`，停用回 404 | 下架用 `status=0` 即可，不必刪除 |
| 18 | 刪除車廠／車款／資源分類／商品不檢查關聯，留下孤兒資料 | 還被引用就回 400「仍被 N 筆 X 使用中，無法刪除」；盲點商品刪除時子資料「適用車款」一起刪 | 收到 400 就先處理被引用的資料，或改停用 |
| 19 | 車框刪單張圖（`PATCH …/{id}/img`）會實體刪檔，共用同一張圖的資料變壞圖 | 只清空該格網址，檔案留在檔案管理員 | 可以放心清格子；要清檔案另外用檔案管理員 |
| 21 | `website.content` 整份取代，少帶一個鍵就被清掉 | 本機已改成沒送的鍵沿用（第 129 項，**待再部署**） | 部署前照 7.19 整份送；部署後只送要改的鍵 |
| 20 | 首頁精選商品會顯示已停用的商品 | 前台精選只顯示 `status=1` 的商品；後台列表照常 | 下架商品不必另外動精選 |

### 0.2 版本確認（每次開工第一件事）

```bash
curl -s -H "X-Agent-Key: $MEIMAI_AGENT_KEY" https://admin.meimai.com.tw/api/agent/schema > /tmp/schema.json
```
看回應裡有沒有這三樣，**三樣都有＝新版**：
1. `resources.watermark`（浮水印資源）
2. `resources.car_media.docs`（白話說明）
3. 頂層 `image_input`

再做一個無害測試：對任一已啟用的商品 `PATCH /api/agent/car_dashcam/{id}/status` 帶 `{"status":1}`，新版會回 `{"unchanged":true}`，**不會把它切成停用**。
如果三樣缺任何一樣，或 status 測試把商品切成了停用：**正式站還是舊版**，請立刻把它切回來、停止使用新行為、照第 11 章舊版注意事項操作，並回報老闆。

### 0.3 路徑與網址總表（不要記錯）

| 用途 | 路徑／網址 |
|---|---|
| 後台畫面 | `https://admin.meimai.com.tw/`（登入後 `/main`） |
| Agent API | `https://admin.meimai.com.tw/api/agent/...`，header `X-Agent-Key: $MEIMAI_AGENT_KEY`（金鑰只從環境變數讀） |
| 前台公開 API（驗證訪客看到什麼） | `https://admin.meimai.com.tw/api/...`，不需金鑰 |
| 前台網站 | `https://clarion.meimai.com.tw/...`（靜態網站，後台改完要重新產生） |
| 圖片對外網址 | `https://admin.meimai.com.tw/storage/files/1/<資料夾>/<檔名>` |
| 圖片在伺服器上 | `storage/app/public/files/1/...`（＝後台「檔案管理員」看到的那棵樹） |
| Agent 自動存圖 | `files/1/AgentImport/YYYYMM/`（填外部網址或 base64 時） |
| `upload` 預設資料夾 | `files/1/Agent/`（沒帶 `folder` 時） |
| 浮水印檔 | `storage/app/public/watermark-config/`（備份在 `watermark-config/backup/`） |
| 老闆本機程式 | `D:\MeimaiCode(不能刪)\backend`（後端）、`D:\MeimaiCode(不能刪)\frontend-v2`（前台） |
| 工程師看的草稿與交辦 | `D:\MeimaiCode(不能刪)\260706 claude改版相關產出\`（`工程師必看_本次更改紀錄_20260906.txt`、`工程師交付_後端API修正_20260930\`） |
| 這份手冊 | `backend/docs/美邁後台與AgentAPI完整操作手冊_20260930.md`（英文檔名 `Meimai_Backend_AgentAPI_Full_Manual_20260930.md`） |
| 網站地圖與前後台分工 | `backend/docs/網站地圖與前後台對接對照_20260930.md` |

---

## 1. 系統全貌

| 層 | 是什麼 | 網址 |
|---|---|---|
| 後台（Laravel） | 管理畫面＋給前台用的 API＋Agent API | `admin.meimai.com.tw` |
| 前台（Nuxt 3 靜態網站） | 訪客看到的網站 | `clarion.meimai.com.tw` |
| Agent API | 給 Hermes 用的快速通道，不用開後台畫面 | `/api/agent/…` |

- **前台資料何時生效（v3 更正，2026-10-01 實測）**：前台是伺服器即時讀取，**後台寫入後前台重新整理就會變**，不需要重新產生。舊版（靜態產生）的說法：建置（`npm run generate`）時才抓資料，後台改了要重新產生並部署前台——若日後前台改回靜態產生，仍照此辦理。
- Agent API 沒有第二套邏輯：它直接呼叫「後台畫面本來就在用的同一支控制器」，驗證規則、必填欄位、限制和人手按按鈕一樣。
- 前台讀的是另一組公開 API（`GET /api/multimedia`、`/api/carframe`…），只回 `status=1`（啟用）的資料。**後台資料和前台看到的資料不是同一個回應**，驗證要分兩邊看。

### 1.1 後台選單 ↔ 手冊章節 ↔ Agent API 對照（全部項目）

後台左側選單每一項都對得到 API；「主頁」只是儀表板，沒有資料可讀寫。

| 後台選單 | 資源名稱（API 路徑用） | 手冊章節 | 主要端點 |
|---|---|---|---|
| 系統設定 → 常見問題 | `qa` | 7.19 | GET all／PATCH |
| 系統設定 → 關於我們 | `about` | 7.19 | GET all／PATCH |
| 系統設定 → 網站基本設定 | `website` | 7.19 | GET all／PATCH |
| 系統設定 → SEO／GEO 設定（v3 新增） | `website`（`content.seo`） | 14.3 | 同 website：GET all／PATCH（整份 content） |
| 產品管理 → 產品類別開關（v3 新增） | `website`（`content.categories`） | 14.1 | 同 website：GET all／PATCH（整份 content） |
| 系統設定 → 網站基本設定 → 全站標誌圖片／全站底色（v3 新增） | `website`（`content.logo_*`、`content.theme_*`） | 14.2、14.6 | 同 website |
| 系統設定 → 浮水印設定 | `watermark` | 7.20 | GET all／POST {key}／DELETE {key} |
| Banner管理 | `banner` | 7.15 | 標準 CRUD＋status＋all/sort |
| 首頁精選商品 | `recommend_product` | 7.14 | 標準 CRUD＋status＋all/sort＋GET options |
| 安裝案例（導入事例） | `install_case` | 7.13 | 標準 CRUD＋status／pinned／home＋all/sort |
| 產品頁 Banner 管理 | `list_banner` | 7.16 | GET all／PATCH {page_key}/{type_key} |
| 首頁滿版區塊管理 | `home_section` | 7.17 | GET all／PATCH {section_key} |
| 資源下載管理 → 分類管理 | `resource_category` | 7.18 | 標準 CRUD＋status＋all/sort |
| 資源下載管理 → 檔案管理 | `resource` | 7.18 | 標準 CRUD＋status＋all/sort |
| 經銷據點管理 | `dealer` | 7.12 | 標準 CRUD＋status＋all/sort |
| 產品管理 → 汽車品牌 | `car_brand` | 7.11 | 標準 CRUD＋status＋all/sort |
| 產品管理 → 汽車車款 | `car` | 7.11 | 標準 CRUD＋status＋all/sort |
| 產品管理 → 安卓車框 | `car_frame` | 7.10 | 標準 CRUD＋status＋img（刪單張）＋all/sort |
| 產品管理 → 盲點偵測 | `car_blind_spot`（適用車款 `car_blind_spot_format`） | 7.8、7.9 | 標準 CRUD＋status／top |
| 產品管理 → 車用配件 | `car_fitting` | 7.7 | 標準 CRUD＋status／top＋all/sort |
| 產品管理 → 多媒體機 | `car_media` | 7.1 | 標準 CRUD＋status／top＋all/sort |
| 產品管理 → 行車記錄器 | `car_dashcam` | 7.3 | 標準 CRUD＋status／top＋all/sort |
| 產品管理 → 鏡頭 | `car_camera` | 7.4 | 標準 CRUD＋status／top＋all/sort |
| 產品管理 → 頭枕螢幕 | `car_headrest` | 7.6 | 標準 CRUD＋status／top＋all/sort |
| 產品管理 → 可攜式 | `car_portable` | 7.6 | 標準 CRUD＋status／top＋all/sort |
| 產品管理 → 汽車音響 | `car_audio_accessories` | 7.5 | 標準 CRUD＋status／top＋all/sort |
| 產品管理 → 車用主機 1/2DIN | `car_head_unit` | 7.2 | 標準 CRUD＋status／top＋all/sort |
| （選單沒有）檔案管理員 | `upload` | 4.2 | POST /api/agent/upload |
| （選單沒有）重新產生前台 | `publish` | 13 | POST /api/agent/publish |

「標準 CRUD」＝`POST /`（新增）、`GET /all`（列表）、`GET /{id}`（單筆）、`PATCH /{id}`（編輯）、`DELETE /{id}`（刪除）；`status`＝畫面上的「啟用」開關，`top`＝「置頂」；細節見第 3 章。

### 資料關聯地圖

```
car_brand（車廠）──< car（車款）──< car_frame（安卓車框，含 4 組圖片）
     │                    │
     │                    └── install_case（導入事例，用 car_brand_id＋car_id）
     └──< car_blind_spot_format（盲點偵測適用車款，掛在 car_blind_spot 商品底下）

car_media／car_head_unit／car_dashcam／car_camera／car_audio_accessories／
car_headrest／car_portable／car_fitting／car_blind_spot（九類商品）
     └── recommend_product（首頁精選：product_type＋product_id 指到上面任一類）

banner（首頁輪播）　list_banner（各頁頂端橫幅，用 page_key＋type_key）
home_section（首頁三個滿版區塊 zone1/2/3）
dealer（經銷據點）　resource_category ──< resource（資源下載）
website／qa／about（單一設定，存在 setting 表，用 type 區分）
```

---

## 2. 認證與權限

- 每次請求帶 header：`X-Agent-Key: mmk_xxxx`（或 `Authorization: Bearer mmk_xxxx`）。**金鑰不要貼在任何訊息、記錄、截圖裡**。
- 權限群組（scope）：`products`、`banners`、`cases`、`dealers`、`resources`、`settings`、`files`。`GET` 需要 `群組:read`，`POST／PATCH／DELETE` 需要 `群組:write`；`*` 是全部權限。
- 每把金鑰每分鐘最多 120 次。超過回 429。
- 每次寫入都會記稽核紀錄（payload＋改前快照＋回應碼＋IP）：`GET /api/agent/audit?page=1&resource=car_media&record_id=12`。
- 系統一律回 JSON（Agent 請求強制 `Accept: application/json`）。

| 群組 | 管到哪些資源 |
|---|---|
| products | car_media、car_head_unit、car_dashcam、car_camera、car_audio_accessories、car_headrest、car_portable、car_fitting、car_blind_spot、car_blind_spot_format、car_frame、car_brand、car、recommend_product |
| banners | banner、list_banner、home_section |
| cases | install_case |
| dealers | dealer |
| resources | resource_category、resource |
| settings | website、qa、about |
| files | upload（只有 write） |

---

## 3. 讀寫通則（所有資源共通，務必先懂）

### 3.1 端點長相（標準型資源）

| 動作 | 方法與路徑 | 說明 |
|---|---|---|
| 列表 | `GET /api/agent/{資源}/all?page=1` | 分頁。回 `{"items": {"data":[…], "current_page", "last_page", "per_page", "total"}, …}`；有些資源另外帶 `brands`、`cars` 下拉資料 |
| 單筆 | `GET /api/agent/{資源}/{id}` | 回 `{"item": {…}}`；查無回 400 `{"message":"查無資料"}` |
| 新增 | `POST /api/agent/{資源}` | body 為 JSON 欄位；成功回 `{"message":"新增成功"}`，**不會回新資料的 id**，要再用列表／篩選找回來 |
| 更新 | `PATCH /api/agent/{資源}/{id}` | 見 3.3 |
| 刪除 | `DELETE /api/agent/{資源}/{id}` | **硬刪除，無法復原**（沒有軟刪除、沒有回收桶）；只有稽核紀錄的 `before` 可照著重建。2026-09-30 起：車廠／車款／資源分類／商品**還被其他資料引用時會回 400 並說明**（見 13 章） |
| 切換啟用 | `PATCH /api/agent/{資源}/{id}/status` | 不帶 body＝切換；帶 body（如 `{"status":1}`）＝直接設定（見 3.4、第 13 章） |
| 切換置頂 | `PATCH /api/agent/{資源}/{id}/top` | 商品類才有，同樣是切換 |
| 排序 | `PATCH /api/agent/{資源}/all/sort` | body `{"items":[{"id":1,"sort":1},{"id":2,"sort":2}]}`（見第 6 章） |

### 3.2 列表的篩選參數

- 商品類：`?name=關鍵字`（LIKE 模糊比對）。
- `car_frame`：`?car_brand_id=`、`?car_id=`。
- `car`：`?car_brand_id=`、`?name=`。
- `install_case`、`dealer`、`resource`：`?name=`；`dealer` 另有 `?county=`（見 7.12）；`recommend_product` 有 `?product_type=`。
- 每頁筆數：商品類 15、車框／車款／車廠／盲點 30、盲點適用車款 50、其餘 15。**不是所有資料一次回來，要翻頁（`last_page`）**。

### 3.3 PATCH 更新（重要）

原本後台的更新是「整份取代」：控制器把每個欄位都重新寫一遍，**沒送的欄位會被存成空／null**。Agent API 已加一層保護：

- **標準型資源**：沒送的欄位自動沿用資料庫現有值，所以只要送要改的欄位即可（例：只改 `name`）。
- **list_banner／home_section**：同樣合併（送 `img` 不會清掉 `img_mobile`）。
- **要清空某欄**：明確送空字串 `""`（圖片欄位也一樣）。
- **website**（整份 JSON 取代）：必須先 GET 整份，改完再整份送回（見 7.14）。
- **qa／about**（整份 HTML 取代）：同樣先 GET 再整份送回。
- 車框的圖片只看 `imgArr`（見第 4 章）。

### 3.4 切換型端點（易踩雷）

`status`、`top`（商品）、`pinned`／`home`（導入事例）都是「目前是 1 就變 0、是 0 就變 1」。不帶 body 維持「切換」；**帶 body（如 `{"status":1}`）會直接設定成該值，已相同回 `unchanged`**（2026-10-01 已部署；開工時照第 0.2 節確認版本）。

### 3.5 常見錯誤碼

| 碼 | 意思 |
|---|---|
| 400 | 查無資料、或操作不合法（例：首頁事例已滿 3 筆） |
| 401 | 金鑰無效或已停用 |
| 403 | 這把金鑰沒有該群組的 read／write 權限 |
| 422 | 驗證失敗（欄位缺少或格式錯、圖片網址找不到）；回應會指出哪個欄位 |
| 429 | 超過每分鐘上限 |

### 3.6 欄位通則

- `status`：`1`＝啟用（前台看得到）、`0`＝停用。**新增時必填**。
- `is_top`：商品類「置頂」，`1`／`0`，新增時必填。**前台排序方向有問題，見第 11 章**。
- `sort`：排序數字，方向依模組不同（第 6 章）。
- `content`：HTML（第 5 章）。
- `img`：圖片網址（第 4 章）。

---

## 4. 圖片：規格、上傳、怎麼填進欄位

### 4.1 圖片到底存在哪

- 資料庫**只存網址字串**，檔案在後台伺服器：`storage/app/public/files/1/…`，對外網址 `https://admin.meimai.com.tw/storage/files/1/<資料夾>/<檔名>`（`APP_URL` 要和後台實際網域一致）。
- 後台畫面的「檔案管理員」（laravel-filemanager）看的是同一棵目錄樹；所有圖片欄位的選擇器都開 `files` 類型。
- **不要移動、改名、刪除檔案庫裡的資料夾**：資料庫裡所有舊網址會同時失效（曾發生，799 筆車框圖片要批次修復）。

### 4.2 Hermes 怎麼把圖放進欄位（三種方式，擇一）

**方式 A（最簡單）：直接在寫入請求裡給圖**。任何圖片欄位都吃：
1. 外部 `https://…` 圖片網址：伺服器自己下載存檔（擋內網位址、只收 jpg／png／webp／gif、單檔 20MB）；
2. `data:image/png;base64,…`；
3. multipart 直接帶檔案，欄位名稱同圖片欄位（例 `img`）；
4. 已經上傳過的站內網址。

伺服器會把圖存進 `files/1/AgentImport/年月/`，並換成站內網址後交給後台控制器。

**方式 B：先 upload 再填**（想指定資料夾、要一次傳很多張時用）

```bash
curl -H "X-Agent-Key: $KEY" -F "file=@DSP1.webp" \
  -F "folder=Clarion 2026/GL-700_Ultra_13/Chinese/Transparent/3840x2159" \
  https://admin.meimai.com.tw/api/agent/upload
# → {"message":"上傳成功","name":"DSP1.webp","url":"https://admin.meimai.com.tw/storage/files/1/Clarion 2026/…/DSP1.webp","path":"…","size":12345}
```
- `folder`：`files/1` 底下的相對路徑，可多層，沒有會自動建；不填放 `Agent`。不可含 `..`、反斜線。
- 多檔：`files[]` 一次最多 20 個。檔名沿用原檔名（保留中文），同名自動加 `-2`。
- 允許 jpg／jpeg／png／webp／gif／svg／pdf，單檔 20MB。
- **回傳的 `url` 原封不動填進欄位**：不要自己改網域、不要自己 URL 編碼、不要截掉前段。

**方式 C：取用既有圖片**：GET 讀出別筆資料的圖片網址，照原樣填進去即可（多筆可共用同一個檔案）。

### 4.3 為什麼以前會「網址被清空」（已修，但要知道）

舊版只接受「以 `APP_URL/storage` 開頭、且沒有 URL 編碼」的網址，其他一律存成空字串，不報錯。Agent API 現在會：自動解碼與修正網域；對不到檔案回 422 並指出是哪個欄位；寫入後回傳 `saved_images`，存進去是空的會附 `warnings`。**收到 `warnings` 代表沒存成功，要重做，不要當成功回報**。

### 4.4 各資源的圖片欄位與建議尺寸

| 資源 | 欄位 | 用途／建議尺寸 |
|---|---|---|
| 商品類（car_media、car_head_unit、car_dashcam、car_camera、car_audio_accessories、car_headrest、car_portable、car_fitting、car_blind_spot） | `img` | 列表卡＋詳情主圖；**1200×1200 正方形、純白底或去背、主體置中**，300KB 內。詳情頁若只有一張圖，列表和詳情共用 |
| car_frame | `imgArr` | 見 4.5 |
| banner | `img`、`img_mobile` | 首頁輪播；**v3 更正：電腦版 1920×1080（16:9）、手機版 1080×2160（1:2 直式）**，300KB 內；重要文字放中間，底部約 190px 不放字；手機沒傳就用電腦版（兩側約被裁掉 35%） |
| list_banner | `img`、`img_mobile`、`kicker`、`title`、`description`、三組顏色 | 各頁頂端橫幅；電腦版 **1920×480（4:1）**、手機版 **1080×608（16:9）**；左半邊留白、文字用欄位填（見 14.4）；沒傳圖就維持原本純色標題帶 |
| home_section | `img`、`img_mobile`、`bg_color` | 首頁滿版區塊背景；**v3：電腦 2560×1440、手機 1080×1920**；另有區塊底色 `bg_color`（見 14.5） |
| install_case | `img` | 導入事例實拍；**1600×1000（16:10）**，主體置中，300KB 內；電腦手機共用（首頁卡、列表卡、內頁都是 16:10 裁切） |

**v3 更正**：Logo、頁尾標誌、分頁小圖示、預設分享圖現在**可以在後台改**（`website` 的 `logo_*` 與 `seo.og_image`，見 14.2、14.3），規格見第 17 章。程式內建的預設圖仍在前台程式 `public/` 與 `assets/`，沒設定時使用。

### 4.5 安卓車框的圖片（最特殊）

資料庫有 4 個欄位 `img`、`img1`、`img2`、`img3`（各存 JSON 陣列，每組最多 3 張）。寫入時一律用 `imgArr[群組][序號]`：

| 群組 | 對應 | 前台用途 |
|---|---|---|
| `imgArr[0][0..2]` | `img` 車框主圖 | 列表卡與年份比對的車框長相；**第 1 張是必備**（沒有就顯示灰色佔位框） |
| `imgArr[1][0..2]` | `img1` 車框配件圖 | 詳情頁「車框配件」圖庫 |
| `imgArr[2][0..2]` | `img2` 實際安裝（完工照） | 結果卡的完工照、詳情頁「實際安裝」圖庫；沒有時前台顯示「完工照準備中」 |
| `imgArr[3][0..2]` | `img3` 車框概觀／概況 | 有就當結果卡第 2 張；沒有就不顯示（不寫「沒有」） |

- **沒送的格子、或送 `null` 的格子都保留原圖**（JSON 陣列跳不過前面的格子，用 `null` 佔位，例 `imgArr:[[null,null,"新網址"]]` 只換主圖第 3 張）；要清空某格才送 `""`。
- 浮水印：選填 `watermarkArr`（結構同 `imgArr`）：`0` 不加、`1` 左上、`2` 左下、`3` 右上、`4` 右下、`5` 置中、`-1` 全圖。**預設 0（不加）**。
- 刪單張圖：`PATCH /api/agent/car_frame/{id}/img`，body `{"type":"img|img1|img2|img3","index":0}`。2026-09-30 起**只清空那一格的網址，不再刪實體檔**（同一張圖可能被其他資料共用）；要清檔案請到後台檔案管理員。
- 列表 `GET /car_frame/all` 對 `img1／img2／img3` 只回「張數」，要看實際網址請用 `GET /car_frame/{id}`。
- 前台列表 API（`/api/carframe`）對每組只回**第 1 張**；詳情 API 才回完整陣列。

---

## 5. 內文（content）、摘要（memo／memo_in）怎麼寫

### 5.1 三個文字欄位的分工（商品類）

| 欄位 | 是什麼 | 顯示在哪 | 格式 |
|---|---|---|---|
| `memo` | 列表簡述（**只有 car_media 有、必填**；其他商品表沒有這個欄位） | 多媒體安卓機列表卡片上的一句話 | 純文字，一兩句 |
| `memo_in` | 詳情摘要 | 詳情頁標題下的摘要，**同時是 SEO description** | 純文字，建議 80～120 字，不要放 HTML |
| `content` | 內文／產品規格 | 詳情頁下方的大段內容（規格表、特色、圖文） | **HTML** |

> `memo_in` 不在驗證規則裡（可以不送），但每個商品控制器都會讀它；不送就沿用原值（Agent API 已合併）。

### 5.2 content 的 HTML 寫法

- 前台用 `v-html` 直接輸出，外層樣式是 `.rich`：**字型一律強制成 Noto Sans TC（你寫的行內字型會被蓋掉）**、圖片自動縮到版面寬度並以白底融合（`mix-blend-mode: multiply`，所以圖片請用白底或去背）、表格置中。所以**寫乾淨的語意 HTML 就好**：`<h2>`、`<h3>`、`<p>`、`<ul><li>`、`<table><thead><tbody><tr><th><td>`、`<img src="完整站內網址" alt="說明">`、`<strong>`。
- **規格用 table**：左欄 `<th>` 項目、右欄 `<td>` 內容。前台會把表格置中、最大寬度不超出版面，`th` 會套用底色與品牌色文字。
- **內文裡的圖片**：`<img src>` 要放完整網址（先上傳取得）。**這些網址不在圖片欄位裡**，所以改檔案位置時不會自動跟著改，要另外搜尋 `content` 欄位。圖片請加 `alt`。
- **不要**放 `<script>`、`<style>` 全域樣式、`<iframe>`（YouTube 嵌入除外且需先確認）、行內寫死顏色與字級（會破壞全站字型與品牌色）。
- **不要**在內文放 `<h1>`（頁面標題已經是 h1）。
- 歌樂專區與美邁專區的頁面配色會自動套用（Clarion 藍／美邁藍＋橘），**內文裡不要自己指定品牌色**。
- `qa`／`about`／`home_section.content` 也是 HTML，規則相同。特別注意 `about`（品牌故事）：**舊內容夾帶全域 CSS（`.about-page:has(.about-v5) …`）會把頁面標題藏起來**，新內容不要再放這種全域 CSS。

### 5.3 純文字欄位

`name`、`memo`、`memo_in`、`size`、`hard_drive`、`ram`、`resolution`、`price`、`material`、`power` 都是純文字（存字串，前台直接顯示，不要放 HTML）。`price` 是「建議售價」字串，例 `NT$ 12,800`；有填詳情頁會顯示價格標籤，留空則顯示預設文字（洽詢經銷據點之類，以前台實際顯示為準）。

---

## 6. 排序邏輯（逐模組，依程式）

> **v3 更正：下表是 09-30 的舊版狀態。本機新版已統一排序規則，請以 14.8 為準**（8 個產品頁後台補排序介面、前後台順序一致、首頁精選改成小的在前、banner／dealer／resource_category／car_brand 排序只在啟用或停用同組內調整）。下表保留作為「部署前正式站」的現況對照。

前台實際順序由**前台 API**決定，後台列表順序只是後台畫面方便管理。兩邊不同。

| 資源 | 前台順序（訪客看到） | 後台列表順序 | `sort` 怎麼用 |
|---|---|---|---|
| car_media | `is_top` **大到小** → `name` A→Z | `is_top` 大→小 → `status` 大→小 → `name` | **前台不看 sort** |
| car_head_unit、car_dashcam、car_camera、car_audio_accessories、car_headrest、car_portable、car_fitting | `is_top` **大到小**（置頂在前，2026-09-30 修正；原本反向）→ `sort` 小→大 | 建立時間新→舊 | sort 小的在前；預設都是 0 |
| car_blind_spot | `is_top` 大→小（2026-09-30 修正）→ `name` A→Z | `status` → `name` → 建立時間 | 資料表沒有 sort 欄位 |
| car_frame | `name` A→Z（前台再依年份分組排） | 車廠名→車框名→起始年 | 前台不看 sort |
| car_brand／car | `sort` 小→大 → `name` A→Z | 車廠：status→name；車款：status→車廠名→車款名→起始年 | sort 小的在前；預設 0，等於按名稱排 |
| car_blind_spot_format | `sort` 小→大 | status→sort→建立時間 | sort 小的在前 |
| banner | `sort` 小→大（只取啟用） | status→sort→建立時間 | sort 小的在前 |
| dealer | `sort` 小→大 | status→sort→建立時間 | sort 小的在前 |
| resource_category／resource | `sort` 小→大 | status→sort→建立時間 | sort 小的在前 |
| install_case 列表 | `is_pinned` 大→小 → 「安裝日期（沒填用建立時間）」新→舊 → `sort` 大→小 | `sort` 大→小 → 建立時間 | **sort 大的在前**（和其他相反） |
| install_case 首頁 | 只取 `is_home=1`，`home_sort` 小→大 → 日期新→舊，**最多 3 筆** | — | — |
| recommend_product（首頁精選） | `sort` **大→小** → 建立時間新→舊 | 同前台 | **sort 大的在前** |
| list_banner／home_section／website／qa／about | 無排序 | — | — |

### 6.1 怎麼改排序

> **哪些資源的新增／更新收 `sort` 欄位？只有 `install_case` 和 `recommend_product`。** 其他所有資源（banner、dealer、resource、resource_category、car_brand、car、car_blind_spot_format、商品類）在新增／更新時送 `sort` 會被忽略，**新增出來一律是 0**，要調整只能用下面的排序端點。

`PATCH /api/agent/{資源}/all/sort`，body：

```json
{"items":[{"id":12,"sort":1},{"id":7,"sort":2},{"id":3,"sort":3}]}
```
- 只更新有列出來的 id；沒列出的不動。`sort` 會被取整數。
- **要確認方向**：大部分資源是小的在前；`install_case`、`recommend_product` 是大的在前。
- 商品類的後台畫面沒有拖曳排序，`sort` 預設全是 0，所以**多數商品前台其實是依 `is_top`＋建立順序**（同為 0 時資料庫自然順序）。要控制順序請用上面的 sort 端點。
- `car_media` 前台完全不看 `sort`（只看 `is_top`＋名稱）；`car_blind_spot` 沒有 `sort` 欄位。
- `is_top` 翻轉請用 `PATCH /{id}/top`（切換）或 `PATCH /{id}` 帶 `is_top`。

> 2026-09-30 已修正：商品類前台 `is_top` 一律「大到小」，置頂在前（原本 9 支 API 寫反；2026-10-01 已部署）。

---

## 7. 逐模組說明（欄位、前台位置、注意事項）

欄位表「必填」欄：新增（POST）時必填；更新（PATCH）不必重送，已自動沿用原值。

### 7.1 car_media　多媒體安卓機／車型專用機

- 後台：商品 → 多媒體。前台：`type` 決定頁面——
  `0`＝`/mm/me`（MM ME 系列）、`1`＝`/mm/oem`（MM 車型專用機）、`2`＝`/clarion/gl`（Clarion GL 系列）、`3`＝`/clarion/oem`（Clarion 車型專用機）。詳情 `/multimediaDetail/{id}`；也出現在首頁搜尋與精選。
- 端點：標準型＋`PATCH /{id}/top`。

| 欄位 | 必填 | 說明 |
|---|---|---|
| type | ✔ | 0～3，決定在哪個列表頁、用哪個品牌配色（**美邁＝深藍橘、歌樂＝Clarion 藍**） |
| name | ✔ | 型號名稱，列表與詳情標題 |
| img | ✔ | 主圖（第 4 章） |
| memo | ✔ | 列表卡簡述 |
| size／hard_drive／ram／resolution／price |  | 規格徽章與價格，純文字 |
| memo_in |  | 詳情摘要（SEO description） |
| content | ✔ | 內文 HTML（第 5 章） |
| is_top、status | ✔ | 1／0 |

注意：前台詳情 API 沒有檢查 `status`（見第 11 章），停用的商品知道網址仍打得開。

### 7.2 car_head_unit　車用主機 1/2DIN（Clarion）

- 前台 `/headUnit`、詳情 `/headUnitDetail/{id}`。`type`：`0`＝1DIN、`1`＝2DIN（前台可依 type 篩）。
- 欄位：type✔、name✔、img✔、size、hard_drive、ram、resolution、price、content✔、is_top✔、status✔。

### 7.3 car_dashcam　行車記錄器

- 前台：`brand=0`→`/mm/dashcam`、詳情 `/mm/dashcamDetail/{id}`；`brand=1`→`/clarion/dashcam`、詳情 `/clarion/dashcamDetail/{id}`。
- 欄位：**brand✔（0 美邁、1 歌樂）**、name✔、img✔、content✔、is_top✔、status✔（memo_in 選填）。
- 注意：**行車記錄器獨立分類，不要放進「車用配件」**。美邁頁面自動用美邁色。

### 7.4 car_camera　鏡頭

- 與行車記錄器完全同結構：`brand=0`→`/mm/camera`、`/mm/cameraDetail/{id}`；`brand=1`→`/clarion/camera`、`/clarion/cameraDetail/{id}`。
- 欄位：brand✔、name✔、img✔、content✔、is_top✔、status✔。鏡頭獨立分類，不要放進車用配件或影像・安全。

### 7.5 car_audio_accessories　汽車音響（Clarion）

- 前台 `/audioAccessories`、詳情 `/clarion/audioAccessoriesDetail/{id}`。
- 欄位：type✔（`0`一般喇叭、`1`高音喇叭、`2`重低音、`3`擴大機，前台可依 type 篩）、name✔、img✔、content✔、is_top✔、status✔。

### 7.6 car_headrest（頭枕螢幕）／car_portable（可攜式）

- 前台 `/headrest`、`/portable`，詳情 `/headrestDetail/{id}`、`/portableDetail/{id}`。**目前前台導覽已隱藏入口**，但資料與頁面仍在（直接輸入網址看得到）。
- 欄位：name✔、img✔、content✔、is_top✔、status✔。

### 7.7 car_fitting　車用配件（美邁）

- 前台 `/fitting`、詳情 `/fittingDetail/{id}`。
- 欄位：name✔、img✔、**material✔（材質）、power✔（電源）**、content✔、is_top✔、status✔。
- 注意：只放「安裝與日常使用的配件」；行車記錄器、鏡頭都已獨立分類，不要再放進來。

### 7.8 car_blind_spot　影像・安全／盲點偵測（美邁）

- 前台 `/safety`、詳情 `/safetyDetail/{id}`（詳情頁帶「適用車款」查詢，資料來自 7.9）。
- 欄位：name✔、img✔、content✔、is_top✔、status✔。資料表**沒有 `sort` 欄位**。

### 7.9 car_blind_spot_format　盲點偵測適用車款（掛在商品底下）

- 路徑多一層父層：`/api/agent/car_blind_spot_format/{car_blind_spot 的 id}/all`、`POST …/{父id}`、`PATCH …/{父id}/{id}`、`DELETE …/{父id}/{id}`。父商品不存在會回 500「系統異常,請正常操作」。
- 欄位：car_brand_id✔（車廠，見 7.11）、style✔（車款名稱，文字）、year✔（年份文字，例 `2019-2023`）、spc✔（規格／安裝備註）、status✔。
- 前台：詳情頁「適用車款」依車廠篩選，順序 `sort` 小→大。

### 7.10 car_frame　安卓車框

- 前台：`/carFrame`（車廠→車款→年份→結果）、詳情 `/carFrameDetail/{id}`、首頁快搜、車型查詢結果頁。
- 欄位：

| 欄位 | 必填 | 說明 |
|---|---|---|
| car_brand_id | ✔ | 車廠 id（car_brand） |
| car_id | ✔ | 車款 id（car），**必須屬於上面那個車廠** |
| year_start／year_end | ✔ | 適用年份區間。前台依「起始-結束」分組；同區間有多款就顯示「需經銷店確認」 |
| size | ✔ | 可搭主機吋數，純文字，例 `9`、`10.1` |
| name |  | 這一款的名稱／顏色，同年份有多款時用來區分（例：棕色面板）；只有一款可留空 |
| content |  | 詳情頁內文 HTML |
| imgArr |  | 圖片，見 4.5（新增時至少要有 `imgArr[0][0]` 才有列表圖） |
| watermarkArr |  | 浮水印，選填 |
| status | ✔ | 1／0 |

- 前台規則：結果卡第 1 張＝車框（`imgArr[0]`）、有 `imgArr[3]` 就第 2 張＝車框概況、`imgArr[2]` 為完工照（沒有顯示「完工照準備中」）。
- 新增流程與範例見 9.4。
- 注意：列表 `GET /car_frame/all` 是 `JOIN car_brand`，**車廠被刪的車框不會出現在後台列表**。

### 7.11 car_brand／car　車廠與車款

- 前台：車框、導入事例、盲點適用車款的下拉，以及首頁快搜。
- car_brand 欄位：name✔、status✔。**name 格式「英文＋空格＋中文」**，例 `TOYOTA 豐田`；沒有中文就只有英文（`BMW`）。前台用英文部分做大字、中文做小字，首頁快搜固定熱門品牌靠英文比對：`TOYOTA、MITSUBISHI、HONDA、SUZUKI、NISSAN、HYUNDAI、MAZDA、FORD、BENZ、BMW、VOLKSWAGEN、AUDI`（名稱英文要完全對上，`NISSAN 裕隆` 這種寫法才會被歸到 NISSAN）。
- car 欄位：car_brand_id✔、name✔（車款名）、year_start✔、year_end✔、status✔。
- ⚠ **刪除車廠或車款不會檢查是否還有車框／事例在用**，會留下孤兒資料（車框的 `JOIN` 會讓它消失）。要下架請改 `status=0`，不要刪。

### 7.12 dealer　經銷據點

- 前台 `/partner`，可依縣市篩選。
- 欄位：name✔、county✔、address✔、tel✔、status✔。排序只能用 `PATCH /dealer/all/sort`（新增／更新不收 `sort`）。
- ⚠ **`county` 是縣市的「編號」，不是文字**。編號＝`config/county.php` 的順序：`0` 台北市、`1` 新北市、`2` 桃園市、`3` 台中市、`4` 台南市、`5` 嘉義市、`6` 高雄市、`7` 新竹縣、`8` 苗栗縣、`9` 彰化縣、`10` 南投縣、`11` 雲林縣、`12` 嘉義縣、`13` 屏東縣、`14` 宜蘭縣、`15` 花蓮縣、`16` 台東縣、`17` 澎湖縣、`18` 金門縣、`19` 連江縣、`20` 基隆市、`21` 新竹市。送文字會造成前台取不到縣市名稱而壞掉。

### 7.13 install_case　導入事例（安裝案例）

- 前台 `/cases`、`/cases/{id}`；`is_home=1` 的最多 3 筆出現在首頁「案例」。
- 額外切換端點：`PATCH /{id}/pinned`（置頂切換）、`PATCH /{id}/home`（首頁顯示切換，**已滿 3 筆會回 400**）。
- 欄位：

| 欄位 | 必填 | 說明 |
|---|---|---|
| category | ✔ | `0`多媒體安卓機 `1`車型專用機 `2`汽車音響 `3`行車記錄器 `4`影像・安全 `5`車用配件 `6`車用主機 1/2DIN `7`頭枕螢幕 `8`可攜式（必須 0～8） |
| name | ✔ | 案例名稱 |
| img | ✔ | 案例圖 1600×1000 |
| car_brand_id／car_id | ✔ | 車廠／車款 id（必須存在於 car_brand／car）。前台組成「車廠名 車款名」顯示 |
| product |  | 使用產品（**純文字**，不是商品 id） |
| dealer |  | 施工據點（**純文字**，不是 dealer 資源的 id） |
| need／work |  | 客戶需求／施工內容（純文字，內頁顯示） |
| installed_at |  | 安裝日期 `YYYY-MM-DD`；排序用它，沒填就用建立時間 |
| is_pinned／is_home／home_sort／sort |  | 置頂／首頁顯示／首頁排序／列表排序（sort **大的在前**） |
| status | ✔ | 1／0 |

- ⚠ **用 POST／PATCH 直接把 `is_home` 設成 1 不會檢查 3 筆上限**（只有 `/home` 切換端點會檢查）。要上首頁請用 `/home` 端點，或先數過目前 `is_home=1` 的筆數。

### 7.14 recommend_product　首頁精選商品

- 前台首頁「精選商品」區（前台讀 `/api/recommend_products`）。**不是商品頁的推薦搭配**。
- 欄位：product_type✔、product_id✔、sort（選填，**v3：小的在前**；部署前的正式站仍是大的在前，要等 migration `2026_10_02_000005` 執行後才改變，見 14.0）、status✔。
- `product_type` 可選值：`car_frame`、`car_media`、`car_blind_spot`、`car_dashcam`、`car_camera`、`car_fitting`、`car_headrest`、`car_portable`、`car_audio_accessories`、`car_head_unit`（以 `GET /api/agent/schema` 與 `GET /api/agent/recommend_product/options?product_type=…` 為準；options 會列出該類型所有商品的 id 與名稱）。
- 名稱、圖片、連結由後端依類型自動組（車框會組成「車廠 車款 名稱」）；**商品被刪除時該筆不會顯示**；**商品停用（status=0）仍可能顯示**（解析器沒檢查 status），要下架請同時把這筆精選停用。

### 7.15 banner　首頁輪播

- 前台首頁頂端輪播，只取 `status=1`，`sort` 小→大。
- 欄位：name✔（後台辨識用）、url（點擊連結，可空，必須是完整網址）、img✔（電腦版）、img_mobile（手機版，沒傳用電腦版）、status✔。**排序不能用新增／更新設定，只能用 `PATCH /banner/all/sort`**（第 6 章）。

### 7.16 list_banner　各頁頂端橫幅

- **v3：除了圖片，還能設左側文字（小標題、大標題、說明）與三組文字顏色，見 14.4。**
- 路徑：`GET /api/agent/list_banner/all`（列出所有可設定的位置，含目前圖片與文字）；`PATCH /api/agent/list_banner/{page_key}/{type_key}`，body `{"img":"…","img_mobile":"…","kicker":"…","title":"…","description":"…","kicker_color":"#RRGGBB",…}`（只送要改的）。**沒有新增／刪除**，位置固定寫在 `config/list_banner.php`，要清掉就送空字串。
- 沒有 `types` 的頁面，`type_key` 固定用 `default`。
- 尺寸：電腦 **1920×480**、手機 **1080×608**。左半邊要留白，前台會疊標題文字（別把字烙在圖上）。

| page_key | 對應前台頁面 | type_key |
|---|---|---|
| multimedia | 多媒體安卓機 | `0` MM ME、`1` MM 車型專用機、`2` Clarion GL、`3` Clarion OEM |
| camera | 鏡頭 | `mm`、`clarion` |
| dashcam | 行車記錄器 | `mm`、`clarion` |
| carFrame | 安卓車框 `/carFrame` | default |
| fitting | 車用配件 `/fitting` | default |
| safety | 影像・安全 `/safety` | default |
| headUnit | 車用主機 1/2DIN | default |
| audioAccessories | 汽車音響 | default |
| portable／headrest | 可攜式／頭枕螢幕 | default |
| clarionOverview／mmOverview | 歌樂總覽／美邁總覽 | default |
| about | 品牌故事 `/about` | default |
| cases | 導入事例列表 | default |
| qa | 常見問題 | default |
| searchPage | 車型查詢結果頁 | default |
| download | 資源下載 | default |
| partner | 經銷據點 | default |
| productLanding | 產品分類頁 | `landingHeadunit`（/products/android-headunit）、`landingAudio`（/products/car-audio）、`landingDashcam`（/products/dash-cam） |

### 7.17 home_section　首頁三個滿版區塊

- 路徑：`GET /api/agent/home_section/all`；`PATCH /api/agent/home_section/{section_key}`，`section_key`＝`zone1`（Clarion 滿版背景區，緊接在精選商品下面）、`zone2`（MM 美邁滿版背景區）、`zone3`（尾端 CTA 區）。
- 欄位：content（HTML，區塊文字）、img（背景圖電腦版）、img_mobile、**bg_color（v3 新增，區塊底色 `#RRGGBB`）**。沒設定內容前台就不顯示該區塊文字。內容結構與尺寸見 14.5。

### 7.18 resource_category／resource　資源下載

- 前台 `/download`：分類為標題、底下列出檔案。
- resource_category：name✔、memo（簡述）、status✔。
- resource：resource_category_id✔、name✔、**url✔（必須是完整 http(s) 網址，前台直接連出去）**、status✔。
- 兩者的排序都只能用各自的 `PATCH …/all/sort`（新增／更新不收 `sort`）。列表可用 `?name=`，resource 另可 `?resource_category_id=`。**v3 更正：下載檔案一律是貼連結**（Google 雲端分享連結或 YouTube 連結，後台已拿掉上傳按鈕），Google 雲端要把一般存取權設成「知道連結的任何人」，見 14.7。

### 7.19 website／qa／about　設定類（單一份資料）

- 路徑：`GET /api/agent/{website|qa|about}/all`、`PATCH /api/agent/{website|qa|about}`（沒有 id）。
- **website（v3：現在裝的東西更多）**：`content` 是一個 JSON 物件，除了下面的基本欄位，還有 `theme_*`（全站色彩，14.6）、`logo_*`（標誌圖片，14.2）、`categories`（產品類別開關，14.1）、`seo`（SEO／GEO，14.3）。**每次寫入一律先 GET 整份、只改自己的鍵、整份送回；`categories`、`seo` 是巢狀物件，要完整帶回。** 基本欄位：`copyright`（頁尾版權文字）、`address`、`tel`、`email`、`facebook`、`instagram`、`youtube`（社群網址；**沒填的社群，前台頁尾就不會顯示該圖示**）。目前正式站是**整份取代**：先 GET、改欄位、`PATCH {"content": {整份物件}}`。本機已改成「沒送的鍵沿用現有值」（工程師必看第 129 項，**要再部署一次才生效**；部署後只送要改的鍵即可，例 `{"content":{"tel":"03-…"}}`）。qa／about 是整段 HTML，維持整份取代。公司名稱、地址、電話要跟前台 `composables/useSiteInfo.js` 逐字一致（地址 `桃園市桃園區國信街35號`、電話 `03-2170098`）。
- **qa**：`content` 是整份 HTML（常見問題頁）。整份取代。
- **about**：`content` 是整份 HTML（品牌故事頁的內文區）。整份取代；頁面大標與 Banner 由前台程式與 `list_banner.about` 決定，不在這裡。

### 7.20 watermark　浮水印設定（系統設定 → 浮水印設定）

- 路徑：`GET /api/agent/watermark/all`（看 4 組目前用的檔）、`POST /api/agent/watermark/{key}`（換圖，multipart 欄位 `file`）、`DELETE /api/agent/watermark/{key}`（還原成程式內建預設圖）。`key`＝0～3，各組用途看 `GET all` 回的 `name`。權限 `settings`。
- 檔案規格：PNG 透明背景、5MB 以內、寬高 20×10～4000×4000。
- 不是新增／刪除型資源：只有這 4 組，不能加第 5 組。
- **只影響之後存檔的車框圖**：車框（7.10）存檔時依 `watermarkArr` 選的位置把浮水印蓋上去；換了浮水印，舊車框圖要重新存檔才會套用。
- 換圖與還原前，舊檔會自動備份到 `storage/app/public/watermark-config/backup/`；「已經是預設」再按還原會回 422。
- 前台不直接顯示這個設定。

---

## 8. 前台公開 API 對照（用來驗證「前台會看到什麼」）

前台只回 `status=1` 的資料（**詳情 API 例外，見第 11 章**）。Base：`https://admin.meimai.com.tw/api`，不需要金鑰。

| 前台 API | 參數 | 回什麼 |
|---|---|---|
| `GET /multimedia`、`/multimedia/{id}` | `type=0..3` | car_media 列表／詳情 |
| `GET /head_unit`、`/dashcam`、`/camera`、`/audio_accessories`、`/headrest`、`/portable`、`/fitting`、`/blindspot`（各自附 `/{id}`） | `brand=0/1`（dashcam、camera）、`type`（head_unit、audio） | 各類商品列表／詳情 |
| `GET /carframe`、`/carframe/{id}` | `car_brand_id`、`car_id`、`year` | 車框；列表每組圖只回第 1 張 |
| `GET /car` | — | `car_brand`＋`car`（車廠與車款） |
| `GET /search` | `car_brand_id`、`car_id`、`year` | 車型查詢結果（車框＋相關商品） |
| `GET /banner` | — | 首頁輪播 |
| `GET /list_banner` | `page`、`type` | 某頁橫幅（沒設定回 `img:null`） |
| `GET /home_section` | — | 首頁三個區塊 |
| `GET /recommend_products` | — | 首頁精選（已組好 name／img／link） |
| `GET /install_cases`、`/install_cases/{id}` | `home=1` 取首頁 3 筆 | 導入事例 |
| `GET /partner` | `county` | 經銷據點＋縣市清單 |
| `GET /resource` | — | 資源下載（分類含檔案） |
| `GET /website`、`/question`、`/about` | — | 設定、常見問題、品牌故事內容 |

---

## 9. 標準作業流程（照做，不要自己發明）

### 9.1 新增一個商品（以行車記錄器為例）

1. `GET /api/agent/schema`，確認 `car_dashcam` 必填欄位。
2. 準備圖片：1200×1200 白底／去背。
3. `POST /api/agent/car_dashcam`：
```json
{
  "brand": 0,
  "name": "MM-DVR 4CH",
  "img": "https://…/DVR-4CH.webp",
  "memo_in": "四鏡頭行車記錄器，前後左右全覆蓋，支援停車監控。",
  "content": "<h2>產品特色</h2><ul><li>…</li></ul><table><tr><th>鏡頭數</th><td>4</td></tr></table>",
  "is_top": 0,
  "status": 1
}
```
4. `GET /api/agent/car_dashcam/all?name=MM-DVR 4CH` 找回新筆 id。
5. `GET /api/agent/car_dashcam/{id}` 讀回，確認 `img` 是站內網址（不是空字串、不是外部網址）。
6. 打前台 `GET /dashcam?brand=0`，確認出現在列表；`GET /dashcam/{id}?brand=0` 確認詳情。

### 9.2 只換某商品的圖

`PATCH /api/agent/car_dashcam/{id}`，body 只有 `{"img":"https://…新圖…"}`。讀回確認 `saved_images`／`img` 已更新；其他欄位不會被動到。

### 9.3 上架／下架

`PATCH /{id}` 帶 `{"status":0}`（下架）或 `{"status":1}`（上架）。**不要用刪除來下架**。

### 9.4 新增一筆安卓車框（含完整圖片）

1. 找車廠 id：`GET /api/agent/car_brand/all?page=1`（依 `last_page` 翻完）；找車款：`GET /api/agent/car/all?car_brand_id=…&name=…`。沒有就先 7.11 建立（車廠名稱格式「英文 中文」）。
2. `POST /api/agent/car_frame`：
```json
{
  "car_brand_id": 3, "car_id": 41,
  "year_start": 2019, "year_end": 2023, "size": "9",
  "name": "", "status": 1,
  "imgArr": [
    ["https://…/frame-front.webp"],
    ["https://…/parts-1.webp", "https://…/parts-2.webp"],
    ["https://…/installed-1.webp"],
    ["https://…/overview.webp"]
  ]
}
```
3. `GET /api/agent/car_frame/all?car_brand_id=3&car_id=41` 找回 id，再 `GET /api/agent/car_frame/{id}` 確認 4 組圖都是站內網址。
4. 前台 `GET /carframe?car_brand_id=3&car_id=41` 確認列表出現。

### 9.5 更新網站基本設定（website）

`GET /api/agent/website/all` → 取 `item.content` → 只改要改的欄位 → `PATCH /api/agent/website` body `{"content": {整份物件}}`。**欄位一個都不能少**（v3：content 裡還有 theme_／logo_／categories／seo，漏掉就等於刪掉）。具體流程見 16.1～16.5。

### 9.6 改列表頁 Banner

`GET /api/agent/list_banner/all` 找到 `page_key／type_key` → `PATCH /api/agent/list_banner/{page_key}/{type_key}` body `{"img":"…","img_mobile":"…"}`（只送一張也不會清掉另一張）。尺寸 1920×480／1080×608，左半留白。

### 9.7 調整排序

見 6.1。先 `GET all` 列出現況（含 id、sort），算好新順序，再一次 `PATCH all/sort` 送全部要動的 id。

### 9.8 改完要讓前台更新

**v3 更正**：以 2026-10-01 實測，前台即時讀取，後台資料改完前台重新整理就會變，不需要 `/publish`。回報時分開說：「後台已改」＋「前台已用公開 API／網頁確認」；若前台有快取或改回靜態產生，才要說「待重新產生」。

---

## 10. 驗證清單（每次寫入後都做，回報時附證據）

1. 寫入回應沒有 `warnings`、狀態碼 2xx；看 `saved_images` 的網址是 `https://admin.meimai.com.tw/storage/files/1/…`。
2. `GET /api/agent/{資源}/{id}` 讀回：欄位值符合預期、圖片不是空字串。
3. 打前台公開 API（第 8 章）確認訪客端看得到（`status=1` 才會出現）。
4. 圖片實際可開：對 `saved_images` 的網址送 `HEAD`／`GET`，要 200 且 `Content-Type` 是 `image/*`。
5. 列表頁要翻到 `last_page`，不要只看第一頁就說「沒有這筆」。
6. 回報格式：「資源＋id、改了哪些欄位（前→後）、讀回結果、前台 API 確認結果、前台是否已重新產生」。
7. 不確定的事寫「不確定／待驗證」，不要猜。

---

## 11. 已知問題與待修正清單

### 11.1 已修好並於 2026-10-01 部署（工程師必看第 111～123 項；集中對照見第 0 章）

- 車框圖片網址被默默清空：網域／編碼不符、沒送的格子都會被清成空字串（已修，且對不到檔案改回 422）。
- Agent API 全資源圖片統一處理、PATCH 合併、寫入後驗證、欄位白話說明（`/api/agent/schema` 的 `docs`）。
- 列表 Banner／首頁區塊：送一張圖會把另一張清成 null（Agent 端已合併）。
- `car_media` 的排序端點：缺 `use DB`、且寫到 `car_frame` 資料表（已修為 `car_media`）。
- `car_fitting` 的排序端點：寫到 `car_frame` 資料表（已修為 `car_fitting`）。
- `config/list_banner.php`：`fitting` 原標「影像・安全」、`safety` 原標「盲點偵測」，和實際頁面（車用配件、影像・安全）對不上，已改正名稱。

### 11.2 需要工程師修（程式問題，建議優先序）

| # | 問題 | 影響 | 建議 |
|---|---|---|---|
| 1 | ✅已修（2026-09-30）**商品類前台 API 用 `is_top` 小到大排序**（head_unit、dashcam、camera、audio、headrest、portable、fitting、blind_spot、search），置頂反而排最後；car_media 是大到小 | 「置頂」效果相反，且（已修） | 改成 `orderByDesc('is_top')`，與 car_media 一致；先確認後台是否有人依反向習慣設定過 |
| 2 | **`car_blind_spot` 的排序端點寫到 `car_frame` 資料表，且盲點資料表沒有 `sort` 欄位** | 呼叫排序會報錯或寫錯表 | 加 `sort` 欄位 migration＋修表名，或移除該端點 |
| 3 | ✅已修（2026-09-30）**前台詳情 API 不檢查 `status`**（多媒體、各類商品、車框詳情） | 停用的商品只要知道網址仍打得開、會被搜尋引擎收錄 | 詳情 API 加 `where('status',1)` |
| 4 | ✅已修（2026-09-30）**首頁事例 3 筆上限只在 `/home` 切換端點檢查**，POST／PATCH 直接設 `is_home=1` 會繞過 | 首頁可能出現超過 3 筆（前台雖 `limit(3)`，後台狀態會錯亂） | 在 create／update 也做同樣檢查 |
| 5 | ✅已修（2026-09-30，`App\Support\RelationGuard`）刪除車廠／車款／分類**不檢查關聯資料** | 留下孤兒資料；車框後台列表（JOIN 車廠）會讓它消失 | 有關聯就拒絕刪除，或改軟刪除 |
| 6 | ✅已修（2026-09-30）車框 `deleteImg` 會**實體刪檔** | 共用同一檔案的其他資料變壞圖 | 改成只清空欄位、不刪檔；或先檢查沒有其他引用 |
| 7 | ✅已修（2026-09-30）首頁精選解析器**不檢查商品 `status`** | 停用的商品仍出現在首頁精選 | 解析時加 `status=1` |
| 8 | ✅已修（2026-09-30）`POST` 新增成功**不回新資料 id** | Hermes 要再搜尋才找得到，容易找錯筆 | 回 `{"message":…, "id": 新id}`（所有 create 一起改，向下相容） |
| 9 | 稽核紀錄只涵蓋 Agent API 寫入；後台畫面（人手）操作是否有紀錄【待驗證】，程式裡沒看到 | 人手改壞了無法追溯、無法取得改前快照 | 需要時補上 |
| 10 | 內文 `content` 直接 `v-html` 輸出，**沒有清理／白名單** | 內文若貼進惡意 `<script>` 會在訪客端執行；Agent 金鑰權限高，金鑰外流風險放大 | 存檔時過濾 `<script>`、`on*` 事件；金鑰限制來源 IP |
| 14 | `routes/web.php` 有 `PATCH car_blind_spot/{id}/spc` 路由，但控制器沒有 `spc` 方法（後台「規格綁定」按鈕其實只是跳到適用車款頁） | 沒人呼叫所以沒事；Agent 端已從 schema 移除，避免 Hermes 打到 500 | 順手刪掉這條路由 |
| 11 | `SettingSeeder` 的 qa 種子資料欄位拼成 `contnet` | 只影響「全新安裝」的種子；現有正式站不受影響 | 順手修正拼字 |
| 12 | 正式機 `.env` 的 `APP_URL` 必須等於後台實際網域 | 不一致時上傳網址、車框圖片比對都會出錯 | 部署時確認 |
| 13 | ✅已加 `/publish`（需設定 hook）**後台寫入後前台不會自動重新產生** | 改完看不到是常態，Hermes 與老闆都會以為「沒改成功」 | 加 webhook：後台寫入（或 Agent 寫入）後觸發前台 build；或至少提供一個「重新產生前台」按鈕／API |

### 11.3 需要老闆決定

- 商品排序要不要開放給人在後台畫面拖曳（現在畫面沒有排序功能，全靠 API）。
- `install_case`、`recommend_product` 的 `sort`「大的在前」要不要改成和其他一致的「小的在前」（改了會影響現有順序）。
- Agent 金鑰管理頁面（自己發／停用金鑰）要不要做。
- 影片：目前 upload 不收 mp4，建議影片走 YouTube 嵌入。

### 11.4 網站整體仍待辦（摘要，詳見〈待辦總整理〉）

- 工程師合併並部署本機所有改動（首頁快搜、車框頁改版、分享按鈕與頁尾社群原廠色、美邁頁配色、Header、副標、Agent API、favicon／og／sitemap 等）。
- 表頭「立即諮詢」按鈕、下拉選單、頁尾上緣線在美邁專區改用美邁色（未做）。
- 品牌故事 Banner 重新輸出（左半留白）、`about` 內容裡隱藏標題的舊 CSS 從後台刪除。
- Search Console 提交 sitemap、後台內容中錯誤 canonical／meta 清除、`/api/website` 與 `useSiteInfo.js` 逐字一致。

---

## 12. 禁止事項（Hermes 務必遵守）

1. 不要貼出、記錄、上傳金鑰。
2. 不要直接操作資料庫、不要動檔案庫資料夾（改名、搬移、刪除），不要用 `deleteImg` 來「隱藏」圖片。
3. 不要用刪除代替下架；要刪除任何資料前先回報並取得老闆同意（刪除無法復原）。
4. 不要在內文放 `<script>`、全域 `<style>`、寫死品牌色或字型。
5. 不要同時大量寫入（每分鐘上限 120 次，且一次改太多難以追蹤）；批次操作先做一筆驗證再做其餘。
6. 不要把「後台已改」說成「網站已更新」。
7. 不確定欄位意思時，先讀 `/api/agent/schema` 的 `docs`，再問老闆；不要猜。

---

## 13. 2026-09-30 新增功能（Agent 自動化補強）

- **POST 回傳 id**：新增成功回 `{"message":"新增成功","id":N,"item":{…}}`，不必再搜尋。
- **用 body 直接設定**：`PATCH /{資源}/{id}/status`（或 top／pinned／home）帶 `{"status":1}` 是「設定為 1」；值已相同回 `{"unchanged":true}`，不會誤切換。不帶 body 則維持原本「切換」。
- **sort**：POST／PATCH 可帶 `sort`（install_case、recommend_product 除外，它們維持原邏輯）。
- **重新產生前台**：`POST /api/agent/publish`（scope `publish`）；需 `.env AGENT_DEPLOY_HOOK_URL`，未設定回 501，60 秒內不重複觸發。
- **導入事例首頁上限**：POST／PATCH 設 `is_home=1` 超過 3 筆回 400。
- **已修**：car_media／car_fitting／car_blind_spot 排序寫錯資料表（盲點表仍無 sort 欄位）。
- 第 11.2 表的第 2、4、8、13 項即由上述處理（第 2 項僅部分）。
- **置頂排序修正**：head_unit、dashcam、camera、audio_accessories、headrest、portable、fitting、blindspot、search 這 9 支前台列表 API 改為 `is_top` 大→小（置頂在前）；第 6 章表格中標 ⚠ 的「小到大」現在都是「大到小」。
- **停用商品詳情擋下**：10 支前台詳情 API 加 `status=1`，停用商品的詳情網址回 404（前台目前顯示空白頁，建議前台工程師補「找不到此商品」）。
- **首頁精選跳過停用商品**：`RecommendProductResolver::resolveMany($rows, true)`，後台列表仍看得到停用商品名稱。
- **刪除前檢查關聯**（`App\Support\RelationGuard`）：車廠（被車款／車框／事例／盲點適用車款用）、車款（被車框／事例用）、資源分類（底下有檔案）、10 類商品（還在首頁精選）→ 回 400「…仍被 N 筆 X 使用中，無法刪除」。盲點商品刪除時子資料「適用車款」一起刪。
- **車框刪單張圖不再刪實體檔**：`PATCH /car_frame/{id}/img` 只把該格清空；檔案留在檔案管理員。
- **浮水印設定納入 Agent API**：`watermark` 資源（7.20），後台選單每一項現在都有對應 API（1.1 對照表）。
- 修改前後完整對照：`工程師交付_後端API修正_20260930/`。

---

## 14. 2026-10-02 新增與調整（先在本機完成，部署後才在正式站生效）

這一章是給 Hermes 的「新功能操作說明」。**概念和人一樣**：先看後台畫面長怎樣、每一格是什麼意思，再決定怎麼改。每一節都附：後台畫面位置、前台哪裡看得到、Agent 怎麼讀寫、圖片尺寸、驗證方式。

### 14.0 開工前先確認：這些新功能部署了沒

下面的功能都是「本機版」，正式站要等工程師部署（`php artisan migrate`、`config:clear`、`view:clear`、`route:clear`＋**前台程式本身也要部署**）後才有。**在確認以前，不要對正式站寫入新欄位**。確認方式：

| 要確認的功能 | 怎麼確認已部署 |
|---|---|
| 產品頁 Banner 文字與顏色 | `GET /api/agent/list_banner/all`，每筆有 `kicker`、`title`、`description`、`kicker_color`、`title_color`、`desc_color` |
| 首頁區塊底色 | `GET /api/agent/home_section/all`，每筆有 `bg_color` |
| 產品類別開關／全站標誌／SEO／色彩 | `GET /api/agent/website/all`，且前台建置後 Header 選單依設定顯示（這幾項不需要 migration，資料只是 website 設定的新鍵，沒部署前台時寫了也不會生效，但不會壞） |
| 首頁精選改「小的在前」 | 需要 migration `2026_10_02_000005` 已執行。沒執行前維持舊規則（大的在前），**不要自己反轉數字** |

> **前台資料何時生效（以 2026-10-01 實測為準）**：前台目前是伺服器即時讀取，後台資料寫入後前台**重新整理就會變**，不需要呼叫 `/publish`。**但本章的新功能需要「前台程式」也部署了新版**（新版才會去讀 `categories`、`logo_*`、`seo` 這些新鍵）。若日後前台改回靜態產生（`npm run generate`），資料改完才需要重新產生。回報老闆時，分兩句：「後台已改」「前台目前狀態（已更新／待部署新版程式／待重新產生）」，不要籠統說「網站已更新」。

### 14.1 產品類別開關（美邁、歌樂各自一組）

**用途**：老闆自己決定前台要不要顯示「盲點偵測、鏡頭、頭枕螢幕、可攜式…」這些類別，不用再請工程師註解程式碼。

**後台畫面**：左側選單「產品管理 → 產品類別開關」。美邁、歌樂各一張卡，每個類別一列：☰ 拖曳把手、↑↓ 按鈕、名稱欄（可改顯示名稱，留空＝預設名稱）、啟用開關。右邊有「前台下拉選單會長這樣」的即時預覽。

**資料存哪裡**：`website` 設定的 `content.categories`，結構：

```json
{
  "clarion": [ {"key":"gl","name":"","on":1}, {"key":"oem","name":"","on":1}, ... ],
  "mm":      [ {"key":"android","name":"","on":1}, ... ]
}
```
- 陣列的**順序就是前台顯示順序**（選單與總覽頁都照這個順序）。
- `on`：1 顯示、0 隱藏。`name`：自訂顯示名稱（最多 40 字，只套用在中文；留空用預設）。
- 整個 `categories` 沒設定過時，前台使用預設：歌樂 8 類、美邁 7 類，其中**頭枕螢幕、可攜式預設是隱藏**。

**所有 key（不可自己新增，也不可改名）**

| 品牌 | key | 預設名稱 | 對應前台網址 | 預設 |
|---|---|---|---|---|
| clarion | gl | 多媒體安卓機 GL | /clarion/gl | 顯示 |
| clarion | oem | 車型專用機 | /clarion/oem | 顯示 |
| clarion | audio | 汽車音響 | /audioAccessories | 顯示 |
| clarion | camera | 鏡頭 | /clarion/camera | 顯示 |
| clarion | din | 車用主機 1/2DIN | /headUnit | 顯示 |
| clarion | dvr | 行車記錄器 | /clarion/dashcam | 顯示 |
| clarion | headrest | 頭枕螢幕 | /headrest | **隱藏** |
| clarion | portable | 可攜式 | /portable | **隱藏** |
| mm | android | 多媒體安卓機 | /mm/me | 顯示 |
| mm | oem | 車型專用機 | /mm/oem | 顯示 |
| mm | frame | 安卓車框 | /carFrame | 顯示 |
| mm | safety | 盲點偵測（影像・安全） | /safety | 顯示 |
| mm | dvr | 行車記錄器 | /mm/dashcam | 顯示 |
| mm | camera | 鏡頭 | /mm/camera | 顯示 |
| mm | fitting | 車用配件 | /fitting | 顯示 |

**關閉後前台會怎樣**：①下拉選單該項消失；②歌樂／美邁總覽頁該區塊與上方快速導覽按鈕消失；③有人直接輸入該類網址（含詳情頁，例如 /headrestDetail/3）顯示「找不到頁面」；④產品資料不會被刪，重新打開就全部回來。

**注意**
- 關閉美邁「安卓車框」（frame）前，要先確認首頁的「車型查詢」入口怎麼處理，否則首頁查詢會連到找不到的頁面。
- 頁尾「產品」欄、/products/ 三個分類頁、sitemap.xml 目前**不受**這個開關影響（要連動請工程師另外接）。
- 這個開關只管「前台顯不顯示」，**不是**商品本身的上下架。商品上下架仍用各商品的 `status`。

**Agent 怎麼做**（流程見 16.2）：`GET /api/agent/website/all` 讀出整份 content → 取 `content.categories`（沒有就用上表預設自己組出來）→ 修改 `on`／`name`／順序 → 把**完整的 `categories`（clarion 與 mm 兩個陣列、每一類都要有）**放回 `content` 再 PATCH 整份 website。**不要只送其中一個品牌或只送要改的那幾類**：這個欄位是整個取代，漏掉的類別會回到預設狀態。

### 14.2 全站標誌圖片（Logo、分頁小圖示）

**用途**：換前台左上角標誌、頁尾標誌、瀏覽器分頁小圖示，不用改程式。

**後台畫面**：「系統設定 → 網站基本設定」往下找「全站標誌圖片」，6 格，每格有目前圖片預覽、規格、用途說明、「選取／上傳圖片」、「還原預設」。

**資料存哪裡**：`website` 設定的 `content`，6 個鍵，值是圖片網址；**空字串或沒有＝前台用內建原圖**。

| 鍵 | 後台標題 | 用在前台哪裡 | 建議規格 |
|---|---|---|---|
| `logo_favicon` | ① 瀏覽器分頁小圖示 | 瀏覽器分頁標題旁的小圖、手機加到主畫面的圖示 | **正方形 512×512 PNG**。系統不會自動縮圖，由瀏覽器縮小，所以圖形要簡單、四周留一點邊 |
| `logo_cobrand_dark` | ② 左上角標誌（歌樂 × MM・白底用） | 白底表頭左上角（首頁捲動後、MM 專區、經銷據點等共用頁） | **橫式約 1750×300**，SVG 最佳，或透明背景 PNG；**深色字** |
| `logo_cobrand_white` | ③ 左上角標誌（歌樂 × MM・透明浮動用） | 首頁一開始表頭透明、浮在大 Banner 上時 | 同②尺寸；**白色字**，透明背景 |
| `logo_clarion_dark` | ④ 歌樂專區標誌（白底用） | 進入 /clarion 開頭的歌樂專區頁面，白底表頭 | **橫式約 1260×330**，SVG 或透明 PNG；**深色字** |
| `logo_clarion_white` | ⑤ 歌樂專區標誌（透明浮動用） | 歌樂專區頁面表頭透明時 | 同④尺寸；**白色字**，透明背景 |
| `logo_footer` | ⑥ 頁尾標誌 | 全站最下方深色頁尾左側 | **橫式約 1750×300**；**白色字**，透明背景 |

- 圖片一律要**透明背景**（PNG／SVG），不要帶白底或黑底方塊，否則在不同底色上會露出方塊。
- 上傳後表頭標誌的**顯示高度由程式固定**（歌樂單標 24px、聯名標 30px），所以長寬比和原本接近最安全；比例差太多會看起來偏大或偏小。
- MM 單獨標誌目前網站沒有使用處，所以沒有開放。
- 品牌規則（歌樂為主、顏色與留白）以《品牌標誌使用規則》為準，**LOGO 一律用原廠／VIS 原檔，不要重畫、不要加效果**。

**Agent 怎麼做**（流程見 16.1）：先 `POST /api/agent/upload`（folder 建議 `Logo`）取得網址 → `PATCH /api/agent/website` 把對應鍵寫進 content → 回讀確認 → 重新整理前台確認。**還原預設**＝把該鍵設成 `""`。

### 14.3 SEO／GEO 設定

**用途**：Google 搜尋結果看到的標題與說明（SEO），以及 ChatGPT、Gemini 等 AI 讀取的公司資料與問答（GEO）。

**後台畫面**：「系統設定 → SEO／GEO 設定」，五個區塊：①公司基本資料 ②每頁搜尋標題與說明（有 Google 搜尋結果預覽與字數提示）③分享圖片 ④AI 常見問答 ⑤進階（結構化資料開關、AI 爬蟲開關、驗證碼）。**每一欄留空＝前台維持原本的文字**。

**資料存哪裡**：`website` 設定的 `content.seo`：

```json
{
  "company_zh": "", "company_en": "", "phone_intl": "",
  "addr_region": "", "addr_locality": "", "addr_street": "",
  "og_image": "",
  "gsc": "", "bing": "",
  "flags": {"org":1, "product":1, "ai_bots":1},
  "pages": { "/clarion/overview": {"title":"…","description":"…"} },
  "faq": [ {"q":"…","a":"…"} ]
}
```

| 欄位 | 意思 | 限制與寫法 |
|---|---|---|
| company_zh／company_en | 公司中文／英文全名，給 Google／AI 讀 | 120 字內。**要和 Google 商家、Facebook 一個字都不差**，差一字會被當成兩間公司。預設：美邁車用電子有限公司／MEIMAI Vehicle Electronics Co., Ltd. |
| phone_intl | 國際格式電話 | 預設 +886-3-2170098。網頁上顯示的電話在 website 的 `tel`，兩者要是同一支號碼 |
| addr_region／addr_locality／addr_street | 縣市／區／路段門牌 | 預設 桃園市／桃園區／國信街35號 |
| og_image | 預設分享圖（貼到 LINE、Facebook 看到的圖） | **1200×630**、JPG／PNG、1MB 內；文字與主體放中間，四周各留 10% 邊。沒設定＝用現有歌樂分享圖。商品頁等自己有分享圖的頁面仍用自己的 |
| gsc／bing | Google Search Console／Bing 網站驗證碼 | 只貼 `content=` 引號內的那串碼。沒填不輸出 |
| flags.org | 公司結構化資料（Organization）開關 | 1 開／0 關，**建議一直開著** |
| flags.product | 商品結構化資料（Product）開關 | 同上；有價格才會輸出價格 |
| flags.ai_bots | 允許 AI 爬蟲讀取網站 | 0＝在 robots.txt 擋掉 GPTBot、ClaudeBot、Google-Extended、PerplexityBot 等；**不建議關**；robots.txt 由前台伺服器依這個設定動態產生，若前台有快取或是靜態產生，要等快取過期或重新產生才生效 |
| pages | 逐頁覆蓋標題與說明，鍵是前台網址路徑 | 見下表。標題建議 10～30 字；說明建議 60～110 字（繁中超過約 110 字 Google 會截斷）。寫「誰、賣什麼、哪裡買」。**不要寫「水貨」等攻擊字眼，不要堆關鍵字** |
| faq | AI 常見問答（顯示在「關於我們」頁並同步產生 FAQPage 結構化資料） | 最多 30 組，每組 q（200 字內）與 a（1500 字內）**兩個都要有**；沒有任何一組＝用網站內建 3 則。**這些是頁面上看得到的文字，不可寫與事實不符的內容**（Google 規定問答要是可見內容） |

**可逐頁設定的路徑**（`pages` 的鍵；其他路徑目前不吃覆蓋）：
`/`（首頁）、`/clarion/overview`、`/mm/overview`、`/partner`、`/about`、`/qa`、`/download`、`/cases`、`/clarion/gl`、`/clarion/oem`、`/audioAccessories`、`/clarion/camera`、`/headUnit`、`/clarion/dashcam`、`/mm/me`、`/mm/oem`、`/carFrame`、`/safety`、`/mm/dashcam`、`/mm/camera`、`/fitting`、`/products/android-headunit`、`/products/dash-cam`、`/products/car-audio`。
商品**詳情頁**的標題與說明來自各商品自己的欄位（商品名稱與 `memo_in`，詳見 5.1），不在這裡設定。

**標題的後綴**：多數頁面會自動在標題後接上「｜Clarion 歌樂 台灣官方授權總經銷｜美邁車用電子」，所以你寫的標題**不要再重複寫網站名稱**（首頁、關於我們等少數頁面不接後綴，前台程式已處理）。

**Agent 怎麼做**（流程見 16.3）：`GET website/all` 讀整份 → 修改 `content.seo`（保留沒動到的子欄位，**整個 `seo` 物件完整送回**）→ PATCH → 回讀 → 重新整理前台後，用「檢視原始碼」確認 `<title>`、`<meta name="description">`、`application/ld+json` 有輸出。Agent 與後台畫面走**同一支清洗程式**：超過長度會被截斷、不合格式的顏色與空白欄位會被丟掉，**而且不會報錯**，所以寫入後一定要讀回確認，並自己遵守上表的字數限制。

### 14.4 產品頁 Banner（list_banner）新增：左側文字與顏色

**後台畫面**：「產品頁 Banner 管理」。每個位置一列：頁面名稱、前台網址、目前圖片縮圖；點「設定」進整頁表單：①上傳電腦版／手機版圖片（旁邊有安全區示意）②左側文字（小標題、大標題、說明）③三組文字顏色。表單內有 Banner 示意圖與各種距離標示。**不用再把文字做進圖片裡**。

**欄位**（`PATCH /api/agent/list_banner/{page_key}/{type_key}`，只送要改的，沒送的沿用）

| 欄位 | 意思 | 限制 |
|---|---|---|
| img／img_mobile | 電腦版／手機版背景圖 | 電腦 **1920×480（4:1）**、手機 **1080×608（16:9）**；JPG／WebP；300KB 內。**圖片左半邊留白**，前台會把文字疊在左邊 |
| kicker | 小標題（大標題上方一行小字，例如英文品牌詞） | 100 字內；空＝用前台原本的文字 |
| title | 大標題 | 100 字內；空＝原本的文字 |
| description | 說明文字 | 500 字內；空＝原本的文字 |
| kicker_color／title_color／desc_color | 三組文字顏色 | **只接受 `#RRGGBB`**（例 `#FFFFFF`），空＝預設色 |

- 這些文字**只套用在中文**，英文版維持原本翻譯。
- `GET /api/agent/list_banner/all` 列出所有可設定位置，migration 已把前台原本的文字（小標題、大標題、說明）填進資料庫，所以讀出來就是前台現在看到的字，**先看現況再改**，不要憑空想文字。
- 深色底圖請用淺色字、淺色底圖用深色字，改完要確認可讀。

### 14.5 首頁滿版區塊（home_section）新增：底色與簡單模式

首頁有 3 個可自己編輯的滿版區塊（zone1 歌樂滿版區、zone2 美邁滿版區、zone3 尾端 CTA 區），後台「首頁滿版區塊管理」上方有**首頁模擬圖**，標出 7 個區塊的位置，可編輯的 3 個有藍框。

| 欄位 | 意思 |
|---|---|
| content | 區塊的 HTML 文字。後台「簡單模式」會產生固定結構（見下），Agent 照同樣結構寫即可 |
| img／img_mobile | 區塊背景圖（電腦／手機）。建議電腦 **2560×1440（16:9）**、手機 **1080×1920（9:16）**，主體放中間，文字區域保持乾淨 |
| bg_color | 區塊底色 `#RRGGBB`（沒圖片、或圖片載入前的底色）；空＝維持原本的全站深色底 |

簡單模式產生的 HTML 結構：

```html
<div class="cms-block cms-align-left" data-simple="…">
  <p class="cms-k" style="color:#…">小標題</p>
  <h2 class="cms-t" style="color:#…">大標題</h2>
  <p class="cms-d" style="color:#…">說明</p>
  <a class="cms-b" style="background:#…" href="https://…">按鈕文字</a>
</div>
```
`cms-align-left｜center｜right` 是對齊方向。**不要自己加 `<script>`、全域 `<style>`、`<h1>`**。`data-simple` 是後台回填表單用的記錄，Agent 若自己寫 HTML 可以不帶（後台再開啟時會進「進階模式」，不會壞）。要清除區塊：`PATCH` 送 `{"content":"","img":"","img_mobile":"","bg_color":""}`。

### 14.6 全站色彩（網站基本設定）

`website` 的 `content` 另有 4 個顏色鍵，**只接受 `#RRGGBB`，空＝預設**：

| 鍵 | 意思 | 預設 |
|---|---|---|
| theme_dark | 全站深色底（首頁區塊底、產品頁橫幅、導覽等所有深色背景） | #0D1016 |
| theme_footer | 頁尾底色（沒填跟著 theme_dark） | #0D1016 |
| theme_accent | 重點色（按鈕、連結、選取狀態；滑過與淡色版自動計算） | #007ABE |
| theme_bg2 | 淺灰區塊底色 | #F5F7FA |

深色底請選**深色系**（上面的字是白色，太亮會看不清楚）；淺灰區塊請選淺色。目前**不包含**文字顏色（白字／深色字）與純白底。MM 專用橘 #F28729 與藍紫 #AF47D2 只用在 MM 專頁，不要設成全站重點色。

### 14.7 資源下載：只貼連結（不再上傳檔案）

- 檔案**都放在 Google 雲端**，後台「檔案管理」只有「貼連結」，沒有上傳按鈕。`resource.url` 填 Google 雲端的分享連結，或 YouTube 影片連結；前台依網址自動顯示「下載」（Google 雲端、一般網址）或「觀看」（YouTube／youtu.be）。
- **Google 雲端的檔案必須把「一般存取權」設成「知道連結的任何人」**，否則客人點開會被擋。Agent 無法替老闆設定雲端權限，只能提醒。
- 後台版面：左邊分類、右邊該分類的檔案；「分類管理」是整頁表單，右側有前台手風琴展開的預覽與範例。
- 分類欄位：name（分類名稱）、memo（簡述，顯示在分類標題下）、status。檔案欄位：resource_category_id、name、url、status。

### 14.8 排序規則統一（重要，和舊版第 6 章不同處）

**統一規則：數字小的排在前面；有「置頂」的商品，置頂的一組永遠在一般的前面，兩組各自依數字排。** 後台所有可排序的頁面，操作方式一樣：☰ 拖曳整列、↑↓ 移一格、或直接改數字（自動儲存）。

| 資源 | 前台順序 | 後台列表順序 | 備註 |
|---|---|---|---|
| car_media | 置頂在前 → `sort` 小→大 → 名稱 A→Z | 同前台 | 多媒體機依「類型」＋置頂分組排序 |
| car_head_unit、car_dashcam、car_camera、car_audio_accessories、car_headrest、car_portable、car_fitting | 置頂在前 → `sort` 小→大 → id | 同前台（原本只依建立時間，已改） | **後台現在也有排序介面** |
| car_brand | `sort` 小→大 → 名稱 | 啟用在前 → `sort` → 名稱 | 原本後台只依名稱排，已補 sort |
| banner、dealer、resource_category | 啟用的依 `sort` 小→大 | 啟用在前 → `sort` | 排序只在「啟用」或「停用」同一組內調整 |
| resource | 依 `sort` 小→大 | 依分類分組 | 只能在同一個分類內移動 |
| recommend_product（首頁精選） | **`sort` 小→大**（已和其他頁統一） | 同前台 | 原本是大的在前；部署時 migration 會把既有數字反轉，前台順序不變 |
| install_case | 置頂 → 安裝日期新→舊 → `sort` 大→小 | — | 自動排序，無手動排序介面（和其他相反，維持不變） |
| car、car_frame、car_blind_spot | 依名稱字母 | 依名稱字母 | 無手動排序 |
| car_blind_spot_format | `sort` 小→大 | 啟用 → `sort` | 依車廠分組 |
| 產品類別開關 | 陣列順序 | 陣列順序 | 見 14.1 |

新增商品時，`sort` 自動放在目前最大值＋1（排最後）。Agent 調整排序：`PATCH /api/agent/{資源}/all/sort` 送 `{"items":[{"id":12,"sort":1},{"id":7,"sort":2}]}`，**一組（同為置頂或同為一般）一起送**，數字連續（1、2、3…）最不會出錯。

### 14.9 後台所有頁面的共同畫面規則（Agent 要懂，才能和老闆講同一種話）

- **列表 → 「設定／編輯」→ 整頁表單**：新增與編輯都是整頁（不是彈窗），圖片上傳放最前面，有預覽。
- **彩色提示卡**：綠＝做法重點、紅／橘＝不能做或容易錯、藍＝規格尺寸。
- **固定儲存列**：頁面最下方一直黏著「儲存」按鈕。
- **iPhone 風格開關**：綠＝啟用／顯示，灰＝停用／隱藏。
- **選填就不要必填**：Banner 的名稱、連結網址都是選填。
- 後台的「說明文字」是寫給不懂程式的人看的，**Agent 回報時也用同樣的白話**，附預覽或範例。

---

## 15. 前台 ↔ 後台 ↔ Agent 連結總表（每個設定「改哪裡 → 存哪裡 → 前台哪裡看到」）

讀法：**後台畫面**＝人點的地方；**Agent 資源**＝同一件事 Agent 用的 API；**前台公開 API**＝網站建置時抓資料的來源（驗證「訪客看到什麼」打這個）；**前台位置**＝網站上實際出現的地方；**生效**＝改完要做什麼才看得到。

> 共同規則：以 2026-10-01 實測，前台是伺服器即時讀取，**後台資料改完前台重新整理就會變**（不需要 `/publish`）。新功能要生效的前提是：前台程式已部署新版。若日後前台改回靜態產生，才需要重新 `npm run generate`（`POST /api/agent/publish` 在工程師設定後可觸發）。

| # | 想改的東西 | 後台畫面 | Agent 資源／寫法 | 資料存放 | 前台公開 API | 前台位置 |
|---|---|---|---|---|---|---|
| 1 | 首頁大輪播圖 | Banner管理 | `banner` 標準 CRUD | banner 表 | `GET /api/banner` | 首頁最上方輪播 |
| 2 | 首頁精選商品 | 首頁精選商品 | `recommend_product` | recommend_products 表 | `GET /api/recommend_products` | 首頁「精選商品」區 |
| 3 | 首頁安裝案例 | 安裝案例（勾「顯示在首頁」，最多 3 筆） | `install_case` | install_case 表 | `GET /api/install_cases?home=1` | 首頁精選商品下方「安裝實績」；完整列表 /cases |
| 4 | 首頁三個滿版區塊 | 首頁滿版區塊管理 | `home_section`（zone1／2／3） | home_sections 表 | `GET /api/home_section` | 首頁中段與尾端滿版區 |
| 5 | 各產品頁頂端 Banner（圖＋左側文字＋顏色） | 產品頁 Banner 管理 | `list_banner`（page_key／type_key） | list_banners 表 | `GET /api/list_banner?page=…` | 各產品列表、總覽、品牌故事等頁最上方 |
| 6 | 產品（九類）新增、圖、內文、置頂、啟用、排序 | 產品管理 → 各類 | `car_media` 等標準 CRUD＋top＋status＋all/sort | 各自的表 | `GET /api/multimedia`、`/dashcam`、`/camera`、`/fitting`、`/blindspot`、`/head_unit`、`/audio_accessories`、`/headrest`、`/portable` | 對應列表頁與詳情頁 |
| 7 | 安卓車框、車廠、車款 | 產品管理 → 安卓車框／汽車品牌／汽車車款 | `car_frame`、`car_brand`、`car` | 各自的表 | `GET /api/carframe`、`/car`、`/search` | 車框頁與車型查詢 |
| 8 | **前台選單與總覽頁顯示哪些類別、順序、名稱** | **產品管理 → 產品類別開關** | `website` → `content.categories`（14.1） | settings(type=website) | `GET /api/website` | 表頭下拉選單、歌樂／美邁總覽頁區塊與快速導覽；關閉的類別網址回 404 |
| 9 | **Logo、分頁小圖示** | **網站基本設定 → 全站標誌圖片** | `website` → `content.logo_*`（14.2） | settings(type=website) | `GET /api/website` | 表頭、頁尾、瀏覽器分頁圖示 |
| 10 | **Google 搜尋標題說明、公司資料、分享圖、AI 問答、驗證碼** | **系統設定 → SEO／GEO 設定** | `website` → `content.seo`（14.3） | settings(type=website) | `GET /api/website` | 每頁 `<head>`（title／description／og）、結構化資料 JSON-LD、關於我們頁問答、robots.txt |
| 11 | 全站底色、重點色、頁尾色 | 網站基本設定 → 全站底色 | `website` → `content.theme_*`（14.6） | settings(type=website) | `GET /api/website` | 全站深色區塊、按鈕、頁尾 |
| 12 | 頁尾版權、地址、電話、Email、社群連結 | 網站基本設定 | `website` → `copyright／address／tel／email／facebook／instagram／youtube` | settings(type=website) | `GET /api/website` | 頁尾；Organization 結構化資料也會取 email 與社群連結 |
| 13 | 常見問題頁、品牌故事頁內文 | 系統設定 → 常見問題／關於我們 | `qa`、`about`（整份 HTML） | settings | `GET /api/question`、`/api/about` | /qa、/about |
| 14 | 經銷據點 | 經銷據點管理 | `dealer` | dealer 表 | `GET /api/partner` | /partner |
| 15 | 資源下載（分類與檔案連結） | 資源下載管理 → 分類管理／檔案管理 | `resource_category`、`resource` | 各自的表 | `GET /api/resource` | /download |
| 16 | 車框浮水印 | 系統設定 → 浮水印設定 | `watermark` | 檔案＋設定 | 無（只影響存檔時蓋在車框圖上） | 車框圖片 |

**同一份 `website` 設定裝了很多東西**（第 8～12 項都在 `content` 裡），所以**每次寫入都先 `GET` 整份、只改自己的鍵、整份送回**，最不會誤傷別項。`categories` 與 `seo` 是巢狀物件，改的時候要把整個物件完整帶回。

### 15.1 前台怎麼取得這些設定（給想知道原理的人）

- 前台每頁載入時，用同一個資料請求（名稱 `website-info`）讀 `/api/website`；表頭、頁尾、`app.vue`、SEO 與結構化資料共用這一份，所以不會重複打 API。
- 伺服器端渲染時這份資料會直接寫進送出的 HTML，所以搜尋引擎與 AI 爬蟲看得到。若前台有快取（例如 CDN），後台改完要等快取過期才看得到。
- 沒設定的欄位一律退回程式裡的預設值，所以**後台什麼都不填，網站就和以前一模一樣**。

---

## 16. 標準作業流程（新增項目，照做不要自己發明）

> 通用開頭（每個流程都先做）：①確認金鑰有對應 scope（website／list_banner／home_section 在 `settings`、`banners`；上傳在 `files`）；②確認該功能已部署（14.0）；③先 `GET` 讀現況並**記下改前的值**（回報時要寫前→後）。通用結尾：④寫入後**讀回驗證**；⑤打前台公開 API 確認；⑥回報「後台已改／前台待重新產生」。

### 16.1 換 Logo 或分頁小圖示

1. 準備圖片：照 14.2 的規格（透明背景、白字／深色字別弄反、SVG 或高解析 PNG）。**LOGO 一律用原廠／VIS 原檔，不要重畫。**
2. `POST /api/agent/upload`（`file=@檔案`、`folder=Logo`）→ 取得回傳的 `url`，**原封不動使用**。
3. `GET /api/agent/website/all`，取出 `item.content`。
4. 在 `content` 裡只改對應的鍵（例如頁尾標誌 `logo_footer`），其他鍵保持原樣。
5. `PATCH /api/agent/website`，body `{"content": {整份}}`。
6. 回讀確認該鍵是剛才的網址；對該網址送 GET，要 200 且 `Content-Type` 是 `image/*`。
7. 回報：改了哪一格、舊網址→新網址；「前台重新整理即可看到（若有快取要稍等）」。
8. 要還原：把該鍵設成 `""` 再送一次。

### 16.2 開啟或關閉產品類別、調整順序、改名

1. 確認老闆要動的是哪個品牌（歌樂／美邁）與哪幾類（用 14.1 的 key）。
2. `GET /api/agent/website/all`，看 `content.categories`；沒有就用 14.1 表的預設自己組出完整的兩個陣列。
3. 修改：`on` 設 1／0；要改順序就**調整陣列順序**；要改名就改 `name`（留空＝預設名稱）。**兩個品牌、每一類都要在陣列裡**。
4. 若要關的是美邁 `frame`（安卓車框），先問老闆首頁車型查詢要怎麼辦。
5. `PATCH /api/agent/website`，整份 content（含完整 categories）送回。
6. 回讀確認；告知老闆：關閉的類別，選單、總覽頁區塊、直接輸入的網址（404）都會一起消失，資料沒刪；前台重新整理即生效。

### 16.3 設定某一頁的 Google 標題與說明（SEO）

1. 先決定頁面（14.3 的路徑清單）。**先看這頁目前在 Google 上的樣子與前台目前的標題說明**，不要憑空寫。
2. 寫標題（10～30 字，不重複網站名稱）、說明（60～110 字，講清楚「誰、賣什麼、哪裡買」，自然帶入需求詞如「安卓車機」「車用多媒體主機」，**不要堆關鍵字、不要寫攻擊同業或「水貨」字眼**，不要寫沒有根據的功效或保證）。
3. 與老闆確認文字（這是對外公開的文字）。
4. `GET website/all` → 在 `content.seo.pages` 加入或更新 `"/路徑": {"title":"…","description":"…"}`，**其他頁面的設定不動**。
5. `PATCH website`（整份 content）。
6. 回讀確認；重新整理前台後，用檢視原始碼確認 `<title>` 與 `description` 是新的；用 Google 搜尋結果預覽工具或 Search Console 觀察。
7. 要還原：把該路徑整個刪掉（或標題、說明都設空字串）。

### 16.4 更新公司資料（GEO）或 AI 問答

1. 公司名稱、電話、地址要與 Google 商家、Facebook **逐字一致**；與老闆確認後才改。
2. `content.seo` 的 `company_zh／company_en／phone_intl／addr_*`；頁尾顯示用的電話地址則在 `content.tel／address`（兩邊要同一個號碼與地址）。
3. 問答：`content.seo.faq` 陣列，每組 `{"q":"…","a":"…"}`；**內容要是事實**（例如總經銷身分、如何確認公司貨、保固怎麼算），因為會同時顯示在「關於我們」頁。整個陣列完整送回（順序＝顯示順序）。
4. 回讀、重新整理前台後，確認關於我們頁看得到問答，原始碼有 `FAQPage`。

### 16.5 設定分享圖（LINE／Facebook 預覽圖）

1. 圖 1200×630、JPG／PNG、1MB 內，文字放中間、四周留 10% 邊。
2. 上傳（folder=`SEO`）取得 url → 寫入 `content.seo.og_image`（整份送回）。
3. 驗證：用 Facebook 分享偵錯工具或貼到 LINE 看預覽（有快取，可能要等或重新抓取）。

### 16.6 調整產品頁 Banner（圖片＋左側文字）

1. `GET /api/agent/list_banner/all`，找到 `page_key／type_key`（對照 7.16 表），看目前圖片與文字。
2. 圖片：電腦 1920×480、手機 1080×608，**左半邊留白**，字不要烙在圖上（文字用欄位）。上傳取得 url。
3. 文字：`kicker`（小標）、`title`（大標）、`description`（說明）；顏色三個 `#RRGGBB`。深色底用淺色字。
4. `PATCH /api/agent/list_banner/{page_key}/{type_key}`，只送要改的欄位。
5. 回讀；重新整理前台後在對應頁面看左側文字與顏色。

### 16.7 調整首頁滿版區塊

1. `GET /api/agent/home_section/all` 看三個區塊現況。
2. 圖：電腦 2560×1440、手機 1080×1920，主體置中；底色 `bg_color` `#RRGGBB`（圖片載入前與沒圖時的底色）。
3. 文字用 14.5 的 HTML 結構。
4. `PATCH /api/agent/home_section/{zone1|zone2|zone3}`，只送要改的。
5. 回讀；重新整理前台後看首頁。

### 16.8 調整任何列表的排序

1. `GET /api/agent/{資源}/all?page=…`（翻到 `last_page`）列出現況（id、sort、置頂、狀態）。
2. 依 14.8 決定要排的那一組（置頂組／一般組分開）；數字從 1 連續編號。
3. `PATCH /api/agent/{資源}/all/sort` 送 `{"items":[{"id":…,"sort":1},…]}`。
4. 回讀確認順序；打前台公開 API 確認訪客端順序。

### 16.9 新增一個商品（九類通用，簡版；完整版見 9.1）

1. 圖：1200×1200、白底或去背、主體 80%、300KB 內。
2. `POST /api/agent/{資源}` 帶 name、img、memo_in（SEO 說明來源，80～120 字純文字）、content（HTML）、is_top、status 等（欄位見 7.x）。新增後 `sort` 自動排最後。
3. 取回傳的 id，`GET` 讀回；打前台公開 API 確認；要顯示在首頁精選另用 `recommend_product`。
4. 若該類別在「產品類別開關」是關閉的，**商品存在但前台不會顯示**，回報時要提醒老闆。

### 16.10 資源下載：新增一個檔案連結

1. 老闆把檔案放在 Google 雲端，並設定「一般存取權：知道連結的任何人」。
2. 找到或新增分類（`resource_category`）。
3. `POST /api/agent/resource`：`resource_category_id`、`name`、`url`（完整分享連結或 YouTube 連結）、`status`。
4. 回讀；前台 /download 依網址顯示「下載」或「觀看」。連結打不開就是雲端權限沒開。

---

## 17. 圖片規格總表（全站所有圖片，一張表看完）

| 圖片 | 資源／欄位 | 電腦版 | 手機版 | 格式與大小 | 製作重點 |
|---|---|---|---|---|---|
| 首頁輪播 | `banner` img／img_mobile | **1920×1080（16:9）** | **1080×2160（1:2 直式）** | JPG／WebP，300KB 內 | 文字與主體放畫面中間約 60%；最下方約 190px 會蓋深色漸層與「車型查詢」卡片，**底部不要放字**；手機沒傳會用電腦版，兩側約被裁掉 35%，字可能被切，建議另做 |
| 產品頁 Banner | `list_banner` img／img_mobile | **1920×480（4:1）** | **1080×608（16:9）** | JPG／WebP，300KB 內 | **左半邊留白**（前台疊文字），文字用欄位填，不要烙在圖上 |
| 首頁滿版區塊 | `home_section` img／img_mobile | **2560×1440（16:9）** | **1080×1920（9:16）** | JPG／WebP | 主體置中；文字區域乾淨；另可設 `bg_color` 底色 |
| 商品列表與主圖 | 九類商品 `img` | 1200×1200 正方形（電腦手機共用） | 同左 | 白底或去背 PNG／WebP，300KB 內 | 主體置中佔 80%，等比縮放不裁切 |
| 安卓車框 | `car_frame` imgArr | 見 4.5 | 見 4.5 | 見 4.5 | 浮水印存檔時才蓋上 |
| 安裝案例 | `install_case` img | 1600×1000（16:10） | 同左 | 300KB 內 | 主體置中，首頁卡／列表卡／內頁都是 16:10 裁切 |
| 左上角標誌（白底／透明浮動） | `website` logo_cobrand_dark／logo_cobrand_white | 橫式約 1750×300 | 同左 | SVG 最佳，或透明 PNG | 白底用深色字、透明浮動用白色字；背景必須透明 |
| 歌樂專區標誌（白底／透明浮動） | logo_clarion_dark／logo_clarion_white | 橫式約 1260×330 | 同左 | SVG 或透明 PNG | 同上 |
| 頁尾標誌 | logo_footer | 橫式約 1750×300 | 同左 | SVG 或透明 PNG，白色字 | 放在深色頁尾 |
| 瀏覽器分頁小圖示 | logo_favicon | 512×512 正方形 | 同左 | PNG | 圖形簡單、四周留邊；不會自動縮成多尺寸 |
| 分享預覽圖 | `seo.og_image` | 1200×630 | 同左 | JPG／PNG，1MB 內 | 文字與主體放中間，四周各留 10% |
| 浮水印 | `watermark` | PNG 透明，20×10～4000×4000 | — | 5MB 內 | 只影響之後存檔的車框圖 |

通則：圖片先壓縮再上傳（300KB 內是目標）；檔名用英文與數字；**不要移動、改名、刪除檔案庫的資料夾**；上傳回傳的網址原封不動使用；圖片要有清楚的主體與足夠對比；**LOGO 只用原廠／VIS 原檔**。

---

## 18. 部署前後檢查清單（給工程師與 Hermes）

**後端部署**
1. `php artisan migrate`（本次新增 `2026_10_02_000001`～`000005`：list_banner 文字與顏色、前台原文字種子、home_section 底色、首頁精選排序反轉）。
2. `php artisan config:clear`、`view:clear`、`route:clear`（新增了 `config/product_categories.php`、`config/seo_pages.php`、路由與選單）。
3. 登入後台逐頁點開：產品類別開關、網站基本設定（全站標誌圖片）、SEO／GEO 設定、產品頁 Banner、首頁滿版區塊、各產品頁（列表要有 ☰ 與 ↑↓）、資源下載、經銷據點。**哪一頁空白就看瀏覽器 F12 Console 的紅字**。
4. 在每頁做一次：改值→儲存→重新整理，確認有存進去。

**前台部署**
1. 部署前台新版程式（重新 build；若前台是靜態產生就 `npm run generate`），API 位址指到正式後台。
2. 檢視原始碼確認：`<title>`、`description`、`application/ld+json`、（若填了驗證碼）`google-site-verification`；`/robots.txt` 內容；`sitemap.xml`。
3. 逐一確認：選單依開關顯示、關閉的類別網址是 404、Logo 與頁尾標誌已換、分頁小圖示已換。

**回報格式（Hermes 每次操作後）**：做了什麼（資源＋鍵）→ 改前→改後 → 讀回結果 → 前台公開 API 確認結果 → 前台目前狀態（已更新／待部署新版程式／待重新產生）→ 不確定或待老闆決定的事。

---

## 19. 10/01 真實環境 debug 後補充（部署後生效）

- `website` 的 PATCH 合併規則加強：頂層鍵沒送就沿用（原本第 129 項）；**`seo` 再往下合併一層**——只送 `{"content":{"seo":{"company_en":"Meimai"}}}` 不會清掉 `company_zh`／`pages`／`faq`；`seo.pages` 以網址為單位合併（送 `/about` 只改 `/about`）；`seo.flags` 逐個旗標合併；`seo.faq` 是整份清單，有送就整份取代（要刪一題就送少一題的完整清單）。`categories` 只送 `clarion` 不會清掉 `mm`，但每個品牌的清單本身是整份取代（順序＝清單順序，缺的類別前台會用預設補在後面）。
- **保守做法仍然正確**：先 `GET /website/all` 整份、只改自己的鍵、整份送回，部署前後都安全。上面的合併只是讓「少送」不再出事。
- `seo.pages` 從 API 讀出來一律是物件 `{}`（即使沒設定任何頁），不會再是 `[]`。
- `categories` 只接受 `config/product_categories.php` 裡有的 key；不認識的 `logo_*` 鍵（例如打錯的 `logo_header`）會被丟掉而且**不會報錯**，所以改完請 GET 回來確認有存到。
- 實測確認：產品類別開關、SEO／GEO、全站標誌、14 個排序列表、Agent API 的 GET／PATCH／sort／新增，在真實 Laravel（PHP 8.4＋MariaDB）全部通過。
- 10/01 晚間再補：`dealer.county` 可直接給縣市名稱；`car_media`／`car_blind_spot` 的 `memo_in` 選填；所有列表新增的資料自動排最後；車框 `imgArr` 用 `null` 佔位保留原圖；`seo.pages` 某頁送 `null` 可刪掉覆蓋。完整 SOP 見《Hermes操作指令_前台後台AgentAPI完整流程_v4_20261001.md》。
