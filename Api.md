# API 文檔

## 基本說明

- URL : <http://admin.meimai.com.tw/api/>

- 回傳格式 : json

## 回傳結果說明

成功情境

```json
{
    "result":[
        {}, {}, {}
    ]
    // or
    "result":{}
}
```

失敗情境

```json
{
    "message":"..."
}
```

## 功能列表

### *常見問題

請求位置：URL/question

請求方式：GET

回傳結果：

| 參數名 | 型別 | 說明 |
| -- | -- | -- |
| content | string | 內文 |

```json
{
    "result": {
        "content": "..."
    }
}
```

### *資源下載

請求位置：URL/resource

請求方式：GET

回傳結果：

| 參數名 | 型別 | 說明 |
| -- | -- | -- |
| *.name | string | 標題 |
| *.memo | string | 簡述 |
| *.resources | array | 分類下資料 |

分類下資料

| resources | 參數名 | 型別 | 說明 |
| -- | -- | -- | -- |
| - | *.name | string | 標題 |
| - | *.url | string | 下載url |

```json
{
    "result": [
        {
            "id": 2,
            "name": "...",
            "memo": "...",
            "resources": [
                {
                    "resource_category_id": 2,
                    "name": "...",
                    "url": "..."
                }, {...}
            ]
        }, {...}
    ]
}
```

### *經銷商據點

請求位置：URL/partner

請求方式：GET

Query參數 :

- county : county索引, ex 台北市 = 0, 新北市 = 1

回傳結果：

| 參數名 | 型別 | 說明 |
| -- | -- | -- |
| county | array | 縣市 |
| partner | array | 經銷商 |

經銷商

| partner | 參數名 | 型別 | 說明 |
| -- | -- | -- | -- |
| - | *.name | string | 標題 |
| - | *.tel | string | 聯絡電話 |
| - | *.county | string | 縣市 |
| - | *.address | string | 地址 |

```json
{
    "result": {
        "county": [
            "台北市",
            "新北市",
            "桃園市",
            "台中市",
            "台南市",
            "嘉義市",
            "高雄市",
            "新竹縣",
            "苗栗縣",
            "彰化縣",
            "南投縣",
            "雲林縣",
            "嘉義縣",
            "屏東縣",
            "宜蘭縣",
            "花蓮縣",
            "台東縣",
            "澎湖縣",
            "金門縣",
            "連江縣",
            "基隆市",
            "新竹市",
            "新竹縣"
        ],
        "partner": [
            {
                "name": "...",
                "tel": "...",
                "county": "...",
                "address": "..."
            }, {...}
        ]
    }
}
```

### *車用配件-列表

請求位置：URL/fitting

請求方式：GET

回傳結果：

| 參數名 | 型別 | 說明 |
| -- | -- | -- |
| *.id | int | 編號 |
| *.name | string | 名稱 |
| *.material | string | 材質 |
| *.power | string | 電源 |
| *.img | string | 圖片 |

```json
{
    "result": [
        {
            "id": 1,
            "name": "...",
            "material": "...",
            "power": "...",
            "img": "..."
        }, {...}
    ]
}
```

### *車用配件-詳情

請求位置：URL/fitting/{id}

請求方式：GET

Path參數 :

- id : 編號

回傳結果：

| 參數名 | 型別 | 說明 |
| -- | -- | -- |
| name | string | 名稱 |
| img | string | 圖片 |
| memo_in | string | 簡述 |
| content | string | 內容 |

```json
{
    "result":{
        "name": "...",
        "img": "...",
        "memo_in": "...",
        "content": "...."
    }
}
```

### *盲點偵測-列表

請求位置：URL/blindspot

請求方式：GET

回傳結果：

| 參數名 | 型別 | 說明 |
| -- | -- | -- |
| *.id | int | 編號 |
| *.name | string | 名稱 |
| *.img | string | 圖片 |

```json
{
    "result":[
        {
            "id": 1,
            "name": "...",
            "img": "..."
        }, {...}
    ]
}
```

### *盲點偵測-詳情

請求位置：URL/blindspot/{id}

請求方式：GET

Path參數 :

- id : 編號

回傳結果：

