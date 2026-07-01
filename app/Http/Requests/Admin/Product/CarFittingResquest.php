<?php

namespace App\Http\Requests\Admin\Product;

use App\Http\Requests\BaseRequest;

class CarFittingResquest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'img' => 'required',
            'material' => 'required',
            'power' => 'required',
            'content' => 'required',
            'is_top' => 'required|integer',
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
            'name' => '名稱',
            'img' => '列表圖片',
            'material' => '材質',
            'power' => '電源',
            'content' => '產品規格',
            'is_top' => '置頂',
            'status' => '狀態',
        ];
    }
}
