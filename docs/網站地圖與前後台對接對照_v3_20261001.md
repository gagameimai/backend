# 美邁官網 網站地圖與前後台對接對照（v3，2026-10-01 更新；原版 2026-09-30）

> **v3 重點**：後台新增了「產品類別開關」「全站標誌圖片」「SEO／GEO 設定」「全站色彩」，原本標成【前台工程師】的「選單項目顯示與順序」「Logo」「分享圖」「SEO 標題與描述」現在大部分改成【後台】。詳見本文 §5 與新增的 §7；操作流程見《完整操作手冊 v3》第 14～18 章。前台資料何時生效：以 2026-10-01 實測，前台是伺服器即時讀取，後台改完重新整理就會變（新功能需要前台程式也部署新版）。

> 給老闆、Hermes（Agent）、前台工程師、後端工程師四方共用。
> 每一個前台網址：資料從哪個後台 API 來、對應後台哪個選單、點下去會到哪、**哪些東西在後台就能改、哪些一定要前台工程師改程式**。
> 依 2026-09-30 本機程式碼（`frontend-v2`、`backend`）核對；前台網址以 `https://clarion.meimai.com.tw` 為基底，後台 API 以 `https://admin.meimai.com.tw/api` 為基底。

---

## 0. 先懂三種「誰能改」

| 標記 | 意思 | 誰做 | 要不要重新產生前台 |
|---|---|---|---|
| 【後台】 | 後台畫面或 Agent API 就能改：商品、圖片、內文、Banner、據點、下載、設定，**v3 另有：產品類別顯示與順序、Logo 與分頁圖示、SEO 標題說明、公司資料、AI 問答、全站色彩** | 老闆／Hermes | **v3 更正：不用**（前台即時讀取，重新整理即變；若有快取要稍等。若日後前台改回靜態產生，才需 `npm run generate`） |
| 【前台工程師】 | 寫死在前台程式裡：頁面標題、副標、SEO 標題與描述、選單文字與順序、按鈕文字、版面配置、顏色、固定圖示、篩選規則 | 前台工程師改 `frontend-v2` 程式後重新產生 | 要 |
| 【後端工程師】 | 後台程式邏輯：欄位規則、排序規則、API 回傳格式、上限檢查 | 後端工程師改 `backend` 程式後部署 | 通常不用，但資料相關要 |

判斷口訣：**「內容」在後台改，「外框和文字標籤」找前台工程師，「規則」找後端工程師。**
前台所有寫死文字集中在 `frontend-v2/locales/zh-tw.js`（中文）與 `locales/en.js`（英文），每頁一個區塊（`home`、`carFrame`、`clarionOverview`…）；前台工程師改字不用動版面。

---

## 1. 全站網址地圖（每個網址的資料來源與後台對應）

### 1.1 首頁與總覽

| 前台網址 | 頁面 | 資料來源（前台 API） | 後台選單／Agent 資源 | 點下去會到哪 |
|---|---|---|---|---|
| `/` | 首頁 | `GET /banner`（輪播）、`GET /car`（車型快搜下拉）、`GET /recommend_products`（精選商品）、`GET /home_section`（三段滿版區塊 zone1／zone2／zone3）、`GET /install_cases?home=1`（首頁 3 筆事例）、`GET /website`（頁尾） | Banner管理 `banner`；汽車品牌／車款 `car_brand`、`car`；首頁精選商品 `recommend_product`；首頁滿版區塊管理 `home_section`；安裝案例 `install_case`；網站基本設定 `website` | 輪播→`banner.url`；快搜「查詢」→直接到 `/carFrame?car_brand_id=&car_id=&year=`（安卓車框頁開到這台車的結果；`/searchPage` 已不再是首頁查詢的目的地，只保留給舊連結）；精選卡片→該商品詳情頁（後端自動組，見 §2.2）；「依需求選購」三個連結→`/products/android-headunit`、`/products/dash-cam`、`/products/car-audio`；事例→`/cases/{id}`、「看全部」→`/cases` |
| `/clarion/overview` | Clarion 全品項總覽 | `GET /multimedia?type=2`（GL）、`GET /audio_accessories`、`GET /camera?brand=1`、`GET /head_unit`、`GET /dashcam?brand=1`、`GET /list_banner?page=clarionOverview` | 多媒體機（type 2）、汽車音響、鏡頭（brand 1）、車用主機、行車記錄器（brand 1）；產品頁 Banner 管理 `clarionOverview` | 各卡片→`/multimediaDetail/{id}`、`/clarion/audioAccessoriesDetail/{id}`、`/clarion/cameraDetail/{id}`、`/headUnitDetail/{id}`、`/clarion/dashcamDetail/{id}` |
| `/mm/overview` | MM 美邁全品項總覽 | `GET /multimedia?type=0`、`?type=1`、`GET /carframe`、`GET /blindspot`、`GET /dashcam?brand=0`、`GET /camera?brand=0`、`GET /fitting`、`GET /list_banner?page=mmOverview` | 多媒體機（type 0／1）、安卓車框、盲點偵測、行車記錄器（brand 0）、鏡頭（brand 0）、車用配件；產品頁 Banner 管理 `mmOverview` | 各卡片→對應詳情頁 |