| 參數名 | 型別 | 說明 |
| -- | -- | -- |
| name | string | 名稱 |
| img | string | 圖片 |
| memo_in | string | 簡述 |
| content | string | 內容 |
| depend | array | 適用車款 |
| car_brand | array | 汽車品牌 |

適用車款

| depend | 參數名 | 型別 | 說明 |
| -- | -- | -- | -- |
| - | *.car_brand_id | int | 汽車品牌所屬編號 |
| - | *.style | string | 車款 |
| - | *.year | string | 年份 |
| - | *.spc | string | 規格 |

汽車品牌

| car_brand | 參數名 | 型別 | 說明 |
| -- | -- | -- | -- |
| - | *.id | int | 汽車編號 |
| - | *.name | string | 汽車品牌名稱 |

```json
{
    "result":{
        "name": "...",
        "img": "...",
        "memo_in": "...",
        "content": "...",
        "depend": [
            {
                "car_brand_id": 3,
                "style": "...",
                "year": "...",
                "spc": "..."
            }, {...}
        ],
        "car_brand": [
            {
                "id": 3,
                "name": "..."
            }, {...}
        ]
    }
}
```

### *汽車品牌與車種資訊

請求位置：URL/car

請求方式：GET

回傳結果：

| 參數名 | 型別 | 說明 |
| -- | -- | -- |
| car_brand | array | 汽車品牌 |
| car | array | 汽車種類 |

安卓車框

汽車品牌

| car_brand | 參數名 | 型別 | 說明 |
| -- | -- | -- | -- |
| - | *.id | int | 編號 |
| - | *.name | string | 名稱 |

汽車種類

| car | 參數名 | 型別 | 說明 |
| -- | -- | -- | -- |
| - | *.id | int | 編號 |
| - | *.car_brand_id | int | 汽車品牌編號 |
| - | *.name | string | 名稱 |
| - | *.year_start | int | 年份頭 |
| - | *.year_end | int | 年份尾 |

```json
{
    "result":{
        "car_brand": [
            {
                "id": 1,
                "name": "..."
            }, {...}
        ],
        "car": [
            {
                "car_brand_id": 2,
                "name": "...",
                "year_start": 2000,
                "year_end": 2022
            }, {...}
        ]
    }
}
```

### *安卓車框-列表

請求位置：URL/carframe

請求方式：GET

Query參數 :

- car_brand_id : 汽車品牌編號
- car_id : 汽車編號
- year : 年份，會去比對是否在year_start與year_end之間

回傳結果：

| 參數名 | 型別 | 說明 |
| -- | -- | -- |
| *.id | int | 編號 |
| *.name | string | 名稱 |
| *.year_start | int | 年份頭 |
| *.year_end | int | 年份尾 |
| *.size | string | 尺寸 |
| *.img | string | 圖片 |
| *.brand_name | string | 汽車種類名稱 |
| *.car_name | string | 汽車名稱 |

```json
{
    "result": [
        {
            "id": 1,
            "name": "...",
            "year_start": 2010,
            "year_end": 2022,
            "size": "...",
            "img": "...",
            "brand_name": "...",
            "car_name": "..."
        }, {...}
    ]
}
```

### *安卓車框-詳情

請求位置：URL/carframe/{id}

請求方式：GET

Path參數 :

- id : 編號

回傳結果：

| 參數名 | 型別 | 說明 |
| -- | -- | -- |
| name | string | 名稱 |
| img | string | 圖片 |
| content | string | 內容 |
| year_start | int | 年份頭 |
| year_end | int | 年份尾 |
| size | string | 尺寸 |
| brand_name | string | 汽車種類名稱 |
| car_name | string | 汽車名稱 |

```json
{
    "result":{
        "name": "...",
        "img": "...",
        "content": "...",
        "year_start": 2022,
        "year_end": 2022,
        "size": "...",
        "brand_name": "...",
        "car_name": "..."
    }
}
```

### *多媒體機-列表

請求位置：URL/multimedia

請求方式：GET

回傳結果：

| 參數名 | 型別 | 說明 |
| -- | -- | -- |
| *.id | int | 編號 |
| *.name | string | 名稱 |
| *.img | string | 圖片 |
| *.memo | string | 簡述 |
| *.size | string | 尺寸 |
| *.hard_drive | string | 硬碟 |
| *.price | string | 建議售價 |
| *.ram | string | 記憶體 |
| *.resolution | string | 解析度 |

