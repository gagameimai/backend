<?php

return [
    'admin' => [
        // 單元
        'main' => [
            'icon' => 'fas fa-home',
            'name' => '主頁',
            'route' => 'main',
        ],

        'setting' => [
            'icon' => 'fa-solid fa-gear',
            'name' => '系統設定',
            'belongs' => [
                'qa' => [
                    'name' => '常見問題',
                    'route' => 'qa',
                ],
                'website' => [
                    'name' => '網站基本設定',
                    'route' => 'website',
                ],
            ]
        ],

        'banner' => [
            'icon' => 'fa-solid fa-image',
            'name' => 'Banner管理',
            'route' => 'banner',
        ],

        'resource' => [
            'icon' => 'fa-solid fa-folder-open',
            'name' => '資源下載管理',
            'belongs' => [
                'resource_category' => [
                    'name' => '分類管理',
                    'route' => 'resource_category',
                ],
                'resource' => [
                    'name' => '檔案管理',
                    'route' => 'resource',
                ],
            ]
        ],

        'dealer' => [
            'icon' => 'fa-solid fa-location-dot',
            'name' => '經銷據點管理',
            'route' => 'dealer',
        ],

        'product' => [
            'icon' => 'fa-brands fa-product-hunt',
            'name' => '產品管理',
            'belongs' => [
                'car_brand' => [
                    'name' => '汽車品牌',
                    'route' => 'car_brand',
                ],
                'car' => [
                    'name' => '汽車車款',
                    'route' => 'car',
                ],
                'car_frame' => [
                    'name' => '安卓車框',
                    'route' => 'car_frame',
                ],
                'car_blind_spot' => [
                    'name' => '盲點偵測',
                    'route' => 'car_blind_spot',
                ],
                'car_fitting' => [
                    'name' => '車用配件',
                    'route' => 'car_fitting',
                ],
                'car_media' => [
                    'name' => '多媒體機',
                    'route' => 'car_media',
                ],
            ]
        ]
    ],
];