### 1.2 商品列表頁 → 詳情頁

| 前台列表網址 | 前台 API（參數） | 後台選單 → Agent 資源 | 頂端 Banner（list_banner 的 page／type） | 卡片點下去 |
|---|---|---|---|---|
| `/multimedia` | `GET /multimedia`（不帶 type＝全部） | 產品管理 → 多媒體機 `car_media` | `multimedia`／依 type 0～3 | `/multimediaDetail/{id}` |
| `/mm/me` | `GET /multimedia?type=0` | 多媒體機 type 0（MM 多媒體安卓機 ME 系列） | `multimedia`／`0` | `/multimediaDetail/{id}` |
| `/mm/oem` | `GET /multimedia?type=1` | 多媒體機 type 1（MM 車型專用機） | `multimedia`／`1` | `/multimediaDetail/{id}` |
| `/clarion/gl` | `GET /multimedia?type=2` | 多媒體機 type 2（Clarion GL） | `multimedia`／`2` | `/multimediaDetail/{id}` |
| `/clarion/oem` | `GET /multimedia?type=3` | 多媒體機 type 3（Clarion OEM 車型專用機） | `multimedia`／`3` | `/multimediaDetail/{id}` |
| `/headUnit` | `GET /head_unit` | 車用主機 1/2DIN `car_head_unit` | `headUnit` | `/headUnitDetail/{id}` |
| `/audioAccessories` | `GET /audio_accessories` | 汽車音響 `car_audio_accessories`（type 0 喇叭／1 高音／2 重低音／3 擴大機，前台分頁籤） | `audioAccessories` | `/clarion/audioAccessoriesDetail/{id}` |
| `/mm/dashcam` | `GET /dashcam?brand=0` | 行車記錄器 `car_dashcam`，**brand 0＝美邁** | `dashcam`／`mm` | `/mm/dashcamDetail/{id}` |
| `/clarion/dashcam` | `GET /dashcam?brand=1` | 行車記錄器，**brand 1＝歌樂** | `dashcam`／`clarion` | `/clarion/dashcamDetail/{id}` |
| `/mm/camera` | `GET /camera?brand=0` | 鏡頭 `car_camera`，brand 0 | `camera`／`mm` | `/mm/cameraDetail/{id}` |
| `/clarion/camera` | `GET /camera?brand=1` | 鏡頭，brand 1 | `camera`／`clarion` | `/clarion/cameraDetail/{id}` |
| `/carFrame` | `GET /car`（品牌／車款下拉）＋`GET /carframe?car_brand_id=&car_id=&year=` | 安卓車框 `car_frame`；汽車品牌 `car_brand`；汽車車款 `car` | `carFrame` | 結果卡片→`/carFrameDetail/{id}`；卡片另有 LINE／FB／複製連結分享。品牌下拉的「熱門品牌」是前台固定 12 個（TOYOTA、MITSUBISHI、HONDA…靠英文名比對）【前台工程師】 |
| `/safety` | `GET /blindspot` | 盲點偵測 `car_blind_spot` | `safety` | `/safetyDetail/{id}` |
| `/fitting` | `GET /fitting` | 車用配件 `car_fitting` | `fitting` | `/fittingDetail/{id}` |
| `/headrest`（選單已隱藏，網址仍可開） | `GET /headrest` | 頭枕螢幕 `car_headrest` | `headrest` | `/headrestDetail/{id}` |
| `/portable`（選單已隱藏，網址仍可開） | `GET /portable` | 可攜式 `car_portable` | `portable` | `/portableDetail/{id}` |
| `/products/android-headunit` | `GET /multimedia?type=2`、`?type=3`、`?type=0`（三組） | 多媒體機 | `productLanding`／`landingHeadunit` | `/multimediaDetail/{id}` |
| `/products/car-audio` | `GET /audio_accessories`、`GET /head_unit` | 汽車音響、車用主機 | `productLanding`／`landingAudio` | 對應詳情 |
| `/products/dash-cam` | `GET /dashcam?brand=1`、`?brand=0` | 行車記錄器 | `productLanding`／`landingDashcam` | 對應詳情 |
| `/searchPage`（舊查詢結果頁，首頁已改導到 `/carFrame`） | `GET /search?car_brand_id=&car_id=&year=` | 車框＋相關商品（後端組合） | `searchPage` | 對應詳情 |

