<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarFittingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_fitting', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('img');
            $table->string('material');
            $table->string('power');
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
        Schema::dropIfExists('car_fitting');
    }
}
