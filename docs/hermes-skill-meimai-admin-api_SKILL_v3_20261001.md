---
name: meimai-admin-api
description: 用 Agent API 直接改美邁官網後台（產品、圖片、Banner、導入事例、經銷據點、資源下載、網站設定、產品類別開關、Logo、SEO／GEO、浮水印）。v3（2026-10-01）：新增產品類別開關、全站標誌圖片、SEO／GEO、產品頁 Banner 文字與顏色、首頁區塊底色、全站色彩、排序統一；圖片直接填、PATCH 只送要改的欄位、新增回 id、啟用／置頂用 body 設定、寫完呼叫 publish。API 不通時照舊用瀏覽器。只動後台資料，不碰前台程式。
---

# 美邁官網後台 Agent API 操作規則（v3，2026-10-01；含本機新增功能）

> **更正（2026-10-01 實測）**：前台是即時讀取的伺服器，後台資料寫入後前台立即變（已用 car_dashcam/1 實測並還原）。**不需要呼叫 `/publish`，回 501 或 `needed:false` 都不用回報「待重新產生」**；寫入後讀回驗證＋打前台公開 API 確認即可。本文其他提到 publish／501／「待工程師重新產生」之處，一律以此更正為準。


這份取代 9/29 的舊技能。**舊技能裡「先 GET 再整份送回」「上傳後再填 url 才算完成」「切換前先 GET 現況」「車框沒送的圖會被刪」這四條都已經不適用**，照本份做。

完整說明在《美邁後台與 Agent API 完整操作手冊 v3》（`backend/docs/美邁後台與AgentAPI完整操作手冊_v3_20261001.md`）；第 0 章是「舊版 vs 新版」對照、路徑總表；第 7 章逐模組欄位；第 9 章標準流程；**第 14～18 章是新功能的詳細說明、前後台連結總表、流程、圖片尺寸總表與部署檢查**。

## 鐵律
1. **金鑰只從環境變數 `MEIMAI_AGENT_KEY` 讀**，不寫進檔案、不貼進對話、不印出來。
2. **每次開工先做版本確認**（下一節）。確認是新版才用新行為；是舊版就照手冊第 11 章舊版注意事項，並回報老闆。
3. **API 能做的優先用 API**；API 回 500、連線失敗、或 schema 沒有這個資源，就照舊用瀏覽器操作後台，並回報「已改用瀏覽器」。
4. **只動後台資料。** 前台程式、版面、選單、按鈕文字不是你的工作，也做不到（分工見《網站地圖與前後台對接對照》）。
5. **刪除、停用、大量修改之前先問老闆**，列出「哪幾筆、改什麼」。刪除無法復原；下架一律用 `status=0`，不要刪。
6. **寫入後一定讀回驗證**，回報時附讀回的資料與 `saved_images`，不要只說「完成」。看到 `warnings` 就是沒成功。
7. **資料改完前台不會自動更新。** 寫完呼叫 `POST /api/agent/publish`；回 501 就回報「後台已改、前台待工程師重新產生」，不要說「網站已更新」。

## 開工三步
```bash
B=https://admin.meimai.com.tw/api/agent
H="X-Agent-Key: $MEIMAI_AGENT_KEY"
curl -s -H "$H" $B/me                      # 金鑰與權限；401 就停下來告訴老闆
curl -s -H "$H" $B/schema > /tmp/schema.json
```
版本確認（三樣都有＝新版）：`resources.watermark`、`resources.car_media.docs`、頂層 `image_input`。
再對任一已啟用商品做 `PATCH $B/car_dashcam/{id}/status` 帶 `{"status":1}`：新版回 `{"unchanged":true}`，不會切成停用。若被切成停用，立刻設回 `{"status":1}`，判定為舊版。