**詳情頁**（`/xxxDetail/{id}`）各自打 `GET /{同名 API}/{id}`；行車記錄器與鏡頭詳情另帶 `brand` 參數，避免 `/mm/...` 網址開到歌樂的商品。
**車框詳情** `/carFrameDetail/{id}`：`GET /carframe/{id}`（4 組圖、年份、尺寸、車廠車款名）＋`GET /multimedia`（下方推薦主機）。

### 1.3 內容與支援頁

| 前台網址 | 前台 API | 後台選單 → Agent 資源 | 頂端 Banner key | 備註 |
|---|---|---|---|---|
| `/cases` | `GET /install_cases` | 安裝案例 `install_case` | `cases` | 卡片→`/cases/{id}`；`is_pinned` 置頂、`is_home` 首頁顯示（最多 3 筆） |
| `/cases/{id}` | `GET /install_cases/{id}` | 安裝案例 | — | 顯示車廠車款（由 `car_brand_id`、`car_id` 組）、產品、據點、需求、施工內容 |
| `/partner` | `GET /partner?county=` | 經銷據點管理 `dealer` | `partner` | 縣市清單由後端從據點資料整理 |
| `/download` | `GET /resource` | 資源下載管理 → 分類管理 `resource_category`、檔案管理 `resource` | `download` | 檔案的 `url` 是外部連結（雲端／PDF），前台直接連出去 |
| `/qa` | `GET /question` | 系統設定 → 常見問題 `qa` | `qa` | 整份 HTML |
| `/about` | `GET /about` | 系統設定 → 關於我們 `about` | `about` | 內文整份 HTML；頁面大標與 Banner 由前台與 `list_banner.about` 決定 |
| 頁尾（每一頁） | `GET /website` | 系統設定 → 網站基本設定 `website` | — | 電話、Email、地址、FB／IG／YT 連結（沒填就不顯示該圖示）、版權文字 |
| `/sitemap.xml`、`/robots.txt` | 前台程式自動產生（`server/routes/sitemap.xml.ts`）：抓所有列表 API 把每個 `status=1` 的商品詳情頁列進去 | — | — | 【前台工程師】新增一種商品類型時要在這支程式加一行 |

### 1.4 表頭選單（Header）連到哪

