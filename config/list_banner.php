<?php

return [
    // 前台「產品列表／總覽」頁面清單，後台 Banner 管理畫面依這份清單顯示可設定的項目。
    // page_key：對應前台呼叫 /api/list_banner 時帶的 page 參數。
    // types 為 null：該頁只有單一 banner（type_key 固定用 'default'）。
    // types 為陣列：同一個元件被多個品牌／分類共用，各自可以設定不同 banner（type_key => 顯示用標籤）。
    'pages' => [
        'multimedia' => [
            'name' => '多媒體安卓機／車型專用機頁（共 4 個版本）',
            'types' => [
                '0' => 'MM 多媒體安卓機 ME 系列',
                '1' => 'MM 車型專用機',
                '2' => 'Clarion GL 系列',
                '3' => 'Clarion OEM 車型專用機',
            ],
        ],
        'camera' => [
            'name' => '鏡頭頁（MM、歌樂各一個）',
            'types' => [
                'mm' => 'MM 美邁',
                'clarion' => 'Clarion 歌樂',
            ],
        ],
        'dashcam' => [
            'name' => '行車記錄器頁（MM、歌樂各一個）',
            'types' => [
                'mm' => 'MM 美邁',
                'clarion' => 'Clarion 歌樂',
            ],
        ],
        'carFrame' => [
            'name' => '安卓車框',
            'types' => null,
        ],
        'fitting' => [
            'name' => '車用配件',
            'types' => null,
        ],
        'safety' => [
            'name' => '影像・安全',
            'types' => null,
        ],
        'headUnit' => [
            'name' => '車用主機 1/2DIN',
            'types' => null,
        ],
        'audioAccessories' => [
            'name' => '汽車音響',
            'types' => null,
        ],
        'portable' => [
            'name' => '可攜式',
            'types' => null,
        ],
        'headrest' => [
            'name' => '頭枕螢幕',
            'types' => null,
        ],
        'clarionOverview' => [
            'name' => 'Clarion 歌樂總覽頁',
            'types' => null,
        ],
        'mmOverview' => [
            'name' => 'MM 美邁總覽頁',
            'types' => null,
        ],

        // ── 2026-09-13 補齊：以下是原本沒有 Banner 欄位的頁面。
        // 全站尺寸一律：電腦版 1920×480（4:1）、手機版 1080×608（16:9）。
        // 沒上傳圖的頁面前台維持原本的樣子（純色／漸層標題帶），上傳了才會變成 Banner。
        'about' => [
            'name' => '品牌故事',
            'types' => null,
        ],
        'cases' => [
            'name' => '導入事例列表',
            'types' => null,
        ],
        'qa' => [
            'name' => '常見問題',
            'types' => null,
        ],
        'searchPage' => [
            'name' => '車型查詢結果頁',
            'types' => null,
        ],
        'download' => [
            'name' => '資源下載',
            'types' => null,
        ],
        'partner' => [
            'name' => '經銷據點',
            'types' => null,
        ],
        'productLanding' => [
            'name' => '產品分類入口頁（安卓主機／汽車音響／行車記錄器）',
            'types' => [
                'landingHeadunit' => '安卓主機入口頁',
                'landingAudio' => '汽車音響入口頁',
                'landingDashcam' => '行車記錄器入口頁',
            ],
        ],
    ],

    // 前台網址（後台列表顯示用，讓人知道這一列對應哪個頁面）。
    // 有 types 的頁面用「頁面代碼 => [分類代碼 => 網址]」，沒有 types 的頁面直接是網址字串。
    'urls' => [
        'carFrame' => '/carFrame',
        'fitting' => '/fitting',
        'safety' => '/safety',
        'headUnit' => '/headUnit',
        'audioAccessories' => '/audioAccessories',
        'portable' => '/portable',
        'headrest' => '/headrest',
        'clarionOverview' => '/clarion/overview',
        'mmOverview' => '/mm/overview',
        'about' => '/about',
        'cases' => '/cases',
        'qa' => '/qa',
        'searchPage' => '/searchPage',
        'download' => '/download',
        'partner' => '/partner',
        'multimedia' => ['0' => '/mm/me', '1' => '/mm/oem', '2' => '/clarion/gl', '3' => '/clarion/oem'],
        'camera' => ['mm' => '/mm/camera', 'clarion' => '/clarion/camera'],
        'dashcam' => ['mm' => '/mm/dashcam', 'clarion' => '/clarion/dashcam'],
        'productLanding' => ['landingHeadunit' => '/products/android-headunit', 'landingAudio' => '/products/car-audio', 'landingDashcam' => '/products/dash-cam'],
    ],
];