## 連線規則
- Base：`https://admin.meimai.com.tw/api/agent`；每個請求帶 `X-Agent-Key`。JSON body 用 `Content-Type: application/json`；傳檔用 multipart。
- 權限群組：`products`、`banners`、`cases`、`dealers`、`resources`、`settings`、`files`（upload）、`publish`。GET 要 `群組:read`，寫入要 `群組:write`。
- 每分鐘最多 120 次；批次先做 1 筆驗證，再做其餘，中間留間隔。
- 前台公開 API（驗證訪客看到什麼，不需金鑰）：`https://admin.meimai.com.tw/api/...`；前台網站 `https://clarion.meimai.com.tw`。

## 端點長相（標準型資源）
| 動作 | 端點 | 新版重點 |
|---|---|---|
| 列表 | `GET /{資源}/all?page=1` | 要翻到 `last_page` 才能說「沒有這筆」 |
| 單筆 | `GET /{資源}/{id}` | 讀回驗證用 |
| 新增 | `POST /{資源}` | 回 `{"message","id","item"}`，直接用 id |
| 修改 | `PATCH /{資源}/{id}` | **只送要改的欄位**，其他自動沿用 |
| 刪除 | `DELETE /{資源}/{id}` | 無法復原；被引用會回 400 說明幾筆在用 |
| 啟用／停用 | `PATCH /{資源}/{id}/status` body `{"status":1}` 或 `{"status":0}` | 直接設定；已相同回 `unchanged` |
| 置頂 | `PATCH /{資源}/{id}/top` body `{"is_top":1}` | 同上（導入事例是 `/pinned` `{"is_pinned":1}`、`/home` `{"is_home":1}`） |
| 排序 | POST／PATCH 帶 `sort`；整批 `PATCH /{資源}/all/sort` body `{"items":[{"id":1,"sort":1},…]}` | `install_case`、`recommend_product` 只能用 all/sort |
| 單份設定 | `GET /{website\|qa\|about}/all`、`PATCH /{website\|qa\|about}` | `website.content` 整份物件取代，欄位一個都不能少 |
| 頁面橫幅 | `GET /list_banner/all`、`PATCH /list_banner/{page_key}/{type_key}` | 只送一張不會清掉另一張 |
| 首頁區塊 | `GET /home_section/all`、`PATCH /home_section/{zone1\|zone2\|zone3}` | 同上 |
| 浮水印 | `GET /watermark/all`、`POST /watermark/{0-3}`（multipart `file`＝PNG）、`DELETE /watermark/{0-3}` 還原 | 只影響之後存檔的車框圖 |
| 上傳 | `POST /upload`（`file` 或 `files[]`、`folder`、`name`） | 要指定資料夾時才用；回 `url` |
| 重新產生前台 | `POST /publish` | 200＝已觸發；501＝工程師未設定，改回報 |
| 稽核 | `GET /audit?resource=&record_id=` | 每次寫入的改前快照 `before`，可照著退回 |

資源名稱（＝後台選單）：`car_brand` 汽車品牌、`car` 汽車車款、`car_frame` 安卓車框、`car_blind_spot` 盲點偵測（適用車款 `car_blind_spot_format/{父id}`）、`car_fitting` 車用配件、`car_media` 多媒體機（type 0 MM 多媒體安卓機／1 MM 車型專用機／2 Clarion GL／3 Clarion OEM）、`car_dashcam` 行車記錄器（brand 0 美邁／1 歌樂）、`car_camera` 鏡頭（brand 同上）、`car_headrest` 頭枕螢幕、`car_portable` 可攜式、`car_audio_accessories` 汽車音響（type 0 喇叭／1 高音／2 重低音／3 擴大機）、`car_head_unit` 車用主機 1/2DIN、`banner` 首頁輪播、`recommend_product` 首頁精選商品、`install_case` 安裝案例、`list_banner` 產品頁 Banner、`home_section` 首頁滿版區塊、`resource_category`／`resource` 資源下載、`dealer` 經銷據點、`website`／`qa`／`about`／`watermark` 系統設定。