| 選單 | 連結 | 文字來源 |
|---|---|---|
| Logo | `/` | **v3：後台「網站基本設定 → 全站標誌圖片」可換**（沒設定用前台 `assets/` 內建圖）【後台】 |
| 歌樂 Clarion ▾ | 觸發→`/clarion/overview`；下拉：`/clarion/gl`、`/clarion/oem`、`/audioAccessories`、`/clarion/camera`、`/headUnit`、`/clarion/dashcam`（頭枕、可攜式預設隱藏）。**v3：下拉哪些項目顯示、順序、名稱改由後台「產品類別開關」決定** | `locales/*.js` 的 `header.clarionItems`【前台工程師】 |
| 美邁 MM ▾ | 觸發→`/mm/overview`；下拉：`/mm/me`、`/mm/oem`、`/carFrame`、`/safety`、`/mm/dashcam`、`/mm/camera`、`/fitting` | `header.mmItems`【前台工程師】 |
| 導入事例 | `/#cases`（首頁事例區） | `header`【前台工程師】 |
| 立即諮詢（CTA 按鈕） | `/partner` | `header`【前台工程師】 |

---

## 2. 每一頁「後台能改什麼、前台工程師才能改什麼」

### 2.1 通則（適用所有頁面）

| 項目 | 誰能改 | 在哪改 |
|---|---|---|
| 商品名稱、圖片、摘要（memo_in）、內文（content）、規格欄位、價格、啟用／停用、置頂 | 【後台】 | 產品管理各分類，或 Agent `PATCH /api/agent/{資源}/{id}` |
| 商品排序 | 【後台】（Agent `PATCH …/all/sort`，或 POST／PATCH 帶 `sort`；後台畫面沒有拖曳排序） | 見手冊第 6 章 |
| 商品列表出現順序的**規則**（先置頂→再 sort→再名稱） | 【後端工程師】 | `backend/app/Http/Controllers/Api/*Controller.php` |
| 每頁頂端 Banner 圖（電腦版／手機版） | 【後台】 | 產品頁 Banner 管理 `list_banner`，依 `page_key／type_key` |
| 每頁頂端 Banner **上面的標題、副標、麵包屑文字** | 【前台工程師】 | `locales/zh-tw.js` 該頁區塊（例 `carFrame.title`） |
| 每頁 SEO 標題與描述（`<title>`、`meta description`） | 【前台工程師】 | `locales/zh-tw.js` 各頁 `seoTitle`／`seoDesc` |
| 商品卡片上的標籤（「NEW」、「置頂」樣式）、卡片版型、每列幾張、顏色、字型 | 【前台工程師】 | 各 `components/*.vue`、`pages/**.vue` 的 `<style>` |
| 篩選頁籤（例：汽車音響的喇叭／高音／重低音／擴大機分頁；行車記錄器 MM／Clarion 分開） | 【前台工程師】（規則寫死在前台）；商品要出現在哪個頁籤是【後台】改 `type`／`brand` 欄位 | 前台 `pages/**`；後台商品欄位 |
| 表頭選單項目、順序、隱藏／顯示（例：頭枕、可攜式目前隱藏） | 【前台工程師】 | `components/Header.vue`、`locales/*.js` |
| 頁尾聯絡資訊、社群連結、版權 | 【後台】 | 網站基本設定 `website` |
| 頁尾欄位標籤文字（例：「聯絡我們」）、社群圖示樣式 | 【前台工程師】 | `components/Footer.vue`、`locales/*.js` 的 `footer` |
| 品牌 Logo、favicon、og 分享圖、固定裝飾圖 | 【前台工程師】 | `frontend-v2/assets/`、`public/` |
| 分享按鈕（LINE／FB／複製連結）的文案 | 【前台工程師】 | `locales/*.js` 的 `share` |
| 網址結構（例 `/mm/dashcam` 改名） | 【前台工程師】＋要同步改 `list_banner` 的 key（【後端工程師】） | `pages/` 目錄結構、`config/list_banner.php` |

### 2.2 首頁 `/` 逐區塊