```json
{
    "result":[
        {
            "id": 1,
            "name": "...",
            "img": "...",
            "memo": "...",
            "size": "...",
            "hard_drive": "...",
            "price": "...",
            "ram": "...",
            "resolution": "..."
        }, {...}
    ]
}
```

### *多媒體機-詳情

請求位置：URL/multimedia/{id}

請求方式：GET

Path參數 :

- id : 編號

回傳結果：

| 參數名 | 型別 | 說明 |
| -- | -- | -- |
| name | string | 名稱 |
| img | string | 圖片 |
| memo_in | string | 簡述 |
| content | string | 內容 |

```json
{
    "result":{
        "name": "...",
        "img": "...",
        "memo_in": "...",
        "content": "..."
    }
}
```

### *Banner

請求位置：URL/banner

請求方式：GET

回傳結果：

| 參數名 | 型別 | 說明 |
| -- | -- | -- |
| name | string | 名稱 |
| img | string | 圖片 |
| url | string | 連結 |

```json
{
    "result":[
        {
            "name": "...",
            "img": "...",
            "url": "..."
        }, {...}
    ]
}
```

### *網站基本參數

請求位置：URL/website

請求方式：GET

回傳結果：

| 參數名 | 型別 | 說明 |
| -- | -- | -- |
| copyright | string | copyright |
| address | string | 地址 |
| tel | string | 聯絡電話 |
| email | string | mail |
| facebook | string | facebook連結 |
| instagram | string | instagram連結 |
| youtube | string | youtube連結 |

```json
{
    "result":{
        "copyright": "test",
        "address": "test",
        "tel": "test",
        "email": "test@gmail.com",
        "facebook": "https://tw.yahoo.com/",
        "youtube": "https://tw.yahoo.com/",
        "instagram": "https://tw.yahoo.com/"
    }
}
```

### *關鍵字搜尋

備註：針對安卓車框關鍵字搜尋，其餘會全部顯示

請求位置：URL/search

請求方式：GET

Query參數：

- car_brand_id : 汽車品牌編號
- car_id : 汽車編號
- year : 年份，會去比對是否在year_start與year_end之間

回傳結果：

| 參數名 | 型別 | 說明 |
| -- | -- | -- |
| car_frame | array | 安卓車框 |
| car_media | array | 多媒體機 |
| car_blind_spot | array | 盲點偵測 |
| car_fitting | array | 車用配件 |

安卓車框

| car_frame | 參數名 | 型別 | 說明 |
| -- | -- | -- | -- |
| - | *.id | int | 編號 |
| - | *.name | string | 名稱 |
| - | *.img | string | 圖片 |
| - | *.year_start | int | 年份頭 |
| - | *.year_end | int | 年份尾 |
| - | *.size | string | 尺寸 |
| - | *.brand_name | string | 汽車種類名稱 |
| - | *.car_name | string | 汽車名稱 |

多媒體機

| car_media | 參數名 | 型別 | 說明 |
| -- | -- | -- | -- |
| - | *.id | int | 編號 |
| - | *.name | string | 名稱 |
| - | *.img | string | 圖片 |

盲點偵測

| car_blind_spot | 參數名 | 型別 | 說明 |
| -- | -- | -- | -- |
| - | *.id | int | 編號 |
| - | *.name | string | 名稱 |
| - | *.img | string | 圖片 |

車用配件

| car_fitting | 參數名 | 型別 | 說明 |
| -- | -- | -- | -- |
| - | *.id | int | 編號 |
| - | *.name | string | 名稱 |
| - | *.img | string | 圖片 |

```json
{
    "result":{
        "car_frame":[
            {
                "id": 1,
                "name": "...",
                "img": "...",
                "year_start": 2022,
                "year_end": 2022,
                "size": "...",
                "brand_name": "...",
                "car_name": "..."
            }, {...}
        ],
        "car_media":[
             {
                "id": 1,
                "name": "...",
                "img": "..."
            }, {...}
        ],
        "car_blind_spot":[
             {
                "id": 1,
                "name": "...",
                "img": "..."
            }, {...}
        ],
        "car_fitting":[
             {
                "id": 1,
                "name": "...",
                "img": "..."
            }, {...}
        ]
    }
}
```
