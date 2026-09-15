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
    ],

    'resources' => [
        // ── 商品 ──
        'car_media'             => ['scope' => 'products', 'controller' => 'App\Http\Controllers\Admin\Product\CarMediaController',            'model' => 'App\Models\CarMediaModel',            'request' => 'App\Http\Requests\Admin\Product\CarMediaResquest',            'actions' => ['top']],
        'car_head_unit'         => ['scope' => 'products', 'controller' => 'App\Http\Controllers\Admin\Product\CarHeadUnitController',         'model' => 'App\Models\CarHeadUnitModel',         'request' => 'App\Http\Requests\Admin\Product\CarHeadUnitResquest',         'actions' => ['top']],
        'car_dashcam'           => ['scope' => 'products', 'controller' => 'App\Http\Controllers\Admin\Product\CarDashcamController',          'model' => 'App\Models\CarDashcamModel',          'request' => 'App\Http\Requests\Admin\Product\CarDashcamResquest',          'actions' => ['top']],
        'car_camera'            => ['scope' => 'products', 'controller' => 'App\Http\Controllers\Admin\Product\CarCameraController',           'model' => 'App\Models\CarCameraModel',           'request' => 'App\Http\Requests\Admin\Product\CarCameraResquest',           'actions' => ['top']],
        'car_audio_accessories' => ['scope' => 'products', 'controller' => 'App\Http\Controllers\Admin\Product\CarAudioAccessoriesController', 'model' => 'App\Models\CarAudioAccessoriesModel', 'request' => 'App\Http\Requests\Admin\Product\CarAudioAccessoriesResquest', 'actions' => ['top']],
        'car_headrest'          => ['scope' => 'products', 'controller' => 'App\Http\Controllers\Admin\Product\CarHeadrestController',         'model' => 'App\Models\CarHeadrestModel',         'request' => 'App\Http\Requests\Admin\Product\CarHeadrestResquest',         'actions' => ['top']],
        'car_portable'          => ['scope' => 'products', 'controller' => 'App\Http\Controllers\Admin\Product\CarPortableController',         'model' => 'App\Models\CarPortableModel',         'request' => 'App\Http\Requests\Admin\Product\CarPortableResquest',         'actions' => ['top']],
        'car_fitting'           => ['scope' => 'products', 'controller' => 'App\Http\Controllers\Admin\Product\CarFittingController',          'model' => 'App\Models\CarFittingModel',          'request' => 'App\Http\Requests\Admin\Product\CarFittingResquest',          'actions' => ['top']],
        'car_blind_spot'        => ['scope' => 'products', 'controller' => 'App\Http\Controllers\Admin\Product\CarBlindSpotController',        'model' => 'App\Models\CarBlindSpotModel',        'request' => 'App\Http\Requests\Admin\Product\CarBlindSpotResquest',        'actions' => ['top', 'spc']],
        'car_blind_spot_format' => ['scope' => 'products', 'controller' => 'App\Http\Controllers\Admin\Product\CarBlindSpotFormatController',  'model' => 'App\Models\CarBlindSpotFormatModel',  'request' => 'App\Http\Requests\Admin\Product\CarBlindSpotFormatResquest',  'actions' => [], 'prefix' => 'car_blind_spot_format/{car_blind_spot}'],
        'car_frame'             => ['scope' => 'products', 'controller' => 'App\Http\Controllers\Admin\Product\CarFrameController',            'model' => 'App\Models\CarFrameModel',            'request' => 'App\Http\Requests\Admin\Product\CarFrameResquest',            'actions' => ['img' => 'deleteImg']],
        'car_brand'             => ['scope' => 'products', 'controller' => 'App\Http\Controllers\Admin\Product\CarBrandController',            'model' => 'App\Models\CarBrandModel',            'request' => 'App\Http\Requests\Admin\Product\CarBrandResquest',            'actions' => []],
        'car'                   => ['scope' => 'products', 'controller' => 'App\Http\Controllers\Admin\Product\CarController',                 'model' => 'App\Models\CarModel',                 'request' => 'App\Http\Requests\Admin\Product\CarResquest',                 'actions' => []],
        'recommend_product'     => ['scope' => 'products', 'controller' => 'App\Http\Controllers\Admin\RecommendProductController',            'model' => 'App\Models\RecommendProductModel',    'request' => 'App\Http\Requests\Admin\RecommendProductResquest',            'actions' => [], 'extra_get' => ['options']],
        // ── Banner ──
        'banner'                => ['scope' => 'banners',  'controller' => 'App\Http\Controllers\Admin\BannerController',                      'model' => 'App\Models\BannerModel',              'request' => 'App\Http\Requests\Admin\BannerResquest',                      'actions' => []],
        'list_banner'           => ['scope' => 'banners',  'controller' => 'App\Http\Controllers\Admin\ListBannerController',                  'model' => 'App\Models\ListBannerModel',          'request' => 'App\Http\Requests\Admin\ListBannerResquest',                  'style' => 'kv', 'key' => '{page_key}/{type_key}'],
        'home_section'          => ['scope' => 'banners',  'controller' => 'App\Http\Controllers\Admin\HomeSectionController',                 'model' => 'App\Models\HomeSectionModel',         'request' => 'App\Http\Requests\Admin\HomeSectionResquest',                 'style' => 'kv', 'key' => '{section_key}'],
        // ── 導入事例 ──
        'install_case'          => ['scope' => 'cases',    'controller' => 'App\Http\Controllers\Admin\InstallCaseController',                 'model' => 'App\Models\InstallCaseModel',         'request' => 'App\Http\Requests\Admin\InstallCaseResquest',                 'actions' => ['pinned', 'home']],
        // ── 經銷據點 ──
        'dealer'                => ['scope' => 'dealers',  'controller' => 'App\Http\Controllers\Admin\DealerController',                      'model' => 'App\Models\DealerModel',              'request' => 'App\Http\Requests\Admin\DealerResquest',                      'actions' => []],
        // ── 資源下載 ──
        'resource_category'     => ['scope' => 'resources','controller' => 'App\Http\Controllers\Admin\Resource\ResourceCategoryController',   'model' => 'App\Models\ResourceCategoryModel',    'request' => 'App\Http\Requests\Admin\Resource\ResourceCategoryResquest',   'actions' => []],
        'resource'              => ['scope' => 'resources','controller' => 'App\Http\Controllers\Admin\Resource\ResourceController',           'model' => 'App\Models\ResourceModel',            'request' => 'App\Http\Requests\Admin\Resource\ResourceResquest',           'actions' => []],
        // ── 設定 ──
        'website'               => ['scope' => 'settings', 'controller' => 'App\Http\Controllers\Admin\Setting\WebsiteInfoController',         'model' => null, 'request' => null, 'style' => 'single'],
        'qa'                    => ['scope' => 'settings', 'controller' => 'App\Http\Controllers\Admin\Setting\QaController',                  'model' => null, 'request' => null, 'style' => 'single'],
        'about'                 => ['scope' => 'settings', 'controller' => 'App\Http\Controllers\Admin\Setting\AboutController',               'model' => null, 'request' => null, 'style' => 'single'],
    ],

    // 圖片上傳：只允許放到這些資料夾（對應後台檔案管理員 files/1/<資料夾>），網址格式與檔案管理員一致
    'upload' => [
        'disk' => 'public',
        'base' => 'files/1',
        'folders' => ['Banner', 'ListBanner', 'HomeSection', 'MultiMedia', 'Din', 'Dashcam', 'Camera', 'AudioAccessories', 'Headrest', 'Portable', 'Fitting', 'BlindSpot', 'CarFrame', 'Case', 'Dealer', 'Resource', 'Agent'],
        'max_kb' => 8192,
        'mimes' => 'jpg,jpeg,png,webp,gif,pdf',
    ],

    // 每分鐘請求上限（每把金鑰）
    'rate_limit_per_minute' => 120,
];
