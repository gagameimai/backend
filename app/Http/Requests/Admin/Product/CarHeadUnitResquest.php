<?php

namespace App\Http\Requests\Admin\Product;

use App\Http\Requests\BaseRequest;

class CarHeadUnitResquest extends BaseRequest
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
            'size' => 'nullable',
            'hard_drive' => 'nullable',
            'ram' => 'nullable',
            'resolution' => 'nullable',
            'price' => 'nullable',
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
            'size' => '尺寸',
            'hard_drive' => '硬碟',
            'ram' => '記憶體',
            'resolution' => '解析度',
            'price' => '建議售價',
            'content' => '產品規格',
            'is_top' => '置頂',
            'status' => '狀態',
        ];
    }
}
