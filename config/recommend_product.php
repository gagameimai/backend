<?php

// 首頁「精選商品」對照表：product_type（字串 key，存進 recommend_products.product_type）
// 對應到哪個 Model、後台下拉選單顯示的名稱、以及是否有 brand 欄位（影響前台連結路徑）。
// 新增產品分類時，只要在這裡多加一筆設定，後台下拉選單與前台 API 會自動支援，
// 不用去改 Controller 的邏輯（實際的名稱／圖片／連結組裝邏輯在 App\Services\RecommendProductResolver）。

return [
    'types' => [
        'car_frame' => [
            'label' => '安卓車框',
            'model' => \App\Models\CarFrameModel::class,
        ],
        'car_media' => [
            'label' => '多媒體安卓機／車型專用機',
            'model' => \App\Models\CarMediaModel::class,
        ],
        'car_blind_spot' => [
            'label' => '盲點偵測',
            'model' => \App\Models\CarBlindSpotModel::class,
        ],
        'car_dashcam' => [
            'label' => '行車記錄器',
            'model' => \App\Models\CarDashcamModel::class,
            'has_brand' => true,
        ],
        'car_camera' => [
            'label' => '鏡頭',
            'model' => \App\Models\CarCameraModel::class,
            'has_brand' => true,
        ],
        'car_fitting' => [
            'label' => '車用配件',
            'model' => \App\Models\CarFittingModel::class,
        ],
        'car_headrest' => [
            'label' => '頭枕螢幕',
            'model' => \App\Models\CarHeadrestModel::class,
        ],
        'car_portable' => [
            'label' => '可攜式',
            'model' => \App\Models\CarPortableModel::class,
        ],
        'car_audio_accessories' => [
            'label' => '汽車音響',
            'model' => \App\Models\CarAudioAccessoriesModel::class,
        ],
        'car_head_unit' => [
            'label' => '車用主機 1/2DIN',
            'model' => \App\Models\CarHeadUnitModel::class,
        ],
    ],
];
