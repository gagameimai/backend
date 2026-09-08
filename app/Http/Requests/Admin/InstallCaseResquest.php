<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;

class InstallCaseResquest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'category' => 'required|integer|in:0,1,2,3,4,5,6,7,8',
            'name' => 'required',
            'img' => 'required',
            'sort' => 'nullable|integer',
            'status' => 'required|integer',
            'installed_at' => 'nullable|date',
            'is_pinned' => 'nullable|integer|in:0,1',
            'is_home' => 'nullable|integer|in:0,1',
            'home_sort' => 'nullable|integer',
            'car_brand_id' => 'required|integer|exists:car_brand,id',
            'car_id' => 'required|integer|exists:car,id',
            'product' => 'nullable|string|max:255',
            'dealer' => 'nullable|string|max:255',
            'need' => 'nullable|string',
            'work' => 'nullable|string',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'category' => '分類',
            'name' => '名稱',
            'img' => '案例圖片',
            'sort' => '排序',
            'status' => '狀態',
            'installed_at' => '安裝日期',
            'is_pinned' => '置頂',
            'is_home' => '顯示在首頁',
            'home_sort' => '首頁排序',
            'car_brand_id' => '汽車品牌',
            'car_id' => '汽車車款',
            'product' => '安裝產品',
            'dealer' => '施工據點',
            'need' => '客戶需求',
            'work' => '施工內容',
        ];
    }
}
