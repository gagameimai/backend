<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 產品列表／總覽頁 Banner 增加「左邊文字」三個選填欄位（小標、大標題、說明）。
 *
 * 文字是前台的真實 HTML 文字，位置由頁面排版自動決定，後台不用算距離、也不用把字做進圖片。
 * 三格都允許留空 —— 留空的那一格前台沿用頁面原本（多國語系檔）的文字。
 */
class AddTextToListBannersTable extends Migration
{
    public function up()
    {
        Schema::table('list_banners', function (Blueprint $table) {
            if (!Schema::hasColumn('list_banners', 'kicker')) {
                $table->string('kicker')->nullable()->after('img_mobile')->comment('小標題（選填）');
            }
            if (!Schema::hasColumn('list_banners', 'title')) {
                $table->string('title')->nullable()->after('kicker')->comment('大標題（選填）');
            }
            if (!Schema::hasColumn('list_banners', 'description')) {
                $table->text('description')->nullable()->after('title')->comment('說明文字（選填）');
            }
        });
    }

    public function down()
    {
        Schema::table('list_banners', function (Blueprint $table) {
            foreach (['description', 'title', 'kicker'] as $col) {
                if (Schema::hasColumn('list_banners', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
}
