<?php

namespace App\Http\Requests\Admin\Product;

use App\Http\Requests\BaseRequest;
use Route;

class CarFrameResquest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $currentAction = Route::currentRouteAction();

        return [
            'car_brand_id' => 'required|integer',
            'car_id' => 'required|integer',
            'year_start' => 'required|integer',
            'year_end' => 'required|integer',
            'size' => 'required',
            'name' => 'nullable',
            // 'img0' => substr($currentAction, strpos($currentAction, '@') + 1) == 'create' ? 'required' : 'nullable',
            'content' => 'nullable',
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
            'car_id' => '汽車車款',
            'year_start' => '開始年份',
            'year_end' => '結束年份',
            'size' => '尺寸',
            'name' => '名稱',
            // 'img0' => '列表圖片',
            'content' => '內容敘述',
            'status' => '狀態',
        ];
    }
}
