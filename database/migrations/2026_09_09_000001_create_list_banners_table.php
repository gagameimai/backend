<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('list_banners', function (Blueprint $table) {
            $table->id();
            // 頁面代碼，對應前台「產品列表／總覽」頁面（例：multimedia、carFrame、clarionOverview），詳見 config/list_banner.php
            $table->string('page_key');
            // 分類代碼：同一頁面被多個 type／品牌共用時用來區分（例：multimedia 用 0~3，camera／dashcam 用 mm／clarion）；
            // 頁面本身只有單一 banner 時固定用 'default'
            $table->string('type_key')->default('default');
            // Banner 背景圖網址（後台用檔案管理員上傳後寫入的網址字串）；沒有值時前台改用預設漸層背景
            $table->string('img')->nullable();
            $table->timestamps();

            $table->unique(['page_key', 'type_key']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('list_banners');
    }
}
