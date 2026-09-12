<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 產品列表／總覽頁 Banner 增加「手機版圖片」欄位。
 *
 * 桌機版 Banner 是 4:1 的橫長條（1920×480），手機螢幕寬只有 390px 左右，
 * 同一張圖用 cover 塞進去會被左右各裁掉七成，主體幾乎看不到。
 * 所以手機另放一張 16:9（1080×608），前台在 640px 以下自動改吃這張，
 * 並改成「圖在上、文字在下」的堆疊，圖不加白霧、完整露出。
 *
 * img_mobile 允許留空 —— 留空時前台沿用桌機那張（會被裁，但不會壞）。
 */
class AddImgMobileToListBannersTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('list_banners', 'img_mobile')) {
            Schema::table('list_banners', function (Blueprint $table) {
                $table->string('img_mobile')->nullable()->after('img')->comment('手機版圖片（16:9，1080x608），留空則沿用桌機圖');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('list_banners', 'img_mobile')) {
            Schema::table('list_banners', function (Blueprint $table) {
                $table->dropColumn('img_mobile');
            });
        }
    }
}