| 首頁區塊（由上到下） | 後台能改 | 前台工程師才能改 |
|---|---|---|
| 頂端輪播（Hero） | 圖（電腦版 2560×960、手機版直式）、點擊連結、順序、啟用【後台：Banner管理】 | 輪播秒數、箭頭樣式、高度、透明表頭 |
| 車型查詢抽屜（品牌／車款／年份） | 品牌、車款、年份範圍資料【後台：汽車品牌、汽車車款】 | 抽屜把手、欄位標籤、按鈕文字、「不知道年份」連結文案 |
| 精選商品 | 放哪些商品、順序、啟用【後台：首頁精選商品】；卡片的名稱／圖片／連結由後端自動從商品組出來，**停用的商品不再顯示（本次修正）** | 區塊標題「Products／精選商品」、說明文、卡片版型 |
| 「依需求選購」三個連結（安卓主機／行車記錄器／汽車音響） | — | 文案與連到哪【前台工程師，`locales` 的 `footer.lp*`】 |
| 三段滿版區塊 zone1（Clarion）、zone2（MM）、zone3（尾端 CTA） | 背景圖（電腦版 2560×1440、手機版 1080×1920）、區塊內 HTML 內容【後台：首頁滿版區塊管理】；後台沒填時用前台預設 | 預設背景圖（`public/home/section-*.webp`，同檔名覆蓋即可）、版位順序、內容區塊的排版樣式 |
| 導入事例 | 哪 3 筆上首頁（`is_home`，最多 3 筆）、事例內容【後台：安裝案例】 | 區塊標題、卡片版型 |
| 頁尾 | 聯絡資訊、社群、版權【後台：網站基本設定】 | 欄位標籤、版型 |

### 2.3 商品詳情頁（所有 `/xxxDetail/{id}`）

| 區塊 | 後台能改 | 前台工程師才能改 |
|---|---|---|
| 主圖、名稱、摘要、規格表、內文 | 全部【後台】 | 版型、規格表欄位名稱的順序、字級 |
| 內文的排版 | 內文用 `<h2>`、`<ul>`、`<table>` 等標準 HTML【後台】 | 這些標籤長什麼樣（`.cms-content` 樣式） |
| 分享按鈕、返回列表按鈕 | — | 文案與樣式 |
| 車框詳情下方「推薦主機」 | 推薦哪些＝多媒體機列表前幾筆（依排序）【後台改排序】 | 顯示幾筆、標題 |
| 盲點偵測詳情「適用車款」 | 車款清單【後台：盲點偵測 → 規格綁定（適用車款）】 | 表格版型 |
| **停用的商品** | 設 `status=0`【後台】 | 本次修正後 API 回 404，前台目前顯示空白頁；建議前台工程師加「找不到此商品」提示（見 §4） |

### 2.4 內容頁

| 頁面 | 後台能改 | 前台工程師才能改 |
|---|---|---|
| `/about` 品牌故事 | 內文 HTML、頂端 Banner【後台】 | 頁面大標、兩個品牌 Logo（`assets/about-clarion.svg`、`about-mm.svg`）、版型 |
| `/qa` 常見問題 | 整份 HTML【後台】 | 頁面標題、手風琴樣式 |
| `/partner` 經銷據點 | 據點名稱、縣市、地址、電話、啟用、排序【後台】 | 地圖／縣市篩選的介面、標籤文字 |
| `/download` 資源下載 | 分類、檔案名稱、外部連結、排序、啟用【後台】 | 版型、標籤文字 |
| `/cases`、`/cases/{id}` | 事例全部內容【後台】 | 版型、欄位標籤（「需求」「施工內容」等） |

---

## 3. 資料關聯（改一邊會影響另一邊）

| 後台資料 | 被誰引用 | 刪除／停用時會怎樣（本次修正後） |
|---|---|---|
| 汽車品牌 `car_brand` | 汽車車款、安卓車框、安裝案例、盲點適用車款 | **有人在用就不能刪**，回 400 並說明幾筆在用；請改停用 |
| 汽車車款 `car` | 安卓車框、安裝案例 | 同上 |
| 資源分類 `resource_category` | 資源檔案 | 底下有檔案就不能刪 |
| 各類商品（10 類） | 首頁精選商品 | 還在精選裡就不能刪；先把精選那筆移除或停用 |
| 盲點偵測商品 | 適用車款（子資料） | 刪商品時子資料一起刪（原本會留孤兒） |
| 商品 `status=0` | 列表、詳情、首頁精選、sitemap | 列表不出現、**詳情 404（本次修正）**、**精選不顯示（本次修正）**、sitemap 不列 |
| 車框圖片欄位 | 同一張圖可能被多筆車框／Banner／內文共用 | **刪圖只清欄位、不刪實體檔（本次修正）**；要清檔案請到後台檔案管理員 |
| 檔案管理員裡的圖 | 資料庫只存網址，不知道誰在用 | 在檔案管理員刪檔＝所有引用該圖的地方變壞圖；刪之前先全站搜尋網址 |

