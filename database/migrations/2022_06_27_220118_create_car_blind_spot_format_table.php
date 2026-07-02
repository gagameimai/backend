<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarBlindSpotFormatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_blind_spot_format', function (Blueprint $table) {
            $table->id();
            $table->integer('car_blind_spot_id');
            $table->integer('car_brand_id');
            $table->string('style');
            $table->string('year');
            $table->string('spc');
            $table->integer('sort')->default(0);
            $table->integer('status')->default(1);
            $table->timestamps();

            $table->index('car_blind_spot_id');
            $table->index('car_brand_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('car_blind_spot_format');
    }
}
