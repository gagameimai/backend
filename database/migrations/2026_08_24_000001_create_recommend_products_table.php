<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecommendProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recommend_products', function (Blueprint $table) {
            $table->id();
            // 商品分類 key，對應 config/recommend_product.php 的 types（例：car_frame、car_media...）
            $table->string('product_type', 40)->comment('商品分類 key，對應 config/recommend_product.php');
            // 對應分類表（如 car_frame、car_media...）的商品 id
            $table->unsignedBigInteger('product_id')->comment('對應分類表的商品 id');
            $table->integer('sort')->default(0);
            $table->integer('status')->default(1);
            $table->timestamps();

            $table->index(['product_type', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recommend_products');
    }
}
