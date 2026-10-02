<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 首頁滿版區塊增加「底色」：圖片底下／沒有圖片時的背景色（#RRGGBB）。
 * 之後想換首頁風格底色，在後台選顏色就好，不用改程式；留空＝維持原本的深色底 #0D1016。
 */
class AddBgColorToHomeSectionsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('home_sections', 'bg_color')) {
            Schema::table('home_sections', function (Blueprint $table) {
                $table->string('bg_color', 7)->nullable()->after('img_mobile')->comment('區塊底色 #RRGGBB，留空用預設深色底');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('home_sections', 'bg_color')) {
            Schema::table('home_sections', function (Blueprint $table) {
                $table->dropColumn('bg_color');
            });
        }
    }
}