---

## 4. 本次後端修正對前台的影響（前台工程師請看）

| 修正 | 前台看到的變化 | 前台需不需要改 |
|---|---|---|
| 商品列表 `is_top` 改為大到小（置頂在前）：head_unit、dashcam、camera、audio_accessories、headrest、portable、fitting、blindspot、search | 置頂商品跑到列表最前面（原本反而在最後） | 不用；重新產生前台即可 |
| 詳情 API 加 `status=1` 檢查（10 支） | 停用商品的詳情網址回 404；前台 `useAsyncData` 取不到資料→頁面內容空白 | **建議**：在各詳情頁對 `data.value === null` 顯示「找不到此商品」並提供回列表連結（目前不會壞，只是空白） |
| 首頁精選跳過停用商品 | 精選商品少一張卡 | 不用 |
| 刪除檢查關聯 | 後台畫面按「刪除」可能收到 400 訊息（後台畫面已會顯示 API 回的訊息） | 前台不受影響 |
| 車框刪圖不刪實體檔 | 無 | 不用 |
| 頂端 Banner 設定檔名稱修正（fitting＝車用配件、safety＝影像・安全） | 後台畫面顯示的標籤正確；前台不受影響 | 不用 |

---

## 5. 快速判斷表（老闆／Hermes 收到需求時先對照）

| 老闆說的話 | 分類 | 做法 |
|---|---|---|
| 「這台換張圖」「改一下規格／價格／內文」 | 【後台】 | Agent PATCH 該商品；重新產生前台 |
| 「這台先下架」「這台要放最前面」 | 【後台】 | `status=0`／`is_top=1`＋排序 |
| 「首頁換 Banner」「首頁那三塊背景換圖」 | 【後台】 | Banner管理／首頁滿版區塊管理 |
| 「某個列表頁頂端的圖換掉」 | 【後台】 | 產品頁 Banner 管理，依 §1.2 的 key |
| 「首頁精選換這幾台」 | 【後台】 | 首頁精選商品 |
| 「加一個經銷點」「加一個下載檔案」「常見問題加一條」 | 【後台】 | dealer／resource／qa |
| 「頁尾電話改掉」「加 IG 連結」 | 【後台】 | 網站基本設定 |
| 「SEO 標題／搜尋結果說明改成…」（24 個主要頁面） | 【後台】（v3） | 系統設定 → SEO／GEO 設定 → 每頁標題與說明 |
| 「頁面標題改成…」「副標文案改一下」（頁面內的字） | 【前台工程師】 | `locales/zh-tw.js`（英文版 `en.js` 同步） |
| 「把頭枕、可攜式、盲點偵測…顯示／隱藏」「選單順序換一下」「類別改名」 | 【後台】（v3） | 產品管理 → 產品類別開關（美邁、歌樂分開） |
| 「選單多加一個全新的項目」 | 【前台工程師】 | `Header.vue`（開關只管現有的 15 個類別） |
| 「卡片改成一列四個」「顏色換成…」「字太小」 | 【前台工程師】 | 對應 `.vue` 的 `<style>` |
| 「首頁那四個入口改名／換圖示」 | 【前台工程師】 | `pages/index.vue`＋`locales` |
| 「Logo 換掉」「頁尾標誌換掉」「分頁小圖示換掉」「LINE／FB 分享縮圖換掉」 | 【後台】（v3） | 網站基本設定 → 全站標誌圖片；SEO／GEO 設定 → 分享圖片 |
| 「全站深色底、重點色、頁尾色換掉」 | 【後台】（v3） | 網站基本設定 → 全站底色 |
| 「公司英文名／國際電話／AI 常見問答／驗證碼」 | 【後台】（v3） | SEO／GEO 設定 |
| 「新增一種商品分類」 | 【後端工程師】＋【前台工程師】 | 後端：資料表、控制器、路由、Agent 設定、list_banner key；前台：頁面、選單、sitemap |
| 「排序規則改掉」「詳情頁要不要擋停用」「上限幾筆」 | 【後端工程師】 | `backend/app/Http/Controllers/Api`、`Admin` |

