<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 產品列表／總覽頁 Banner 左邊文字（小標／大標題／說明）各自可選文字顏色。
 * 值是 #RRGGBB；留空＝用頁面原本的顏色（深色字，適合淺色底圖）。
 */
class AddTextColorsToListBannersTable extends Migration
{
    public function up()
    {
        Schema::table('list_banners', function (Blueprint $table) {
            foreach (['kicker_color' => 'description', 'title_color' => 'kicker_color', 'desc_color' => 'title_color'] as $col => $after) {
                if (!Schema::hasColumn('list_banners', $col)) {
                    $table->string($col, 7)->nullable()->after($after)->comment('文字顏色 #RRGGBB，留空用預設');
                }
            }
        });
    }

    public function down()
    {
        Schema::table('list_banners', function (Blueprint $table) {
            foreach (['desc_color', 'title_color', 'kicker_color'] as $col) {
                if (Schema::hasColumn('list_banners', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
}
