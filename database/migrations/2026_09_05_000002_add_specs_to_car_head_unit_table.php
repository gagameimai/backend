<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSpecsToCarHeadUnitTable extends Migration
{
    /**
     * Run the migrations.
     * 車用主機 1/2DIN 補上尺寸／硬碟／記憶體／解析度／建議售價，比照 car_media 但皆非必填。
     *
     * @return void
     */
    public function up()
    {
        Schema::table('car_head_unit', function (Blueprint $table) {
            $table->string('size')->nullable()->after('img');
            $table->string('hard_drive')->nullable()->after('size');
            $table->string('ram')->nullable()->after('hard_drive');
            $table->string('resolution')->nullable()->after('ram');
            $table->string('price')->nullable()->after('resolution');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('car_head_unit', function (Blueprint $table) {
            $table->dropColumn(['size', 'hard_drive', 'ram', 'resolution', 'price']);
        });
    }
}