---

## 6. 附：list_banner 全部 key（改頂端 Banner 時用）

`multimedia`（type `0`／`1`／`2`／`3`）、`camera`（`mm`／`clarion`）、`dashcam`（`mm`／`clarion`）、`carFrame`、`fitting`、`safety`、`headUnit`、`audioAccessories`、`portable`、`headrest`、`clarionOverview`、`mmOverview`、`about`、`cases`、`qa`、`searchPage`、`download`、`partner`、`productLanding`（`landingHeadunit`／`landingAudio`／`landingDashcam`）。
沒有 type 的用 `PATCH /api/agent/list_banner/{page_key}/default`（以 `GET /api/agent/list_banner/all` 回的 `type_key` 為準）。

---

## 7. v3 新增：後台新功能與前台的連結（2026-10-01）

| 後台位置 | 資料存放 | 前台誰在讀 | 影響前台哪裡 |
|---|---|---|---|
| 產品管理 → 產品類別開關 | `website.content.categories` | `composables/useCategories.js`（Header、歌樂／美邁總覽頁）、`middleware/category-gate.global.js`（關閉的類別網址回 404） | 表頭下拉、總覽頁區塊與快速導覽；頁尾「產品」欄、/products/ 分類頁、sitemap 目前不受影響 |
| 網站基本設定 → 全站標誌圖片 | `website.content.logo_*` | `components/Header.vue`、`components/Footer.vue`、`app.vue`（分頁圖示） | 表頭左上角（白底／透明浮動／歌樂專區）、頁尾、分頁小圖示 |
| 系統設定 → SEO／GEO 設定 | `website.content.seo` | `composables/useSeoSettings.js`、`usePageSeo.js`、`useJsonLd.js`、`pages/about/index.vue`、`app.vue`、`server/routes/robots.txt.ts` | 24 頁的 title／description、og 分享圖、Organization／Product／FAQPage 結構化資料、關於我們頁問答、驗證碼 meta、robots.txt 的 AI 爬蟲規則 |
| 網站基本設定 → 全站底色 | `website.content.theme_*` | `app.vue` 產生 CSS 變數，約 37 個 `.vue` 使用 | 全站深色底、重點色、頁尾色、淺灰區塊色 |
| 產品頁 Banner 管理（文字與顏色） | `list_banners` 表（kicker／title／description／三組顏色） | `composables/useListBanner.js` 與 19 個頁面 | 各頁頂端 Banner 左側文字與顏色（只中文） |
| 首頁滿版區塊管理（底色） | `home_sections.bg_color` | `pages/index.vue`（`zoneColor`） | 首頁三個滿版區塊的底色 |
| 8 個產品頁＋首頁精選等的排序 | 各表 `sort` | 前台 API 排序（置頂→sort 小到大→id；首頁精選改小到大） | 各列表頁與首頁精選順序（規則見手冊 14.8） |
| 資源下載（只貼連結） | `resource.url` | `pages/download/index.vue` | /download：Google 雲端顯示「下載」、YouTube 顯示「觀看」 |

**仍然要找工程師的**：新增全新的商品類別（要新資料表、頁面、選單、sitemap）、改版面與元件、改頁面內寫死的文案（`locales`）、新增可逐頁設定 SEO 的頁面（`config/seo_pages.php` 加一行）、讓頁尾／sitemap／著陸頁也受類別開關控制。
