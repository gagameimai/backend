<?php

namespace App\Http\Requests\Admin\Product;

use App\Http\Requests\BaseRequest;

class CarBlindSpotFormatResquest extends BaseRequest
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
            'style' => 'required',
            'year' => 'required',
            'spc' => 'required',
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
            'style' => '汽車車款',
            'year' => '年份',
            'spc' => '規格',
            'status' => '狀態',
        ];
    }
}
