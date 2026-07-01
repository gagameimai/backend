<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDealerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dealer', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('tel')->nullable();
            $table->integer('county');
            $table->string('address');
            $table->integer('sort')->default(0);
            $table->integer('status')->default(1);
            $table->timestamps();

            $table->index('county');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dealer');
    }
}
