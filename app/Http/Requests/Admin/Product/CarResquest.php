<?php

namespace App\Http\Requests\Admin\Product;

use App\Http\Requests\BaseRequest;

class CarResquest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'car_brand_id' => 'required|integer',
            'name' => 'required',
            'year_start' => 'required|integer',
            'year_end' => 'required|integer',
            'status' => 'required|integer',
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
            'car_brand_id' => '汽車品牌',
            'name' => '名稱',
            'year_start' => '開始年份',
            'year_end' => '結束年份',
            'status' => '狀態',
        ];
    }
}
