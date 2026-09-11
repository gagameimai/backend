<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 首頁 Banner 增加「手機版圖片」欄位。
 *
 * 為什麼要分開存一張：
 *   首頁改成整頁式（滿版）之後，Banner 是用 object-fit:cover 撐滿整個畫面高度。
 *   桌機的 16:9 橫圖放到手機（例如 390×844）會被左右各裁掉約 35%，
 *   圖上的字（Clarion／標語）會直接被切掉。
 *   所以手機要另外放一張直式圖，前台用 <picture> + media query 自動切換。
 *
 * img_mobile 允許留空 —— 留空時前台會直接沿用桌機那張（會被裁，但不會壞）。
 */
class AddImgMobileToBannerTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('banner', 'img_mobile')) {
            Schema::table('banner', function (Blueprint $table) {
                $table->string('img_mobile')->nullable()->after('img')->comment('手機版圖片（直式 1080x2160），留空則沿用桌機圖');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('banner', 'img_mobile')) {
            Schema::table('banner', function (Blueprint $table) {
                $table->dropColumn('img_mobile');
            });
        }
    }
}
