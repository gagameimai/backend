<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstallCasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('install_cases', function (Blueprint $table) {
            $table->id();
            // 案例分類，對應前台「安裝實績」卡片下方的分類文字（0=多媒體安卓機／1=車型專用機／2=汽車音響／3=行車記錄器／4=影像・安全／5=車用配件／6=車用主機1/2DIN／7=頭枕螢幕／8=可攜式）
            $table->tinyInteger('category')->default(0);
            $table->string('name');
            $table->string('img');
            $table->integer('sort')->default(0);
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('install_cases');
    }
}
