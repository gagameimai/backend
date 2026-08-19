<?php

namespace App\Http\Requests\Admin\Product;

use App\Http\Requests\BaseRequest;

class CarAudioAccessoriesResquest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => 'required|integer',
            'name' => 'required',
            'img' => 'required',
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
            'type' => '類型',
            'name' => '名稱',
            'img' => '列表圖片',
            'content' => '產品規格',
            'is_top' => '置頂',
            'status' => '狀態',
        ];
    }
}
