<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;

class CategorySwitchController extends Controller
{
    /**
     * 畫面（資料的讀取／儲存共用「網站基本設定」的 /admin/website/all 與 PATCH /admin/website，
     * 存在設定內容的 categories 欄位）
     */
    public function index()
    {
        return view('admin.product.category_switch', [
            'defaults' => config('product_categories'),
        ]);
    }
}
