<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarFrameTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_frame', function (Blueprint $table) {
            $table->id();
            $table->integer('car_brand_id');
            $table->integer('car_id');
            $table->string('name');
            $table->integer('year_start');
            $table->integer('year_end');
            $table->string('size');
            $table->json('img')->nullable();
            $table->json('img1')->nullable();
            $table->json('img2')->nullable();
            $table->json('img3')->nullable();
            $table->text('content')->nullable();
            $table->integer('sort')->default(0);
            $table->integer('status')->default(1);
            $table->timestamps();

            $table->index('car_brand_id');
            $table->index('car_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('car_frame');
    }
}
