<?php

/**
 * Agent API 欄位白話說明（GET /api/agent/schema 會附上 docs）。
 * 每個資源：label＝後台叫什麼、shows_on＝前台哪個頁面會用到、links＝跟哪些資源有關聯、fields＝每個欄位做什麼。
 * 共通：status 1=啟用（前台看得到）0=停用；is_top 1=置頂；前台是靜態網站，後台改完要重新產生前台才看得到。
 */
$common = [
    'status' => '狀態：1=啟用（前台顯示）、0=停用（前台不顯示）',
    'is_top' => '置頂：1=排在列表最前面、0=一般排序',
    'sort' => '排序數字，小的在前',
    'content' => '內文／產品規格，HTML（前台直接顯示；內文裡的圖片網址不在圖片欄位裡，要另外改）',
];
$std = ['is_top' => $common['is_top'], 'status' => $common['status']];

return [
    'common' => $common,
    'image_input' => [
        'summary' => '圖片欄位可以直接給：①upload 回傳的 url ②任何 https 圖片網址（伺服器自己下載）③data:image/png;base64,… ④multipart 直接帶檔案（欄位名稱同圖片欄位）。伺服器會存進檔案管理員 files/1/ 並換成站內網址。',
        'patch' => 'PATCH 沒送的欄位沿用現有值，只要送要改的欄位；要清空某張圖才送空字串。',
        'result' => '回應會帶 saved_images（實際存進去的網址）；送了圖但存進去是空的會帶 warnings。',
    ],
    'resources' => [
        'car_media' => [
            'label' => '多媒體安卓機／車型專用機',
            'shows_on' => 'type 0：/mm/me（MM 多媒體安卓機 ME 系列）；type 1：/mm/oem（MM 車型專用機）；type 2：/clarion/gl（Clarion GL 系列）；type 3：/clarion/oem（Clarion 車型專用機）。詳情頁 /multimediaDetail/{id}。',
            'links' => ['recommend_product（product_type 選到多媒體時，當商品頁的推薦搭配）'],
            'fields' => [
                'type' => '分類：0=MM ME 系列、1=MM 車型專用機、2=Clarion GL、3=Clarion 車型專用機（決定出現在哪個列表頁、用哪個品牌配色）',
                'name' => '型號名稱（列表與詳情標題）', 'img' => '列表／詳情主圖，建議 1200×1200 白底或去背',
                'memo' => '列表卡片上的簡短描述', 'memo_in' => '詳情頁摘要（同時是 SEO description）',
                'size' => '螢幕尺寸（吋）', 'hard_drive' => '硬碟／儲存容量', 'ram' => '記憶體', 'resolution' => '螢幕解析度', 'price' => '建議售價',
                'content' => $common['content'],
            ] + $std,
        ],
        'car_head_unit' => [
            'label' => '車用主機 1/2DIN（Clarion）', 'shows_on' => '/headUnit、詳情 /headUnitDetail/{id}',
            'fields' => ['type' => '0=1DIN、1=2DIN', 'name' => '型號名稱', 'img' => '列表主圖', 'size' => '尺寸', 'hard_drive' => '硬碟', 'ram' => '記憶體', 'resolution' => '解析度', 'price' => '建議售價', 'content' => $common['content']] + $std,
        ],
        'car_dashcam' => [
            'label' => '行車記錄器', 'shows_on' => 'brand 0：/mm/dashcam、詳情 /mm/dashcamDetail/{id}；brand 1：/clarion/dashcam、詳情 /clarion/dashcamDetail/{id}',
            'fields' => ['brand' => '品牌：0=MM 美邁、1=Clarion 歌樂（決定列表頁與配色）', 'name' => '型號名稱', 'img' => '列表主圖', 'content' => $common['content']] + $std,
        ],
        'car_camera' => [
            'label' => '鏡頭', 'shows_on' => 'brand 0：/mm/camera、詳情 /mm/cameraDetail/{id}；brand 1：/clarion/camera、詳情 /clarion/cameraDetail/{id}',
            'fields' => ['brand' => '品牌：0=MM 美邁、1=Clarion 歌樂', 'name' => '型號名稱', 'img' => '列表主圖', 'content' => $common['content']] + $std,
        ],
        'car_audio_accessories' => [
            'label' => '汽車音響（Clarion）', 'shows_on' => '/audioAccessories、詳情 /clarion/audioAccessoriesDetail/{id}',
            'fields' => ['type' => '0=一般喇叭、1=高音喇叭、2=重低音、3=擴大機、4=DSP', 'name' => '型號名稱', 'img' => '列表主圖', 'content' => $common['content']] + $std,
        ],
        'car_headrest' => ['label' => '頭枕螢幕（前台導覽已隱藏）', 'shows_on' => '/headrest、詳情 /headrestDetail/{id}',
            'fields' => ['name' => '型號名稱', 'img' => '列表主圖', 'content' => $common['content']] + $std],
        'car_portable' => ['label' => '可攜式（前台導覽已隱藏）', 'shows_on' => '/portable、詳情 /portableDetail/{id}',
            'fields' => ['name' => '型號名稱', 'img' => '列表主圖', 'content' => $common['content']] + $std],
        'car_fitting' => [
            'label' => '車用配件（MM）', 'shows_on' => '/fitting、詳情 /fittingDetail/{id}',
            'fields' => ['name' => '配件名稱', 'img' => '列表主圖', 'material' => '材質', 'power' => '電源', 'content' => $common['content']] + $std,
            'notes' => '行車記錄器與鏡頭已各自獨立分類，不要再放進車用配件。',
        ],
        'car_blind_spot' => [
            'label' => '影像・安全／盲點偵測（MM）', 'shows_on' => '/safety、詳情 /safetyDetail/{id}',
            'links' => ['car_blind_spot_format：這個商品「適用車款」表，掛在商品底下'],
            'fields' => ['name' => '商品名稱', 'img' => '列表主圖', 'content' => $common['content']] + $std,
        ],
        'car_blind_spot_format' => [
            'label' => '盲點偵測適用車款（掛在 car_blind_spot/{id} 底下）', 'shows_on' => '/safetyDetail/{id} 的「適用車款」查詢表',
            'links' => ['car_brand（car_brand_id）'],
            'fields' => ['car_brand_id' => '汽車品牌（car_brand 的 id）', 'style' => '車款名稱（文字）', 'year' => '適用年份（文字，例 2019-2023）', 'spc' => '規格／安裝備註', 'status' => $common['status']],
        ],
        'car_frame' => [
            'label' => '安卓車框', 'shows_on' => '/carFrame（車廠→車款→年份查詢）、詳情 /carFrameDetail/{id}、首頁快搜與車型查詢結果',
            'links' => ['car_brand（car_brand_id 車廠）', 'car（car_id 車款，必須屬於該車廠）'],
            'fields' => [
                'car_brand_id' => '車廠（car_brand 的 id）', 'car_id' => '車款（car 的 id）', 'year_start' => '適用起始年份', 'year_end' => '適用結束年份',
                'size' => '可搭主機尺寸（吋）', 'name' => '這一款車框的名稱／顏色（同年份有多款時用來區分，例：棕色面板）', 'content' => '內容敘述 HTML',
                'imgArr' => '圖片：imgArr[0][0..2]=車框主圖（列表與比對用）、imgArr[1]=車框配件圖、imgArr[2]=實際安裝完工照、imgArr[3]=車框概觀（前台當第 2 張圖）；每組最多 3 張',
                'watermarkArr' => '浮水印位置（選填，同 imgArr 結構）：0=不加、1 左上、2 左下、3 右上、4 右下、5 置中、-1 全圖',
                'status' => $common['status'],
            ],
        ],
        'car_brand' => ['label' => '汽車品牌（車廠）', 'shows_on' => '車框、導入事例、盲點適用車款的下拉；首頁快搜',
            'links' => ['car（車款屬於車廠）'], 'fields' => ['name' => '車廠名稱，格式「TOYOTA 豐田」（英文在前、中文在後；沒有中文就只有英文）', 'status' => $common['status']],
            'notes' => '首頁快搜固定熱門：TOYOTA、MITSUBISHI、HONDA、SUZUKI、NISSAN、HYUNDAI、MAZDA、FORD、BENZ、BMW、VOLKSWAGEN、AUDI（名稱要對得上英文）。'],
        'car' => ['label' => '汽車車款', 'shows_on' => '車框與導入事例的車款下拉', 'links' => ['car_brand（car_brand_id）'],
            'fields' => ['car_brand_id' => '所屬車廠', 'name' => '車款名稱', 'year_start' => '起始年份', 'year_end' => '結束年份', 'status' => $common['status']]],
        'recommend_product' => ['label' => '首頁精選商品', 'shows_on' => '首頁「精選商品」區（前台 API /api/recommend_products，名稱／圖／連結由後端依 product_type＋product_id 自動組出；商品被刪或停用不會顯示）；排序數字小的在前',
            'fields' => ['product_type' => '商品類型（見 GET /recommend_product/options 的可選值）', 'product_id' => '該類型商品的 id', 'sort' => $common['sort'], 'status' => $common['status']]],
        'banner' => ['label' => '首頁輪播 Banner', 'shows_on' => '首頁頂端輪播',
            'fields' => ['name' => 'Banner 名稱（後台辨識用）', 'url' => '點擊後前往的外部連結（可空）', 'img' => '電腦版圖', 'img_mobile' => '手機版圖（沒傳就用電腦版）', 'status' => $common['status']]],
        'list_banner' => ['label' => '列表頁 Banner', 'shows_on' => '各產品列表／總覽／品牌故事等頁頂端，用 page_key／type_key 指定哪一頁',
            'fields' => ['img' => '電腦版 1920×480（4:1）', 'img_mobile' => '手機版 1080×608（16:9）', 'kicker' => '小標題（選填，不填沿用頁面原本文字）', 'title' => '大標題（選填）', 'description' => '說明文字（選填，建議 60 字內）', 'kicker_color' => '小標題顏色 #RRGGBB（選填）', 'title_color' => '大標題顏色 #RRGGBB（選填）', 'desc_color' => '說明文字顏色 #RRGGBB（選填）']],
        'home_section' => ['label' => '首頁滿版區塊', 'shows_on' => '首頁三個滿版區塊，section_key=zone1／zone2／zone3',
            'fields' => ['content' => '區塊文字 HTML', 'img' => '背景圖電腦版', 'img_mobile' => '背景圖手機版', 'bg_color' => '區塊底色 #RRGGBB（選填，不填用全站深色底）']],
        'install_case' => ['label' => '導入事例（安裝案例）', 'shows_on' => '/cases、/cases/{id}；is_home=1 的最多 3 筆顯示在首頁',
            'links' => ['car_brand（car_brand_id）', 'car（car_id）', 'dealer 欄位是施工據點文字，不是 dealer 資源的 id'],
            'fields' => ['category' => '分類 0多媒體安卓機 1車型專用機 2汽車音響 3行車記錄器 4影像・安全 5車用配件 6車用主機1/2DIN 7頭枕螢幕 8可攜式', 'name' => '案例名稱', 'img' => '案例圖 1600×1000（16:10）實拍',
                'car_brand_id' => '車廠', 'car_id' => '車款', 'product' => '使用產品（文字）', 'dealer' => '施工據點（文字）', 'need' => '客戶需求', 'work' => '施工內容', 'installed_at' => '安裝日期',
                'is_pinned' => '置頂 1/0', 'is_home' => '顯示在首頁 1/0（最多 3 筆）', 'home_sort' => '首頁排序', 'sort' => $common['sort'], 'status' => $common['status']]],
        'dealer' => ['label' => '經銷據點', 'shows_on' => '/partner', 'fields' => ['name' => '店名', 'county' => '縣市：給名稱（例：桃園市）或 config/county.php 的代碼（0 台北市、1 新北市、2 桃園市、3 台中市、4 台南市、5 嘉義市、6 高雄市、7 新竹縣、8 苗栗縣、9 彰化縣、10 南投縣、11 雲林縣、12 嘉義縣、13 屏東縣、14 宜蘭縣、15 花蓮縣、16 台東縣、17 澎湖縣、18 金門縣、19 連江縣、20 基隆市、21 新竹市）；GET 回傳的是代碼，前台用來篩選', 'address' => '地址', 'tel' => '電話', 'status' => $common['status']]],
        'resource_category' => ['label' => '資源下載分類', 'shows_on' => '/download 的分類', 'fields' => ['name' => '分類名稱', 'memo' => '簡述', 'status' => $common['status']]],
        'resource' => ['label' => '資源下載檔案', 'shows_on' => '/download', 'links' => ['resource_category（resource_category_id）'],
            'fields' => ['resource_category_id' => '所屬分類', 'name' => '檔案名稱', 'url' => '下載連結（外部網址）', 'status' => $common['status']]],
        'website' => ['label' => '網站基本設定', 'shows_on' => '頁尾、聯絡資訊、全站色彩、Logo、前台選單類別開關、SEO／GEO', 'notes' => '先 GET 整份、只改自己的鍵、整份送回（部署第 129 項後頂層鍵會合併，但 categories、seo 是巢狀物件，要完整帶回）。content 內另有：theme_dark／theme_footer／theme_accent／theme_bg2（顏色 #RRGGBB）、logo_favicon／logo_cobrand_dark／logo_cobrand_white／logo_clarion_dark／logo_clarion_white／logo_footer（圖片網址，空＝預設）、categories（產品類別開關 {clarion:[{key,name,on}],mm:[…]}）、seo（SEO／GEO 設定）。詳見手冊第 14 章。公司名稱、地址、電話要和前台 composables/useSiteInfo.js 逐字一致。'],
        'qa' => ['label' => '常見問題', 'shows_on' => '/qa'],
        'content_policy' => ['label' => '內容來源與更正聲明', 'shows_on' => '/content-policy（頁尾「關於」欄的連結）', 'fields' => ['content.zh.title' => '中文標題（純文字）', 'content.zh.intro' => '中文標題帶下方的一段說明（純文字）', 'content.zh.body' => '中文內文（HTML）', 'content.en.title' => '英文標題', 'content.en.intro' => '英文說明', 'content.en.body' => '英文內文（HTML）'], 'notes' => '前台切換中文／EN 會各自顯示對應語言；某欄留空＝前台用程式內建文字。'],
        'about' => ['label' => '關於我們／品牌故事', 'shows_on' => '/about（內容是後台貼的 HTML，圖片與 logo 在內容裡，不在圖片欄位）'],
        'watermark' => ['label' => '系統設定 → 浮水印設定', 'shows_on' => '不直接顯示在前台；車框（car_frame）存檔時會把浮水印蓋到圖片上，watermarkArr 選的位置用哪一組浮水印就看這裡', 'links' => 'car_frame 的 watermarkArr',
            'fields' => ['key' => '0～3 固定 4 組槽位，GET all 的 name 說明各組用途', 'file' => '上傳用：PNG 透明背景、≤5MB、寬高 20×10～4000×4000', 'is_custom' => '讀取用：true＝已換成自訂圖，false＝用程式內建預設圖', 'url' => '讀取用：目前實際使用的浮水印檔網址']],
    ],
];
