<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToCarMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('car_media', function (Blueprint $table) {
            $table->tinyInteger('type')->default(0)->after('id')
                ->comment('類型：0=MM，1=MM 專用機，2=Clarion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('car_media', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