## 圖片怎麼給（新版，任何圖片欄位都適用）
擇一直接填進欄位（`img`、`img_mobile`、車框的 `imgArr[群][序]`）：
1. 已在站內的網址 `https://admin.meimai.com.tw/storage/files/1/...`
2. 外部 `https://` 圖片網址（伺服器自己下載存到 `files/1/AgentImport/年月/`；只收 jpg／png／webp／gif，單檔 20MB）
3. `data:image/png;base64,...`
4. multipart 直接帶檔案，欄位名同圖片欄位

圖片庫（CKEditor 插入圖片，photos/1）用 `POST /upload` 加 `type=images`；老闆說「圖片庫／插入圖片」＝images，「檔案庫」＝預設 files；上傳前確認目的地，回傳 `url` 前綴要是 `/storage/photos/1/`（images）或 `/storage/files/1/`（files），不一致就回報。不要再用瀏覽器 Dropzone。
要把檔案放進指定資料夾（例如 `Clarion 2026/GL-700_Ultra_13/...`）才先 `POST /upload` 拿 `url` 再填。
寫入回應看 `saved_images`：網址要是 `https://admin.meimai.com.tw/storage/files/1/...`；有 `warnings` 就是沒存成功。
尺寸（完整表見手冊第 17 章）：商品圖 1200×1200 白底／去背；首頁輪播 **1920×1080＋手機 1080×2160（直式，底部約 190px 不放字）**；各頁頂端橫幅 1920×480＋手機 1080×608（左半留白）；首頁滿版區塊 2560×1440＋手機 1080×1920。

## 安卓車框（最特殊）
- 圖片是 `imgArr[群組][序號]`，每群最多 3 張：`0` 列表主圖（車框）、`1` 車框配件、`2` 實際安裝完工照、`3` 車框概況。
- **PATCH 沒送的格子會保留**；要清某一格送空字串，或 `PATCH /car_frame/{id}/img` body `{"type":"img2","index":0}`（type：img／img1／img2／img3；只清欄位不刪檔）。
- 對不到檔案回 422 並指出哪一格，照訊息修網址。
- 新增前先找車廠 `GET /car_brand/all`、車款 `GET /car/all?car_brand_id=`；沒有再新增（車廠名稱格式「英文 中文」，例 `TOYOTA 豐田`）。
- 前台列表每組只回第 1 張，完整張數看 `GET /car_frame/{id}`。

## 標準流程
**新增商品**：`GET schema` 看必填 → `POST /{資源}`（圖直接填）→ 用回傳 `id` `GET /{資源}/{id}` 讀回 → 打前台 API（例 `GET https://admin.meimai.com.tw/api/dashcam?brand=0`）確認出現 → `POST /publish` → 回報。老闆沒說上架前 `status` 先 0。
**換圖**：`PATCH /{資源}/{id}` body 只有 `{"img":"…"}` → 看 `saved_images` → publish。
**上下架**：`PATCH /{資源}/{id}/status` 帶 `{"status":0}`／`{"status":1}`。
**首頁精選**：`GET /recommend_product/options?product_type=car_dashcam` 找商品 id → `POST /recommend_product` `{product_type, product_id, status}`；排序用 `all/sort`（**v3：小的在前**；但要等 migration `2026_10_02_000005` 執行後才生效，之前仍是大的在前，**不要自己反轉數字**，先問工程師或老闆是否已執行）。停用的商品前台精選自動不顯示。
**導入事例上首頁**：最多 3 筆；先把舊的 `PATCH /install_case/{id}/home` `{"is_home":0}`，再設新的，超過會回 400。
**改排序**：`GET all` 看現況 → 單筆 `PATCH /{id}` 帶 `sort`；整批 `PATCH all/sort`。
**網站設定（v3：裝的東西變多）**：`website.content` 裡除了頁尾資料，還有 `theme_*`、`logo_*`、`categories`、`seo`，**一律先 GET 整份、只改自己的鍵、整份送回**，漏掉的鍵等於刪掉。`GET /website/all` → 改 `content` 裡要改的欄位 → `PATCH /website` `{"content":{整份物件}}`，一個鍵都不能少（工程師部署第 129 項後才可以只送要改的鍵，部署前一律整份送）。qa／about 的 content 是整段 HTML，永遠整份送。公司名稱、地址、電話不要自己改，要先問老闆。


