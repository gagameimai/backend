# Agent API 使用說明（給業主的 AI agent）

版本 2026-09-12　·　後台 backend（Laravel）　·　網址前綴 `https://<後台網域>/api/agent/`

## 一、這是什麼
讓 AI agent **不用開後台畫面、不用模擬點滑鼠**，直接用 HTTP 把資料丟進後台。
所有端點都指向「後台畫面本來就在用的同一支程式」，所以驗證規則、必填欄位、限制（例如首頁事例最多 3 筆）跟人手按按鈕一模一樣，不會有第二套邏輯。

## 二、金鑰（API Key）
- 由工程師在主機上產生：
  ```
  php artisan agent:key create --name=hermes --scopes=products:read,products:write,files:write
  php artisan agent:key create --name=hermes-all --scopes=*        # 全部權限
  php artisan agent:key list
  php artisan agent:key revoke --id=3                              # 停用，立即失效
  ```
- 金鑰長這樣：`mmk_` 開頭 44 碼。**只在建立當下印一次**，資料庫只存雜湊，之後查不到。
- 每次請求帶在 header：`X-Agent-Key: mmk_xxxxxxxx`（或 `Authorization: Bearer mmk_xxxxxxxx`）
- 跟後台管理員帳號**完全分開**；外流就 revoke 那一把，管理員密碼不用動。
- 每把金鑰每分鐘最多 120 次請求。

### 權限範圍（scopes）
| 群組 | 管到什麼 | 可用值 |
|---|---|---|
| products | 10 類商品、汽車品牌／車款、首頁精選 | `products:read`、`products:write` |
| banners | 首頁 Banner、列表頁 Banner、首頁滿版區塊 | `banners:read`、`banners:write` |
| cases | 導入事例 | `cases:read`、`cases:write` |
| dealers | 經銷據點 | `dealers:read`、`dealers:write` |
| resources | 資源下載與分類 | `resources:read`、`resources:write` |
| settings | 網站基本設定、常見問題、關於我們 | `settings:read`、`settings:write` |
| files | 圖片／檔案上傳 | `files:write` |
`GET` 要 `:read`，`POST／PATCH／DELETE` 要 `:write`；`products:*` ＝ 該群組讀寫；`*` ＝ 全部。

## 三、共用端點
| 方法 | 路徑 | 說明 |
|---|---|---|
| GET | `/api/agent/me` | 這把金鑰的名稱與權限 |
| GET | `/api/agent/schema` | **所有資源的欄位與驗證規則（機器可讀）**，agent 第一步先打這個 |
| POST | `/api/agent/upload` | 上傳圖片／檔案，回傳可直接填進 `img` 欄位的網址（需 `files:write`） |

### 上傳
`multipart/form-data`：`file`（必填）、`folder`（選填，預設 Agent）、`name`（選填，檔名前綴）
允許資料夾：Banner, ListBanner, HomeSection, MultiMedia, Din, Dashcam, Camera, AudioAccessories, Headrest, Portable, Fitting, BlindSpot, CarFrame, Case, Dealer, Resource, Agent
格式 jpg／jpeg／png／webp／gif／pdf，8MB 內。回傳：
```json
{ "message": "上傳成功", "url": "https://<後台網域>/storage/files/1/MultiMedia/gl700-20260912103000-a1b2.jpg", "path": "files/1/MultiMedia/gl700-….jpg", "size": 123456 }
```
把 `url` 填進商品的 `img`（或 Banner 的 `img`／`img_mobile`）即可。
圖片尺寸請照後台各欄位旁標示（首頁 Banner 1920×1080／1080×2160；列表頁 Banner 1920×480／1080×608；商品圖 1200×1200 白底；導入事例 1600×1000）。

## 四、標準 CRUD（大多數資源）
以 `car_media` 為例，其他資源把名稱換掉即可：
| 方法 | 路徑 | 說明 |
|---|---|---|
| GET | `/api/agent/car_media/all?page=1` | 列表（每頁 15 筆，回 `items.data`、`items.total`、`items.current_page`） |
| POST | `/api/agent/car_media` | 新增（body 為 JSON，欄位見第五節） |
| GET | `/api/agent/car_media/{id}` | 單筆（回 `item`） |
| PATCH | `/api/agent/car_media/{id}` | 更新（**要送完整欄位**，跟後台表單一樣，不是只送改的那幾個） |
| DELETE | `/api/agent/car_media/{id}` | 刪除 |
| PATCH | `/api/agent/car_media/{id}/status` | 啟用↔停用切換 |
| PATCH | `/api/agent/car_media/all/sort` | 排序，body：`{"items":[{"id":1,"sort":1},{"id":2,"sort":2}]}` |
額外動作（有的資源才有）：`PATCH /{id}/top`（置頂切換）、`/{id}/spc`、`/{id}/pinned`、`/{id}/home`、`/{id}/img`。

