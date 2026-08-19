<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarHeadUnitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_head_unit', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('type')->default(0)->comment('類型：0=1DIN，1=2DIN');
            $table->string('name');
            $table->string('img');
            $table->text('memo_in')->nullable();
            $table->text('content')->nullable();
            $table->integer('is_top')->default(0);
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
        Schema::dropIfExists('car_head_unit');
    }
}
