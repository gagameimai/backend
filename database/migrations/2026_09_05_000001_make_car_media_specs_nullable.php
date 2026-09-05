<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MakeCarMediaSpecsNullable extends Migration
{
    /**
     * Run the migrations.
     * 尺寸／硬碟／記憶體／解析度／建議售價 改為可空白（後台新增/編輯不再強制必填）。
     * 用原生 SQL 改欄位屬性，避免專案未安裝 doctrine/dbal 造成 Schema::table()->change() 失敗。
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `car_media` MODIFY `size` VARCHAR(255) NULL');
        DB::statement('ALTER TABLE `car_media` MODIFY `hard_drive` VARCHAR(255) NULL');
        DB::statement('ALTER TABLE `car_media` MODIFY `ram` VARCHAR(255) NULL');
        DB::statement('ALTER TABLE `car_media` MODIFY `resolution` VARCHAR(255) NULL');
        DB::statement('ALTER TABLE `car_media` MODIFY `price` VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `car_media` MODIFY `size` VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE `car_media` MODIFY `hard_drive` VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE `car_media` MODIFY `ram` VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE `car_media` MODIFY `resolution` VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE `car_media` MODIFY `price` VARCHAR(255) NOT NULL');
    }
}
