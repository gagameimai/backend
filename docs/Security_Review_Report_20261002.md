# 美邁官網 資安檢查報告（給工程師）

日期：2026-10-02　範圍：本機後台（Laravel 8）、前台（Nuxt 3）、Agent API、設定檔與資料夾

> 這是「看原始碼與設定」的靜態檢查，沒有掃描線上站台、沒有做入侵測試，也沒看到正式伺服器（nginx／PHP／資料庫權限）的設定。標「待確認」的項目，需要工程師到正式環境確認。

## 一、先看結論

- 沒有發現可以直接被外人利用、不用登入就能入侵的漏洞。
- 有 **1 項要最優先處理**：`.env.example` 裡放了真正的 APP_KEY（見 C1）。
- 有 4 項重要、3 項一般，另外有幾項已經做對的地方列在最後。

## 二、要處理的項目（依急迫程度排序）

### 緊急

**C1. APP_KEY 被放進 `.env.example` 並推到 GitHub（待確認正式站是否同一把）**

- 現況：`backend/.env.example` 裡的 APP_KEY 是真的金鑰，而且和你本機 `.env` 的 APP_KEY 完全相同。`.env.example` 有被 git 追蹤，遠端是 `gagameimai/backend`。（`.env` 本身沒有進過 git 歷史，這點是對的。）
- 風險：APP_KEY 是 Laravel 加密 Cookie、Session 的根金鑰。金鑰外洩的話，攻擊者可以偽造或解開加密資料；在 Laravel 8 這類舊版本，已知還有透過偽造 Cookie 達成遠端執行程式的手法。
- 要確認：(1) 這兩個 GitHub 倉庫是不是私人（private）；(2) 正式站用的 APP_KEY 是不是同一把。
- 建議：
  1. 正式站直接換新 APP_KEY（`php artisan key:generate`，所有人會被登出，需重新登入後台）。
  2. `.env.example` 的 APP_KEY 改成空白（`APP_KEY=`），本機與正式站也各自用不同的金鑰。
  3. 光是刪掉檔案裡的字不夠，因為 git 歷史裡還在，所以一定要換新金鑰。

### 重要

**H1. Laravel 8.83 已停止安全更新**

- `composer.lock` 是 laravel/framework v8.83.17。Laravel 8 的安全修補在 2023 年初就結束了，之後有新漏洞也不會有官方修補。
- 建議：排進升級計畫（Laravel 10 或 11，連同 PHP 版本一起），升級前先把正式站掛在防火牆（WAF，例如 Cloudflare）後面。另外在工程師電腦上跑一次 `composer audit`（我這邊連不上 packagist，無法執行）。

**H2. 後台登入沒有次數限制，也沒有第二層驗證**

- `LoginController@loginAuth` 沒有 throttle（`routes` 與 `Kernel` 都沒套用），可以無限次嘗試密碼。
- 建議：登入 POST 加上 `throttle:5,1`（每分鐘 5 次），並確認後台密碼夠強；若可行，後台網址（admin.meimai.com.tw）限制只有公司固定 IP 能連，或加上 Cloudflare Access 之類的第二層。

**H3. 正式站設定要確認（待確認）**

- 你本機的 `.env` 是 `APP_ENV=local`、`APP_DEBUG=true`、`LOG_LEVEL=debug`、`SESSION_DRIVER=file`，沒有設 `SESSION_SECURE_COOKIE`。這作為本機設定沒問題，但正式站必須是：`APP_DEBUG=false`、`APP_ENV=production`、`SESSION_SECURE_COOKIE=true`。
- 原因：debug 開著，任何錯誤頁都會把檔案路徑、SQL 語句、設定值顯示出來（我在測試時看到的錯誤回應就是這樣）。

**H4. 前台 15 處用 `v-html` 直接顯示後台內容，沒有過濾**

- 位置：首頁三個區塊、品牌故事、常見問題、內容來源與更正聲明、各產品詳情頁等。
- 風險：內容來源是後台管理員或 Agent API。如果後台帳號或 Agent 金鑰被盜，或 Hermes 被網頁上的惡意文字誘導（prompt injection）而寫入惡意程式碼，這些程式碼會直接在訪客瀏覽器執行。
- 建議（兩層都做更好）：(1) 後台存檔時用 HTMLPurifier 之類的工具過濾 `<script>`、`on*=` 事件與 `javascript:` 連結；(2) 前台加上 CSP 標頭。另外 Hermes 的金鑰權限（scope）只開需要的項目，不要預設給 `*`。

### 一般

**M1. Agent 抓圖片網址的防護有缺口（SSRF）**

- `AgentWriteHelper` 抓遠端圖片前會擋內網 IP，這點很好；但只檢查「第一個網址」的 IP，之後允許最多 3 次轉址，轉址後的網址沒有再檢查，也沒有防 DNS 偽造。
- 利用條件：要有有效的 Agent 金鑰。建議：關閉轉址（`allow_redirects => false`），或每一次轉址都重新檢查 IP。

**M2. 檔案管理員（UniSharp LFM）只擋 php／html／txt，其他類型沒設白名單**

