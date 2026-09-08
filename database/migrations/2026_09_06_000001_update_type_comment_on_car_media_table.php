<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * car_media.type 新增 3 = Clarion 車型專用機（OEM）
 * 僅更新欄位註解，不動既有資料。
 */
class UpdateTypeCommentOnCarMediaTable extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE `car_media` MODIFY `type` TINYINT NOT NULL DEFAULT 0 COMMENT '類型：0=MM，1=MM 專用機，2=Clarion GL，3=Clarion 車型專用機'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE `car_media` MODIFY `type` TINYINT NOT NULL DEFAULT 0 COMMENT '類型：0=MM，1=MM 專用機，2=Clarion'");
    }
}
