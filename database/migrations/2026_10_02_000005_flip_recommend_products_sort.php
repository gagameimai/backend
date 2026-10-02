<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * 首頁精選商品的排序規則統一成「數字小的在前」（和其他所有後台頁一致）。
 * 原本是「數字大的在前」，這裡把既有數字反轉，前台顯示的順序維持不變。
 * 只執行一次；down() 再反轉一次即可還原。
 */
class FlipRecommendProductsSort extends Migration
{
    public function up()
    {
        $this->flip();
    }

    public function down()
    {
        $this->flip();
    }

    private function flip()
    {
        $max = (int) DB::table('recommend_products')->max('sort');
        $min = (int) DB::table('recommend_products')->min('sort');
        DB::table('recommend_products')->update(['sort' => DB::raw(($max + $min) . ' - sort')]);
    }
}
