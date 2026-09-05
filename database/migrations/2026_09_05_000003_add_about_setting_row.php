<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddAboutSettingRow extends Migration
{
    /**
     * Run the migrations.
     * 「關於我們」頁面比照「常見問題」(type=qa) 的做法，不另外建表，
     * 直接在既有的 setting 表補一筆 type=about，content 放後台 CKEditor 可編輯的一大塊 HTML。
     * 先塞入依草稿整理的預設文字，上線前後台可隨時再編輯。
     *
     * @return void
     */
    public function up()
    {
        $exists = DB::table('setting')->where('type', 'about')->exists();

        if (!$exists) {
            DB::table('setting')->insert([
                'type' => 'about',
                'content' => $this->defaultContent(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('setting')->where('type', 'about')->delete();
    }

    /**
     * 預設內文（依「關於我們」草稿整理，後台可隨時修改）
     *
     * @return string
     */
    private function defaultContent()
    {
        return <<<'HTML'
<h2>CLARION ／ 關於歌樂</h2>
<p>Clarion（歌樂）為日本車用影音與多媒體品牌，憑藉日本一貫的技術底蘊與品質要求，在全球車用音響、影音主機市場建立深厚口碑。</p>
<p>產品線涵蓋車型專用機、多媒體安卓機、汽車音響、行車記錄器與環景輔助系統，致力為駕駛人打造更安全、便利且具娛樂性的車內體驗。</p>
<h2>MEIMAI ／ 關於美邁</h2>
<p>美邁車用電子有限公司（MEIMAI MM）成立於 2020 年，深耕台灣車用電子市場，產品涵蓋安卓車載多媒體機、行車記錄器、倒車鏡頭、AVM 360 環景輔助、盲點偵測，以及各車型專用安卓車框。</p>
<p>我們以「卓越售後服務」為核心，從品質把關到保固認證，並持續拓展全台服務據點，讓專業安裝與維修更便利可靠。</p>
<h2>為什麼選擇 Clarion × MM</h2>
<ul>
<li><strong>原廠正貨</strong>：Clarion 台灣總經銷，引進原廠正品，品質有保障。</li>
<li><strong>在地安裝與保固</strong>：全台合作經銷據點，購買、安裝、保固一站到位。</li>
<li><strong>卓越售後</strong>：從品質把關到售後維修，讓你用得安心。</li>
</ul>
HTML;
    }
}
