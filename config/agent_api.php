<?php

/**
 * Agent API（/api/agent/...）設定。
 *
 * resources：每一個後台資源對應到「既有的 Admin 控制器」——Agent API 不重寫任何商業邏輯，
 *            驗證規則、欄位、首頁最多 3 筆這類限制，全部跟後台畫面按同一顆按鈕一模一樣。
 *   scope       權限群組名稱；金鑰要有 "{scope}:read" 才能 GET、"{scope}:write" 才能 POST/PATCH/DELETE
 *   controller  既有的 Admin 控制器（含命名空間）
 *   model       對應的 Model，用來在修改／刪除前拍快照寫進稽核紀錄；null 代表不拍
 *   request     既有的 FormRequest，/api/agent/schema 會把它的 rules() 印出來給 agent 看欄位
 *   actions     除了標準 CRUD（create/all/sort/find/update/delete/status）之外的額外動作
 *   style       'crud'（標準）／'kv'（list_banner、home_section 這種 patch {key} 的）／'single'（website/qa/about 只有 all+update）
 */
return [
    'header' => 'X-Agent-Key',

    'scopes' => [
        'products'  => '商品（多媒體、主機、行車記錄器、鏡頭、音響、盲點、車框、頭枕、可攜式、車型／品牌、首頁精選）',
        'banners'   => '首頁 Banner、列表頁 Banner、首頁滿版區塊',
        'cases'     => '導入事例',
        'dealers'   => '經銷據點',
        'resources' => '資源下載與分類',
        'settings'  => '網站基本設定、常見問題、關於我們',
        'files'     => '圖片上傳（只有 write）',
        'publish'   => '通知重新產生前台（只有 write）',
    ],

    'resources' => [
        // ── 商品 ──
        'car_media'             => ['scope' => 'products', 'controller' => 'App\Http\Controllers\Admin\Product\CarMediaController',            'model' => 'App\Models\CarMediaModel',            'request' => 'App\Http\Requests\Admin\Product\CarMediaResquest',            'actions' => ['top'],
            'list_params' => ['name'],
            'notes' => 'type：0=MM 多媒體安卓機（ME 系列）、1=MM 車型專用機、2=Clarion GL 系列、3=Clarion 車型專用機。memo=列表簡述、memo_in=詳情頁摘要（SEO description 用）、content=內文 HTML。'],
        'car_head_unit'         => ['scope' => 'products', 'controller' => 'App\Http\Controllers\Admin\Product\CarHeadUnitController',         'model' => 'App\Models\CarHeadUnitModel',         'request' => 'App\Http\Requests\Admin\Product\CarHeadUnitResquest',         'actions' => ['top']],
        'car_dashcam'           => ['scope' => 'products', 'controller' => 'App\Http\Controllers\Admin\Product\CarDashcamController',          'model' => 'App\Models\CarDashcamModel',          'request' => 'App\Http\Requests\Admin\Product\CarDashcamResquest',          'actions' => ['top']],
        'car_camera'            => ['scope' => 'products', 'controller' => 'App\Http\Controllers\Admin\Product\CarCameraController',           'model' => 'App\Models\CarCameraModel',           'request' => 'App\Http\Requests\Admin\Product\CarCameraResquest',           'actions' => ['top']],
        'car_audio_accessories' => ['scope' => 'products', 'controller' => 'App\Http\Controllers\Admin\Product\CarAudioAccessoriesController', 'model' => 'App\Models\CarAudioAccessoriesModel', 'request' => 'App\Http\Requests\Admin\Product\CarAudioAccessoriesResquest', 'actions' => ['top']],
        'car_headrest'          => ['scope' => 'products', 'controller' => 'App\Http\Controllers\Admin\Product\CarHeadrestController',         'model' => 'App\Models\CarHeadrestModel',         'request' => 'App\Http\Requests\Admin\Product\CarHeadrestResquest',         'actions' => ['top']],
        'car_portable'          => ['scope' => 'products', 'controller' => 'App\Http\Controllers\Admin\Product\CarPortableController',         'model' => 'App\Models\CarPortableModel',         'request' => 'App\Http\Requests\Admin\Product\CarPortableResquest',         'actions' => ['top']],
        'car_fitting'           => ['scope' => 'products', 'controller' => 'App\Http\Controllers\Admin\Product\CarFittingController',          'model' => 'App\Models\CarFittingModel',          'request' => 'App\Http\Requests\Admin\Product\CarFittingResquest',          'actions' => ['top']],
        'car_blind_spot'        => ['scope' => 'products', 'controller' => 'App\Http\Controllers\Admin\Product\CarBlindSpotController',        'model' => 'App\Models\CarBlindSpotModel',        'request' => 'App\Http\Requests\Admin\Product\CarBlindSpotResquest',        'actions' => ['top']   /* 原本還列了 spc，但 CarBlindSpotController 沒有 spc 方法（後台「規格綁定」按鈕只是跳頁），打了會 500，已移除 */],
        'car_blind_spot_format' => ['scope' => 'products', 'controller' => 'App\Http\Controllers\Admin\Product\CarBlindSpotFormatController',  'model' => 'App\Models\CarBlindSpotFormatModel',  'request' => 'App\Http\Requests\Admin\Product\CarBlindSpotFormatResquest',  'actions' => [], 'prefix' => 'car_blind_spot_format/{car_blind_spot}'],
        'car_frame'             => ['scope' => 'products', 'controller' => 'App\Http\Controllers\Admin\Product\CarFrameController',            'model' => 'App\Models\CarFrameModel',            'request' => 'App\Http\Requests\Admin\Product\CarFrameResquest',            'actions' => ['img' => 'deleteImg'],
            'list_params' => ['car_brand_id', 'car_id'],
            'notes' => '圖片不是單一 img 欄位，而是 imgArr[群組][序號]（每群最多 3 張，值為 upload 回傳的網址）：imgArr[0]=列表主圖（車框圖）、imgArr[1]=車框配件、imgArr[2]=實際安裝（完工照）、imgArr[3]=車框概觀。PATCH 時沒送的位置、或送 null 的位置都保留原圖（JSON 陣列跳不過前面的格子，用 null 佔位，例：imgArr:[[null,null,"新網址"]] 只換主圖第 3 張）；要刪某格才送空字串 ""（或用 PATCH /{id}/img）。網址請直接用 upload 回傳的 url（網域、%20 編碼都會自動修正，但路徑必須含 /storage/files/1/ 且檔案真的存在），對不到檔案會回 422 並指出是哪一格，不會再默默清空。刪單張圖：PATCH /{id}/img，body {"type":"img|img1|img2|img3","index":0}（img=主圖、img1=配件、img2=實際安裝、img3=概觀）。'],
        'car_brand'             => ['scope' => 'products', 'controller' => 'App\Http\Controllers\Admin\Product\CarBrandController',            'model' => 'App\Models\CarBrandModel',            'request' => 'App\Http\Requests\Admin\Product\CarBrandResquest',            'actions' => []],
        'car'                   => ['scope' => 'products', 'controller' => 'App\Http\Controllers\Admin\Product\CarController',                 'model' => 'App\Models\CarModel',                 'request' => 'App\Http\Requests\Admin\Product\CarResquest',                 'actions' => []],
        'recommend_product'     => ['scope' => 'products', 'controller' => 'App\Http\Controllers\Admin\RecommendProductController',            'model' => 'App\Models\RecommendProductModel',    'request' => 'App\Http\Requests\Admin\RecommendProductResquest',            'actions' => [], 'extra_get' => ['options']],
        // ── Banner ──
        'banner'                => ['scope' => 'banners',  'controller' => 'App\Http\Controllers\Admin\BannerController',                      'model' => 'App\Models\BannerModel',              'request' => 'App\Http\Requests\Admin\BannerResquest',                      'actions' => []],
        'list_banner'           => ['scope' => 'banners',  'controller' => 'App\Http\Controllers\Admin\ListBannerController',                  'model' => 'App\Models\ListBannerModel',          'request' => 'App\Http\Requests\Admin\ListBannerResquest',                  'style' => 'kv', 'key' => '{page_key}/{type_key}',
            'notes' => 'page_key／type_key 以 GET /all 回傳的清單為準（沒有 types 的頁面 type_key 用 default）。img=電腦版 1920×480、img_mobile=手機版 1080×608，值為 upload 回傳的網址。'],
        'home_section'          => ['scope' => 'banners',  'controller' => 'App\Http\Controllers\Admin\HomeSectionController',                 'model' => 'App\Models\HomeSectionModel',         'request' => 'App\Http\Requests\Admin\HomeSectionResquest',                 'style' => 'kv', 'key' => '{section_key}',
            'notes' => 'section_key：zone1／zone2／zone3（首頁三個滿版區塊，順序見 GET /all）。'],
        // ── 導入事例 ──
        'install_case'          => ['scope' => 'cases',    'controller' => 'App\Http\Controllers\Admin\InstallCaseController',                 'model' => 'App\Models\InstallCaseModel',         'request' => 'App\Http\Requests\Admin\InstallCaseResquest',                 'actions' => ['pinned', 'home']],
        // ── 經銷據點 ──
        'dealer'                => ['scope' => 'dealers',  'controller' => 'App\Http\Controllers\Admin\DealerController',                      'model' => 'App\Models\DealerModel',              'request' => 'App\Http\Requests\Admin\DealerResquest',                      'actions' => []],
        // ── 資源下載 ──
        'resource_category'     => ['scope' => 'resources','controller' => 'App\Http\Controllers\Admin\Resource\ResourceCategoryController',   'model' => 'App\Models\ResourceCategoryModel',    'request' => 'App\Http\Requests\Admin\Resource\ResourceCategoryResquest',   'actions' => []],
        'resource'              => ['scope' => 'resources','controller' => 'App\Http\Controllers\Admin\Resource\ResourceController',           'model' => 'App\Models\ResourceModel',            'request' => 'App\Http\Requests\Admin\Resource\ResourceResquest',           'actions' => []],
        // ── 設定 ──
        'website'               => ['scope' => 'settings', 'controller' => 'App\Http\Controllers\Admin\Setting\WebsiteInfoController',         'model' => null, 'request' => null, 'style' => 'single',
            'notes' => '整份 content 物件送回（先 GET /all 拿到 item.content，改欄位後 PATCH {"content": {...}}）。公司名稱、地址、電話要跟前台 composables/useSiteInfo.js 逐字一致。'],
        'qa'                    => ['scope' => 'settings', 'controller' => 'App\Http\Controllers\Admin\Setting\QaController',                  'model' => null, 'request' => null, 'style' => 'single'],
        'about'                 => ['scope' => 'settings', 'controller' => 'App\Http\Controllers\Admin\Setting\AboutController',               'model' => null, 'request' => null, 'style' => 'single'],
        // 內容來源與更正聲明（前台 /content-policy）：content = { zh: {title, intro, body}, en: {title, intro, body} }，可只送要改的語言／欄位，其餘維持；欄位空字串＝前台用內建預設文字
        'content_policy'        => ['scope' => 'settings', 'controller' => 'App\Http\Controllers\Admin\Setting\ContentPolicyController',       'model' => null, 'request' => null, 'style' => 'single',
            'notes' => 'PATCH {"content": {"zh": {"body": "<p>…</p>"}}} 只改中文內文，其他維持。body 是 HTML，title／intro 是純文字。清空某欄（送 ""）＝前台改用程式內建的預設文字。'],
        // 浮水印設定（後台「系統設定 → 浮水印設定」）：4 組固定槽位 key=0..3，只能換圖／還原，不能新增刪除；只影響「之後」存檔的車框圖片
        'watermark'             => ['scope' => 'settings', 'controller' => 'App\Http\Controllers\Admin\Setting\WatermarkController',           'model' => null, 'request' => null, 'style' => 'watermark',
            'notes' => 'GET all 看 4 組目前用的檔（is_custom 表示是否已自訂）。換圖：POST /api/agent/watermark/{key}（multipart，欄位 file，PNG 透明背景 ≤5MB，寬高 20×10～4000×4000）。還原預設：DELETE /api/agent/watermark/{key}（已是預設會回 422）。key 對應見 GET all 的 name。舊檔會自動備份到 watermark-config/backup/。換完只影響之後新存的車框圖，舊圖要重新存檔才會套用。'],
    ],

    // 圖片上傳：放到後台檔案管理員的同一棵目錄樹 files/1/<folder>，folder 可以多層（例：Clarion 2026/GL-700_Ultra_13/Chinese/Transparent/3840x2159），
    // 沒有的資料夾會自動建立；網址格式與檔案管理員一致，後台檔案管理員也看得到。
    'upload' => [
        'disk' => 'public',
        'base' => 'files/1',
        'default_folder' => 'Agent',
        'max_kb' => 20480,
        'max_files' => 20,                       // 一次最多幾個檔（files[]）
        'mimes' => 'jpg,jpeg,png,webp,gif,svg,pdf',
    ],

    // 各資源的圖片欄位（Agent API 會統一處理：給網址／外部網址／base64／直接帶檔案都可以，詳見 App\Support\AgentWriteHelper）
    // 'frame' 代表車框的 imgArr[群組 0..3][序號 0..2]
    'images' => [
        'car_media' => ['img'], 'car_head_unit' => ['img'], 'car_dashcam' => ['img'], 'car_camera' => ['img'],
        'car_audio_accessories' => ['img'], 'car_headrest' => ['img'], 'car_portable' => ['img'],
        'car_fitting' => ['img'], 'car_blind_spot' => ['img'],
        'car_frame' => 'frame',
        'banner' => ['img', 'img_mobile'], 'list_banner' => ['img', 'img_mobile'], 'home_section' => ['img', 'img_mobile'],
        'install_case' => ['img'],
    ],

    // 「重新產生前台」觸發網址（POST）；由工程師在 .env 設定 AGENT_DEPLOY_HOOK_URL，沒設定時 POST /api/agent/publish 會回 501
    'deploy_hook' => env('AGENT_DEPLOY_HOOK_URL'),

    // 每分鐘請求上限（每把金鑰）
    'rate_limit_per_minute' => 120,

    // GET /api/agent/audit 一頁幾筆
    'audit_per_page' => 50,
];
