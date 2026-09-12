<?php

return [
    // 首頁「滿版情境區塊」白名單（single source of truth，跟 config/list_banner.php 同一套做法）。
    // section_key 對應前台 pages/index.vue 呼叫 /api/home_section 拿到的 key。
    // 這裡的 name 只是後台顯示用的說明，不限定內容一定要放哪個品牌，管理者可以自己決定要放什麼。
    'sections' => [
        'zone1' => [
            'name' => '首頁區域 1（目前前台版位：clarion 滿版背景區，緊接在精選商品下面）',
        ],
        'zone2' => [
            'name' => '首頁區域 2（目前前台版位：MM 美邁滿版背景區）',
        ],
        'zone3' => [
            'name' => '首頁區域 3（目前前台版位：尾端 CTA 滿版背景區）',
        ],
    ],
];
