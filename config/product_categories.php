<?php

// 「產品類別開關」的預設清單（順序＝預設順序）。
// key 要和前台 frontend-v2/composables/useCategories.js 的 key 一致，不能亂改。
// on：沒設定過時預設是否顯示。 path：前台網址（直接輸入網址時，關閉的類別會顯示找不到頁面）。
return [
    'clarion' => [
        'name' => '歌樂 Clarion',
        'items' => [
            ['key' => 'gl',       'label' => '多媒體安卓機 GL', 'on' => 1, 'path' => '/clarion/gl'],
            ['key' => 'oem',      'label' => '車型專用機',       'on' => 1, 'path' => '/clarion/oem'],
            ['key' => 'audio',    'label' => '汽車音響',         'on' => 1, 'path' => '/audioAccessories'],
            ['key' => 'camera',   'label' => '鏡頭',             'on' => 1, 'path' => '/clarion/camera'],
            ['key' => 'din',      'label' => '車用主機 1/2DIN',  'on' => 1, 'path' => '/headUnit'],
            ['key' => 'dvr',      'label' => '行車記錄器',       'on' => 1, 'path' => '/clarion/dashcam'],
            ['key' => 'headrest', 'label' => '頭枕螢幕',         'on' => 0, 'path' => '/headrest'],
            ['key' => 'portable', 'label' => '可攜式',           'on' => 0, 'path' => '/portable'],
        ],
    ],
    'mm' => [
        'name' => '美邁 MM',
        'items' => [
            ['key' => 'android', 'label' => '多媒體安卓機',   'on' => 1, 'path' => '/mm/me'],
            ['key' => 'oem',     'label' => '車型專用機',     'on' => 1, 'path' => '/mm/oem'],
            ['key' => 'frame',   'label' => '安卓車框',       'on' => 1, 'path' => '/carFrame'],
            ['key' => 'safety',  'label' => '盲點偵測',       'on' => 1, 'path' => '/safety'],
            ['key' => 'dvr',     'label' => '行車記錄器',     'on' => 1, 'path' => '/mm/dashcam'],
            ['key' => 'camera',  'label' => '鏡頭',           'on' => 1, 'path' => '/mm/camera'],
            ['key' => 'fitting', 'label' => '車用配件',       'on' => 1, 'path' => '/fitting'],
        ],
    ],
];