回應格式：成功 `{"message":"新增成功"}`；驗證失敗 HTTP 422 `{"message":"…欄位為必填"}`；沒權限 403；金鑰錯 401；超過頻率 429。

## 五、各資源欄位（來自後台的驗證規則；required＝必填、nullable＝可留空、integer＝整數）

### `car_media`　多媒體安卓機（MM ME／MM OEM／Clarion GL／Clarion OEM，用 type 0~3 區分）
權限群組：`products`　·　額外動作：top

| 欄位 | 中文 | 規則 |
|---|---|---|
| `type` | 類型 | required|integer |
| `name` | 名稱 | required |
| `img` | 圖片 | required |
| `memo` | 列表簡述 | required |
| `size` | 尺寸 | nullable |
| `hard_drive` | 硬碟 | nullable |
| `ram` | 記憶體 | nullable |
| `resolution` | 解析度 | nullable |
| `price` | 建議售價 | nullable |
| `content` | 內文敘述 | required |
| `is_top` | 置頂 | required|integer |
| `status` | 狀態 | required|integer |

### `car_head_unit`　車用主機 1/2DIN
權限群組：`products`　·　額外動作：top

| 欄位 | 中文 | 規則 |
|---|---|---|
| `type` | 類型 | required|integer |
| `name` | 名稱 | required |
| `img` | 列表圖片 | required |
| `size` | 尺寸 | nullable |
| `hard_drive` | 硬碟 | nullable |
| `ram` | 記憶體 | nullable |
| `resolution` | 解析度 | nullable |
| `price` | 建議售價 | nullable |
| `content` | 產品規格 | required |
| `is_top` | 置頂 | required|integer |
| `status` | 狀態 | required|integer |

### `car_dashcam`　行車記錄器
權限群組：`products`　·　額外動作：top

| 欄位 | 中文 | 規則 |
|---|---|---|
| `brand` | 品牌 | required|integer |
| `name` | 名稱 | required |
| `img` | 列表圖片 | required |
| `content` | 產品規格 | required |
| `is_top` | 置頂 | required|integer |
| `status` | 狀態 | required|integer |

### `car_camera`　鏡頭
權限群組：`products`　·　額外動作：top

| 欄位 | 中文 | 規則 |
|---|---|---|
| `brand` | 品牌 | required|integer |
| `name` | 名稱 | required |
| `img` | 列表圖片 | required |
| `content` | 產品規格 | required |
| `is_top` | 置頂 | required|integer |
| `status` | 狀態 | required|integer |

### `car_audio_accessories`　汽車音響
權限群組：`products`　·　額外動作：top

| 欄位 | 中文 | 規則 |
|---|---|---|
| `type` | 類型 | required|integer |
| `name` | 名稱 | required |
| `img` | 列表圖片 | required |
| `content` | 產品規格 | required |
| `is_top` | 置頂 | required|integer |
| `status` | 狀態 | required|integer |

### `car_headrest`　頭枕螢幕
權限群組：`products`　·　額外動作：top

| 欄位 | 中文 | 規則 |
|---|---|---|
| `name` | 名稱 | required |
| `img` | 列表圖片 | required |
| `content` | 產品規格 | required |
| `is_top` | 置頂 | required|integer |
| `status` | 狀態 | required|integer |

### `car_portable`　可攜式
權限群組：`products`　·　額外動作：top

| 欄位 | 中文 | 規則 |
|---|---|---|
| `name` | 名稱 | required |
| `img` | 列表圖片 | required |
| `content` | 產品規格 | required |
| `is_top` | 置頂 | required|integer |
| `status` | 狀態 | required|integer |

### `car_fitting`　影像・安全
權限群組：`products`　·　額外動作：top

