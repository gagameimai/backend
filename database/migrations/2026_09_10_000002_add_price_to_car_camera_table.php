<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceToCarCameraTable extends Migration
{
    /**
     * Run the migrations.
     * 鏡頭補上建議售價欄位，非必填。
     *
     * @return void
     */
    public function up()
    {
        Schema::table('car_camera', function (Blueprint $table) {
            $table->string('price')->nullable()->after('img');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('car_camera', function (Blueprint $table) {
            $table->dropColumn(['price']);
        });
    }
}
