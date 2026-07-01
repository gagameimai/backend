<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car', function (Blueprint $table) {
            $table->id();
            $table->integer('car_brand_id');
            $table->string('name');
            $table->integer('year_start');
            $table->integer('year_end');
            $table->integer('sort')->default(0);
            $table->integer('status')->default(1);
            $table->timestamps();

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
        Schema::dropIfExists('car');
    }
}
