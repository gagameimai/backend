<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;

class SeoController extends Controller
{
    /**
     * 畫面（資料的讀取／儲存共用「網站基本設定」的 /admin/website/all 與 PATCH /admin/website，
     * 存在設定內容的 seo 欄位）
     */
    public function index()
    {
        return view('admin.setting.seo', [
            'pages' => config('seo_pages'),
        ]);
    }
}
