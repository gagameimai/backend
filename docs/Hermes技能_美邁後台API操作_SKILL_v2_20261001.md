---
name: meimai-admin-api
description: 用 Agent API 直接改美邁官網後台（產品、圖片、Banner、導入事例、經銷據點、資源下載、網站設定、浮水印）。2026-10-01 後端更新版：圖片直接填、PATCH 只送要改的欄位、新增回 id、啟用／置頂用 body 設定、寫完呼叫 publish。API 不通時照舊用瀏覽器。只動後台資料，不碰前台程式。
---

# 美邁官網後台 Agent API 操作規則（v2，2026-10-01 後端更新後）

> **更正（2026-10-01 實測）**：前台是即時讀取的伺服器，後台資料寫入後前台立即變（已用 car_dashcam/1 實測並還原）。**不需要呼叫 `/publish`，回 501 或 `needed:false` 都不用回報「待重新產生」**；寫入後讀回驗證＋打前台公開 API 確認即可。本文其他提到 publish／501／「待工程師重新產生」之處，一律以此更正為準。


這份取代 9/29 的舊技能。**舊技能裡「先 GET 再整份送回」「上傳後再填 url 才算完成」「切換前先 GET 現況」「車框沒送的圖會被刪」這四條都已經不適用**，照本份做。

完整說明在《美邁後台與 Agent API 完整操作手冊》（`backend/docs/美邁後台與AgentAPI完整操作手冊_20260930.md`）；第 0 章是「舊版 vs 新版」對照、路徑總表；第 7 章逐模組欄位；第 9 章標準流程。

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

要把檔案放進指定資料夾（例如 `Clarion 2026/GL-700_Ultra_13/...`）才先 `POST /upload` 拿 `url` 再填。
寫入回應看 `saved_images`：網址要是 `https://admin.meimai.com.tw/storage/files/1/...`；有 `warnings` 就是沒存成功。
尺寸：商品圖 1200×1200 白底／去背；首頁輪播 2560×960（手機直式另傳）；各頁頂端橫幅 1920×480＋手機 1080×608（左半留白）；首頁滿版區塊 2560×1440＋手機 1080×1920。

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
**首頁精選**：`GET /recommend_product/options?product_type=car_dashcam` 找商品 id → `POST /recommend_product` `{product_type, product_id, status}`；排序用 `all/sort`（大的在前）。停用的商品前台精選自動不顯示。
**導入事例上首頁**：最多 3 筆；先把舊的 `PATCH /install_case/{id}/home` `{"is_home":0}`，再設新的，超過會回 400。
**改排序**：`GET all` 看現況 → 單筆 `PATCH /{id}` 帶 `sort`；整批 `PATCH all/sort`。
**網站設定**：`GET /website/all` → 改 `content` 裡要改的欄位 → `PATCH /website` `{"content":{整份物件}}`，一個鍵都不能少（工程師部署第 129 項後才可以只送要改的鍵，部署前一律整份送）。qa／about 的 content 是整段 HTML，永遠整份送。公司名稱、地址、電話不要自己改，要先問老闆。

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