| 欄位 | 中文 | 規則 |
|---|---|---|
| `name` | 名稱 | required |
| `img` | 列表圖片 | required |
| `material` | 材質 | required |
| `power` | 電源 | required |
| `content` | 產品規格 | required |
| `is_top` | 置頂 | required|integer |
| `status` | 狀態 | required|integer |

### `car_blind_spot`　盲點偵測
權限群組：`products`　·　額外動作：top、spc

| 欄位 | 中文 | 規則 |
|---|---|---|
| `name` | 名稱 | required |
| `img` | 圖片 | required |
| `content` | 內文敘述 | required |
| `is_top` | 置頂 | required|integer |
| `status` | 狀態 | required|integer |

### `car_blind_spot_format`　盲點偵測規格（巢狀：先有 car_blind_spot）
權限群組：`products`

| 欄位 | 中文 | 規則 |
|---|---|---|
| `car_brand_id` | 汽車品牌 | required|integer |
| `style` | 汽車車款 | required |
| `year` | 年份 | required |
| `spc` | 規格 | required |
| `status` | 狀態 | required|integer |

### `car_frame`　安卓車框
權限群組：`products`　·　額外動作：img（刪單張圖，對應後台 deleteImg）

| 欄位 | 中文 | 規則 |
|---|---|---|
| `car_brand_id` | 汽車品牌 | required|integer |
| `car_id` | 汽車車款 | required|integer |
| `year_start` | 開始年份 | required|integer |
| `year_end` | 結束年份 | required|integer |
| `size` | 尺寸 | required |
| `name` | 名稱 | nullable |
| `content` | 內容敘述 | nullable |
| `status` | 狀態 | required|integer |

### `car_brand`　汽車品牌
權限群組：`products`

| 欄位 | 中文 | 規則 |
|---|---|---|
| `name` | 名稱 | required |
| `status` | 狀態 | required|integer |

### `car`　汽車車款
權限群組：`products`

| 欄位 | 中文 | 規則 |
|---|---|---|
| `car_brand_id` | 汽車品牌 | required|integer |
| `name` | 名稱 | required |
| `year_start` | 開始年份 | required|integer |
| `year_end` | 結束年份 | required|integer |
| `status` | 狀態 | required|integer |

### `recommend_product`　首頁精選商品
權限群組：`products`

| 欄位 | 中文 | 規則 |
|---|---|---|
| `product_type` | 商品分類 | required|string|in: |
| `product_id` | 商品 | required|integer |
| `sort` | 排序 | nullable|integer |
| `status` | 狀態 | required|integer |

### `banner`　首頁 Banner
權限群組：`banners`

| 欄位 | 中文 | 規則 |
|---|---|---|
| `name` | 名稱 | required |
| `url` | 外部連結 | nullable|url |
| `img` | 圖片（電腦版） | required |
| `img_mobile` | 圖片（手機版） | nullable |
| `status` | 狀態 | required|integer |

### `list_banner`　列表頁 Banner
權限群組：`banners`　·　路徑：`PATCH /api/agent/list_banner/{page_key}/{type_key}`

| 欄位 | 中文 | 規則 |
|---|---|---|
| `img` | Banner 圖片（電腦版） | nullable|string |
| `img_mobile` | Banner 圖片（手機版） | nullable|string |

### `home_section`　首頁滿版區塊
權限群組：`banners`　·　路徑：`PATCH /api/agent/home_section/{section_key}`

| 欄位 | 中文 | 規則 |
|---|---|---|
| `content` | 區塊內容 | nullable|string |
| `img` | 背景圖（電腦版） | nullable|string |
| `img_mobile` | 背景圖（手機版） | nullable|string |

### `install_case`　導入事例
權限群組：`cases`　·　額外動作：pinned（置頂）、home（首頁，最多 3 筆）

| 欄位 | 中文 | 規則 |
|---|---|---|
| `category` | 分類 | required|integer|in:0,1,2,3,4,5,6,7,8 |
| `name` | 名稱 | required |
| `img` | 案例圖片 | required |
| `sort` | 排序 | nullable|integer |
| `status` | 狀態 | required|integer |
| `installed_at` | 安裝日期 | nullable|date |
| `is_pinned` | 置頂 | nullable|integer|in:0,1 |
| `is_home` | 顯示在首頁 | nullable|integer|in:0,1 |
| `home_sort` | 首頁排序 | nullable|integer |
| `car_brand_id` | 汽車品牌 | required|integer|exists:car_brand,id |
| `car_id` | 汽車車款 | required|integer|exists:car,id |
| `product` | 安裝產品 | nullable|string|max:255 |
| `dealer` | 施工據點 | nullable|string|max:255 |
| `need` | 客戶需求 | nullable|string |
| `work` | 施工內容 | nullable|string |

