<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * 把「前台目前實際顯示的 Banner 左邊文字」（小標／大標題／說明）一次寫進 list_banners，
 * 這樣後台打開每一頁就看得到真正的文字，直接改就行，不是空白欄位、也沒有藏在程式裡的預設值。
 *
 * 文字來源：frontend-v2/locales/zh-tw.js（2026-10-02 當下的內容）。
 * 只補「還是空的欄位」，已經有人在後台填過的內容不會被覆蓋；可重複執行。
 */
class SeedListBannerTexts extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('list_banners', 'title')) {
            return;
        }

        $rows = json_decode(<<<'JSON'
[
 {
  "page_key": "multimedia",
  "type_key": "0",
  "kicker": "MEIMAI ｜ 多媒體安卓機",
  "title": "多媒體安卓機",
  "description": "通用型車用安卓多媒體主機（俗稱安卓機），大螢幕、手機互聯、多媒體娛樂一次到位。MEIMAI ME 系列，多年在地研發與售後支援。"
 },
 {
  "page_key": "multimedia",
  "type_key": "1",
  "kicker": "MEIMAI ｜ 車型專用機",
  "title": "車型專用機",
  "description": "依車型開模的專用主機，保留原車空調、方向盤按鍵與內裝造型，免改線、免大幅施工。MM 美邁車型專用機，安裝快、相容性穩，售後由全台經銷據點支援。"
 },
 {
  "page_key": "multimedia",
  "type_key": "2",
  "kicker": "CLARION 歌樂 ｜ 多媒體安卓機",
  "title": "多媒體安卓機",
  "description": "Clarion 歌樂車用安卓多媒體主機，多尺寸、多規格可選，由台灣總經銷引進，全台授權據點供應與安裝。"
 },
 {
  "page_key": "multimedia",
  "type_key": "3",
  "kicker": "CLARION 歌樂 ｜ 車型專用機",
  "title": "車型專用機",
  "description": "Clarion 歌樂車型專用機，依原車面板開模，保留原車功能與內裝造型，由台灣官方授權總經銷引進，全台授權據點供應與安裝。"
 },
 {
  "page_key": "camera",
  "type_key": "mm",
  "kicker": "鏡頭 ｜ MM 美邁",
  "title": "鏡頭",
  "description": "倒車鏡頭、360 環景鏡頭等影像裝置，補齊行車視野死角，倒車、變換車道更安心。"
 },
 {
  "page_key": "camera",
  "type_key": "clarion",
  "kicker": "鏡頭 ｜ Clarion 歌樂",
  "title": "鏡頭",
  "description": "倒車鏡頭、360 環景鏡頭等影像裝置，補齊行車視野死角，倒車、變換車道更安心。"
 },
 {
  "page_key": "dashcam",
  "type_key": "mm",
  "kicker": "行車記錄器 ｜ MM 美邁",
  "title": "行車記錄器",
  "description": "行車事故蒐證影像記錄，隱藏式、多路錄影等多種選擇，行車更安心、更有保障。"
 },
 {
  "page_key": "dashcam",
  "type_key": "clarion",
  "kicker": "行車記錄器 ｜ Clarion 歌樂",
  "title": "行車記錄器",
  "description": "行車事故蒐證影像記錄，隱藏式、多路錄影等多種選擇，行車更安心、更有保障。"
 },
 {
  "page_key": "carFrame",
  "type_key": "default",
  "kicker": "MEIMAI ｜ 安卓車框",
  "title": "安卓車框",
  "description": "把安卓多媒體主機無縫嵌入原車面板的專用車框，多車廠車款專用開模。先「選車型」找到你的車，再看實際安裝範例。"
 },
 {
  "page_key": "cases",
  "type_key": "default",
  "kicker": "CLARION × MM ｜ 導入事例",
  "title": "導入事例",
  "description": "全台授權經銷據點的實際安裝案例：車型、搭配產品、客戶需求與施工重點，提供選購與評估時的參考。"
 },
 {
  "page_key": "download",
  "type_key": "default",
  "kicker": "Downloads ／ 資源下載",
  "title": "驅動 · 韌體 · 說明書",
  "description": "MM 美邁各機型的系統升級檔、APK、說明書與操作影片，依機型分類，隨時取用。"
 },
 {
  "page_key": "partner",
  "type_key": "default",
  "kicker": "Dealers ／ 經銷據點",
  "title": "全台授權經銷據點",
  "description": "選擇你所在的縣市，就近找到購買、安裝與保固的合作店家。"
 },
 {
  "page_key": "headrest",
  "type_key": "default",
  "kicker": "Headrests ｜ 頭枕螢幕",
  "title": "頭枕螢幕",
  "description": "後座影音娛樂螢幕，高畫質面板、觸控操作，長途乘車更舒適。"
 },
 {
  "page_key": "portable",
  "type_key": "default",
  "kicker": "Portable ｜ 可攜式",
  "title": "可攜式產品",
  "description": "Clarion 原廠可攜式產品線，免安裝、隨插即用，靈活搭配你的用車情境。"
 },
 {
  "page_key": "qa",
  "type_key": "default",
  "kicker": "FAQ ＆ WARRANTY ｜ 常見問題與保固",
  "title": "常見問題與保固說明",
  "description": "快速找到解答，從安裝、導航更新到保固申請，一頁看懂。若有疑義，請洽原安裝經銷據點。"
 },
 {
  "page_key": "searchPage",
  "type_key": "default",
  "kicker": "VEHICLE FINDER ｜ 選車型",
  "title": "選車型搜尋",
  "description": "選擇車廠、車款與年份，快速找到相容的車框、多媒體機與影像 · 安全配備。"
 },
 {
  "page_key": "about",
  "type_key": "default",
  "kicker": "品牌故事 ｜ Clarion × MM",
  "title": "品牌故事",
  "description": "美邁車用電子（MEIMAI MM）為日本 Clarion 歌樂車用影音台灣官方授權總經銷，讓你在台灣享有與國際同步的品質，與更貼近在地的服務。"
 },
 {
  "page_key": "fitting",
  "type_key": "default",
  "kicker": "車用配件 ｜ MM 美邁",
  "title": "車用配件",
  "description": "MM 自有車用配件，補齊安裝與日常使用的需求，台灣完整售後支援。行車記錄器與鏡頭已各自獨立分類。"
 },
 {
  "page_key": "safety",
  "type_key": "default",
  "kicker": "影像 · 安全 ｜ MM 美邁",
  "title": "影像 · 安全",
  "description": "盲點偵測等即時影像與行車安全輔助裝置，變換車道、倒車更安心。行車記錄器與鏡頭已各自獨立分類。"
 },
 {
  "page_key": "headUnit",
  "type_key": "default",
  "kicker": "Clarion 歌樂 ｜ 1DIN / 2DIN",
  "title": "車用主機（1DIN / 2DIN）",
  "description": "標準規格單框／雙框主機，收音機、藍牙、多媒體播放，通用車款升級的實用選擇。"
 },
 {
  "page_key": "audioAccessories",
  "type_key": "default",
  "kicker": "Clarion 歌樂 ｜ SOUND 汽車音響",
  "title": "汽車音響系統",
  "description": "喇叭、高音、重低音到擴大機／DSP，打造車內劇院級聲音。Clarion 原廠聲學，讓每一段路都是好聲音。"
 },
 {
  "page_key": "clarionOverview",
  "type_key": "default",
  "kicker": "Clarion 歌樂 ｜ 日本車用影音",
  "title": "Clarion 歌樂 全品項",
  "description": "Clarion 歌樂擁有超過 80 年車用影音經驗，MM 美邁為台灣總經銷。以下依產品分類完整呈現，實際供應以經銷據點為準。"
 },
 {
  "page_key": "mmOverview",
  "type_key": "default",
  "kicker": "MM 美邁 ｜ 自有品牌",
  "title": "MM 美邁 自有品牌",
  "description": "美邁車用電子多年累積的自有產品線 — 從車型專用機、多媒體安卓機，到影像與行車安全設備。與 Clarion 歌樂並肩，統一分類、統一名稱，售後由美邁在台灣完整支援。"
 },
 {
  "page_key": "productLanding",
  "type_key": "landingHeadunit",
  "kicker": "CLARION × MM ｜ 安卓車機・車用多媒體主機",
  "title": "安卓車機・車用多媒體主機",
  "description": "安卓車機（車用多媒體主機）把導航、影音、手機互聯整合到一塊大螢幕上。這裡把 Clarion 歌樂與 MM 美邁的機種放在一起比較，先看怎麼挑，再依車型選擇適合的主機。"
 },
 {
  "page_key": "productLanding",
  "type_key": "landingAudio",
  "kicker": "CLARION ｜ 汽車音響",
  "title": "汽車音響",
  "description": "Clarion 的核心始終在聲音。從 1940 年的車用收音機起家，喇叭、擴大機、DSP 數位音場處理與重低音系統是長年累積的核心技術。"
 },
 {
  "page_key": "productLanding",
  "type_key": "landingDashcam",
  "kicker": "CLARION × MM ｜ 行車記錄器",
  "title": "行車記錄器",
  "description": "行車記錄器負責在事故發生時留下關鍵畫面。這裡把 Clarion 歌樂與 MM 美邁的機種放在一起，說明前後錄、隱藏式安裝與畫質該怎麼取捨。"
 }
]
JSON
, true);

        foreach ($rows as $row) {
            $where = ['page_key' => $row['page_key'], 'type_key' => $row['type_key']];
            $exist = DB::table('list_banners')->where($where)->first();
            if (!$exist) {
                DB::table('list_banners')->insert($where + [
                    'kicker' => $row['kicker'],
                    'title' => $row['title'],
                    'description' => $row['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                continue;
            }
            $fill = [];
            foreach (['kicker', 'title', 'description'] as $col) {
                if ($exist->{$col} === null || $exist->{$col} === '') {
                    $fill[$col] = $row[$col];
                }
            }
            if ($fill) {
                $fill['updated_at'] = now();
                DB::table('list_banners')->where($where)->update($fill);
            }
        }
    }

    public function down()
    {
        // 不還原：文字是後台資料，回滾時不刪除。
    }
}