## v3 新增功能速查（先確認已部署再寫入；詳細流程與規格見手冊第 14～18 章）

先確認：`GET /list_banner/all` 每筆有 `kicker／title／description／*_color`、`GET /home_section/all` 有 `bg_color`，才表示後端新版已部署。`categories`、`logo_*`、`seo`、`theme_*` 只是 `website.content` 的新鍵，沒部署前台新版程式時寫了不會生效、但也不會壞。

| 要做的事 | 怎麼做 | 重點 |
|---|---|---|
| **開／關／排序／改名產品類別**（前台選單與總覽頁） | `website.content.categories`：`{"clarion":[{"key","name","on"},…],"mm":[…]}`；陣列順序＝前台順序 | 歌樂 key：gl／oem／audio／camera／din／dvr／headrest／portable；美邁：android／oem／frame／safety／dvr／camera／fitting。**兩個品牌每一類都要在陣列裡，整個物件完整送回**。頭枕螢幕、可攜式預設隱藏。關閉＝選單、總覽頁區塊消失、網址 404，資料不刪。**關美邁 frame 前先問老闆首頁車型查詢怎麼辦。** |
| **換 Logo／分頁小圖示** | 先 `POST /upload`（folder=`Logo`）→ 把 url 寫進 `website.content.logo_favicon／logo_cobrand_dark／logo_cobrand_white／logo_clarion_dark／logo_clarion_white／logo_footer`；還原＝設成 `""` | favicon 512×512 PNG；左上角聯名標約 1750×300、歌樂單標約 1260×330、頁尾約 1750×300；SVG 或透明 PNG；`_dark` 用深色字（白底）、`_white` 用白色字（透明浮動／頁尾）。LOGO 只用原廠／VIS 原檔，不重畫。 |
| **SEO／GEO** | `website.content.seo`：`company_zh／company_en／phone_intl／addr_region／addr_locality／addr_street／og_image／gsc／bing／flags{org,product,ai_bots}／pages{"/路徑":{title,description}}／faq[{q,a}]` | 標題 10～30 字、說明 60～110 字、不重複網站名稱、不堆關鍵字、不寫「水貨」。公司名稱電話地址要與 Google 商家一字不差，先問老闆。faq 要是頁面看得到的事實。**整個 `seo` 物件完整送回**。分享圖 1200×630。Agent 與後台走同一支清洗程式：超長會被截斷、不合格式會被丟掉且不報錯，寫入後務必讀回確認。 |
| **產品頁 Banner 文字與顏色** | `PATCH /list_banner/{page_key}/{type_key}`：`kicker／title／description／kicker_color／title_color／desc_color`（顏色只接 `#RRGGBB`）＋ `img／img_mobile` | 圖 1920×480＋手機 1080×608，左半邊留白，字不要烙在圖上；先 `GET all` 看現有文字（已填入前台原文）；只套用中文。 |
| **首頁滿版區塊底色** | `PATCH /home_section/{zone1\|zone2\|zone3}`：`bg_color`（`#RRGGBB`）＋ `content／img／img_mobile` | 圖 2560×1440＋手機 1080×1920；content 用 `cms-block／cms-k／cms-t／cms-d／cms-b` 結構（手冊 14.5）。 |
| **全站色彩** | `website.content.theme_dark／theme_footer／theme_accent／theme_bg2`（`#RRGGBB`，空＝預設） | 深色底要選深色系；預設 #0D1016／#0D1016／#007ABE／#F5F7FA。 |
| **資源下載** | `resource.url` 只貼連結：Google 雲端分享連結或 YouTube | 雲端要設「知道連結的任何人」（只能提醒老闆，你無法設定）；前台自動顯示「下載」或「觀看」。 |
| **排序** | `PATCH /{資源}/all/sort`，置頂組與一般組各自送、數字 1 開始連續 | 規則：小的在前；置頂永遠在一般前面。8 個產品頁後台現在也有排序介面。 |