- `config/lfm.php` 的 `disallowed_mimetypes` 只有 3 種，SVG（可夾帶 script）沒有被擋。只有登入後台的人能上傳，所以風險有限。
- 建議：改成白名單（圖片 jpg／png／webp／gif，外加需要的 pdf、zip），並確認正式伺服器設定不會執行 `/storage`、`/upload` 底下的 .php（待確認）。（LFM 的 `valid_file_mimetypes` 白名單我沒看完整，請工程師確認。）

**M3. 前台套件有已知漏洞，且有幾個沒釘版本**

- `npm audit`（只算 production 套件）：45 項，其中嚴重 1、高 29、中 15。大部分是建置與開發工具鏈相關，正式站只跑打包後的 `.output`，實際風險比數字小，但仍建議處理。
- 直接相關的：`nuxt`（3.21.8，有修正版可升）、`@nuxtjs/axios`（Nuxt 2 的模組，沒有修正版，`nuxt.config.ts` 的 modules 裡也沒有用到，看起來是殘留，請確認後移除）、`@vue/cli-plugin-eslint`（用不到的工具，建議移除）、`axios`。
- 另外 `package.json` 的 `"vue": "latest"` 沒有釘版本，每次重新安裝可能拿到不同版本；`Dockerfile` 用 `node:latest` 也一樣。建議釘死版本（例如 Node LTS）。
- 建議做法：開一個分支跑 `npm audit fix`，測試整站沒壞再合併。

**M4. 前台沒有設定安全標頭（待確認 nginx 層是否有設）**

- `nuxt.config.ts` 沒有 `X-Frame-Options`、`Content-Security-Policy`、`Strict-Transport-Security`、`X-Content-Type-Options`、`Referrer-Policy`。若 nginx 或 Cloudflare 已經加了就沒問題。
- 建議：用 securityheaders.com 或 Mozilla Observatory 對正式網址跑一次，缺什麼補什麼。

## 三、小項目

- **L1.** `master.blade.php`、`login.blade.php` 有 3 處 `toastr.success('{!! session(...) !!}')`，不跳脫。目前訊息都是固定中文所以沒事，將來如果訊息帶使用者輸入就會出問題，建議改成 `@json(...)`。
- **L2.** 專案根目錄有三個敏感檔：`agent key.txt`（Agent 金鑰明文）、`admin_202607221211.csv`（後台帳號資料，密碼是 bcrypt 雜湊，不是明碼）、`261001.tar`（637MB 備份，可能含 `.env`）。它們不在 git 倉庫裡（兩個 git 倉庫分別在 `backend/`、`frontend-v2/`，我確認過 `.env` 從沒進過歷史），但不要把整個資料夾分享給別人、也不要同步到沒加密的雲端硬碟。如果 Agent 金鑰曾貼到其他地方，請重新發一把。（我沒有打開這幾個檔案看內容。）
- **L3.** 公開 API 沒有流量限制（`Kernel` 裡 `throttle:api` 被註解掉）。要限流建議放在 Cloudflare／nginx 層，因為前台是伺服器端呼叫 API，從 Laravel 層用 IP 限流可能會誤擋自己的前台伺服器。
- **L4.** CORS 的 `allowed_origins` 是 `*`。公開唯讀 API 這樣沒問題，Agent API 需要金鑰所以也安全；如果日後有只給特定網站用的 API，再收緊即可。

## 四、已經做對的地方

- **SQL 注入：** 後端所有 `selectRaw`／`DB::raw`／`DB::update` 都是固定字串或強制轉成整數（例如排序用的 `update_when_case_string` 會把 id 轉 int、值轉數字），沒有直接把使用者輸入拼進 SQL。
- **後台權限：** 我列出全部 430 條路由，除了登入頁，全部都有 `role.auth` 或 `agent.key` 驗證；檔案管理員也有 `auth`。
- **CSRF：** 後台有啟用，沒有例外網址。
- **密碼：** 後台密碼用 bcrypt 雜湊；登入失敗訊息不會透露是帳號錯還是密碼錯。
- **Agent 金鑰：** 資料庫只存 SHA-256 雜湊、有權限範圍（scope）、每把每分鐘 120 次上限、每次寫入有稽核紀錄與改前快照、可停用。
- **Cookie：** `http_only` 已開，`same_site` 是 `lax`。
- **測試後門：** 我在雲端測試用的 `__testlogin` 路由只存在於我的測試副本，你本機沒有（我確認過）。
- **私密資訊：** 前台 `runtimeConfig` 只放公開網址，沒有放私密值。

## 五、這次沒有檢查到的

- 正式伺服器：nginx／Apache 設定、PHP 版本、檔案權限、資料庫帳號權限、備份存放位置、SSL 設定。
- GitHub 倉庫是否私人、有哪些人有權限。
- `composer audit`（連不上 packagist）。
- 實際入侵測試（不在這次範圍）。

## 六、建議的處理順序

1. **今天：** 確認 GitHub 倉庫是私人、正式站 APP_KEY 換新、`.env.example` 清掉金鑰（C1）。
2. **這週：** 確認正式站 `APP_DEBUG=false`（H3）；登入加次數限制（H2）；檢查 Hermes 的金鑰 scope（H4）。
3. **近期：** 後台內容存檔加過濾與 CSP（H4）；Agent 抓圖防護（M1）；LFM 改白名單（M2）；前台套件整理（M3）；安全標頭（M4）。
4. **規劃中：** Laravel 升級（H1）。
