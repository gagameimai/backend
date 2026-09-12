<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 首頁「滿版情境區塊」管理（首頁 index.vue 裡的 3 段滿版背景區：
 * clarion 滿版區 / MM 美邁滿版區 / 尾端 CTA 滿版區，對應前台 section_key：zone1／zone2／zone3）。
 *
 * 這 3 個區塊固定存在（見 config/home_section.php 白名單），後台不能新增/刪除，
 * 只能編輯既有 3 筆的內容與背景圖，寫法比照 list_banners 那套 config-driven + updateOrCreate 模式。
 *
 * content 開放 CKEditor 讓管理者自己排版（標題／說明／按鈕等全部自己用 HTML 排），
 * 前台改用 v-html 顯示這欄，不再寫死 i18n 文字；沒有內容時前台維持原本預設文字，不會空白。
 */
class CreateHomeSectionsTable extends Migration
{
    public function up()
    {
        Schema::create('home_sections', function (Blueprint $table) {
            $table->id();
            $table->string('section_key')->unique();
            $table->text('content')->nullable();
            $table->string('img')->nullable();
            $table->string('img_mobile')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('home_sections');
    }
}