前台資料何時生效：以 2026-10-01 實測，前台即時讀取、重新整理即變，不需要 `/publish`；但**新功能需要前台程式也部署新版**才會去讀新鍵。回報時分開說「後台已改」與「前台目前狀態」。

## 出錯時
| 回應 | 意思 | 怎麼辦 |
|---|---|---|
| 401 | 金鑰無效或停用 | 告訴老闆，不重試 |
| 403 | 這把金鑰沒有該權限（訊息寫缺 `xxx:write`） | 告訴老闆 |
| 404 | 端點或 id 不存在 | 先看 schema 的 endpoints |
| 400 | 業務規則擋下（被引用不能刪、首頁超過 3 筆） | 照訊息處理，不要繞過 |
| 422 | 缺欄位、格式錯、圖片對不到檔案 | 照 `message` 補，不要亂填 |
| 429 | 超過每分鐘上限 | 等一分鐘 |
| 501 | publish 未設定觸發網址 | 回報「後台已改、前台待重新產生」 |
| 500 | 系統異常 | 不重複重試；記下請求，改用瀏覽器，回報老闆與工程師 |

## 禁止
不貼金鑰；不直接動資料庫與檔案管理員的資料夾（改名、搬移、刪除）；不用刪除代替下架；內文不放 `<script>`、全域 `<style>`、寫死顏色字型；不把「後台已改」說成「網站已更新」；不確定欄位意思先讀 schema 的 `docs` 再問老闆，不猜。

## 回報格式
```
完成：新增 MM-DVR 4CH（car_dashcam #52，status=0 待你確認上架）
圖片：saved_images.img = https://admin.meimai.com.tw/storage/files/1/AgentImport/202610/xxx.webp
讀回：name／brand／memo_in 正確；前台 GET /dashcam?brand=0 尚未出現（status=0）
前台：publish 回 501，待工程師重新產生
需要你決定：是否上架、價格欄位留空
```

## 10/01 補充（部署後生效）
- `website` PATCH：`seo` 會往下合併一層（只送 `seo.company_en` 不會清掉其他 seo 欄位；`seo.pages` 以網址為單位合併；`seo.faq` 有送就整份取代）；`categories` 只送一個品牌不會清掉另一個品牌。保守做法「先 GET 整份、改自己的鍵、整份送回」仍然正確。
- `seo.pages` 讀出來一律是物件 `{}`。不認識的 `categories` key 與 `logo_*` 鍵會被靜默丟掉，改完請 GET 回來確認。
- **v4 指令已發布**：《Hermes操作指令_前台後台AgentAPI完整流程_v4_20261001.md》是實測版 SOP（上傳／新增／修改／排序／刪除／網站設定／前台驗證），與本份衝突時以 v4 為準。

## 圖庫管理（type=files 檔案庫／images 圖片庫；金鑰要 files:read＋files:write）
`GET /files/list|info|usage|download`、`POST /files/folder|rename|move|resize|crop`、`DELETE /files`，參數都帶 `type`、`path`（圖庫內相對路徑）。
- 刪除／搬移／改名一律先經老闆同意；先 `usage` 或 `info` 看有沒有紀錄在用並回報。
- 還有紀錄在用會回 409：搬移／改名確定要做加 `update_references=1`；刪除確定要做加 `force=1`（前台會斷圖，通常先換掉那些紀錄的圖）。
- 縮放、裁剪預設另存新檔；`overwrite=1` 才覆蓋（原檔進垃圾桶）。刪除是移到垃圾桶，工程師可還原。
- 「縮圖／列表顯示」「確認」是畫面按鈕，API 不需要；list 兩種資料都給，選圖直接用 `url`。
