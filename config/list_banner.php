<?php

return [
    // 前台「產品列表／總覽」頁面清單，後台 Banner 管理畫面依這份清單顯示可設定的項目。
    // page_key：對應前台呼叫 /api/list_banner 時帶的 page 參數。
    // types 為 null：該頁只有單一 banner（type_key 固定用 'default'）。
    // types 為陣列：同一個元件被多個品牌／分類共用，各自可以設定不同 banner（type_key => 顯示用標籤）。
    'pages' => [
        'multimedia' => [
            'name' => '多媒體安卓機（共用元件，依分類區分）',
            'types' => [
                '0' => 'MM 多媒體安卓機 ME 系列',
                '1' => 'MM 車型專用機',
                '2' => 'Clarion GL 系列',
                '3' => 'Clarion OEM 車型專用機',
            ],
        ],
        'camera' => [
            'name' => '鏡頭（共用元件，依品牌區分）',
            'types' => [
                'mm' => 'MM 美邁',
                'clarion' => 'Clarion 歌樂',
            ],
        ],
        'dashcam' => [
            'name' => '行車記錄器（共用元件，依品牌區分）',
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
            'name' => '影像・安全',
            'types' => null,
        ],
        'safety' => [
            'name' => '盲點偵測',
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
            'name' => '關於我們',
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
            'name' => '產品分類頁（共用元件，依分類區分）',
            'types' => [
                'landingHeadunit' => '安卓主機（/products/android-headunit）',
                'landingAudio' => '汽車音響（/products/car-audio）',
                'landingDashcam' => '行車記錄器（/products/dash-cam）',
            ],
        ],
    ],
];