### `dealer`　經銷據點
權限群組：`dealers`

| 欄位 | 中文 | 規則 |
|---|---|---|
| `name` | 名稱 | required |
| `county` | 縣市 | required |
| `address` | 地址 | required |
| `tel` | 聯絡電話 | required |
| `status` | 狀態 | required|integer |

### `resource_category`　資源下載分類
權限群組：`resources`

| 欄位 | 中文 | 規則 |
|---|---|---|
| `name` | 名稱 | required |
| `memo` | 簡述 | nullable |
| `status` | 狀態 | required|integer |

### `resource`　資源下載
權限群組：`resources`

| 欄位 | 中文 | 規則 |
|---|---|---|
| `resource_category_id` | 分類 | required|integer |
| `name` | 名稱 | required |
| `url` | 外部連結 | required|url |
| `status` | 狀態 | required|integer |

### `website`／`qa`／`about`（網站設定，群組 `settings`）
只有 `GET /api/agent/{name}/all` 與 `PATCH /api/agent/{name}`，欄位以 GET 回來的為準、整份送回。

## 六、範例
```bash
# 1. 看自己有哪些權限
curl -H "X-Agent-Key: mmk_xxx" https://<後台網域>/api/agent/me

# 2. 上傳商品圖
curl -H "X-Agent-Key: mmk_xxx" -F "file=@gl700.png" -F "folder=MultiMedia" https://<後台網域>/api/agent/upload

# 3. 新增一筆 Clarion GL 商品（type=2）
curl -H "X-Agent-Key: mmk_xxx" -H "Content-Type: application/json" -X POST https://<後台網域>/api/agent/car_media \
  -d '{"type":2,"name":"GL-700 2K","img":"https://…/storage/files/1/MultiMedia/gl700-….png","memo":"2K 螢幕 · 8 核","size":"10.1\"","hard_drive":"256G","ram":"8G","resolution":"2000x1200","price":"","memo_in":"","content":"<p>…</p>","is_top":0,"status":1}'

# 4. 改價格（PATCH 要送完整欄位：先 GET /{id} 拿回 item，改掉要改的，再整份 PATCH 回去）
curl -H "X-Agent-Key: mmk_xxx" -H "Content-Type: application/json" -X PATCH https://<後台網域>/api/agent/car_media/12 -d @item.json

# 5. 導入事例放首頁
curl -H "X-Agent-Key: mmk_xxx" -X PATCH https://<後台網域>/api/agent/install_case/7/home
```
Python：
```python
import requests
H = {"X-Agent-Key": "mmk_xxx"}
BASE = "https://<後台網域>/api/agent"
r = requests.post(f"{BASE}/upload", headers=H, files={"file": open("gl700.png","rb")}, data={"folder":"MultiMedia"}).json()
item = requests.get(f"{BASE}/car_media/12", headers=H).json()["item"]
item["img"] = r["url"]; item["price"] = "NT$ 18,800"
requests.patch(f"{BASE}/car_media/12", headers=H, json=item)
```

## 七、稽核與安全
- 每一次 POST／PATCH／DELETE 都寫進 `agent_audit_logs`：哪把金鑰、幾點、哪個資源、id、送進來的資料、**改前快照**、回應碼、IP。改壞了可以查、可以照快照退回。
- 金鑰只存 SHA-256；`revoked_at` 有值立即失效。
- 建議給 agent 的金鑰只開它真的要用的群組；要改 Banner 再另開一把 `banners:write`。

## 八、工程師部署
1. `php artisan migrate`（建 `agent_keys`、`agent_audit_logs`）
2. `php artisan agent:key create --name=hermes --scopes=...`，把印出的金鑰交給業主
3. 確認 `.env` 的 `APP_URL` 是正式後台網址（上傳回傳的圖片網址靠它組）
4. 確認 `public/storage` 符號連結存在（`php artisan storage:link`）
