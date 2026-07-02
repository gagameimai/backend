<?php

namespace App\Http\Requests\Admin\Product;

use App\Http\Requests\BaseRequest;

    class CarMediaResquest extends BaseRequest
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
            'memo' => 'required',
            'size' => 'required',
            'hard_drive' => 'required',
            'ram' => 'required',
            'resolution' => 'required',
            'price' => 'required',
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
            'img' => '圖片',
            'memo' => '列表簡述',
            'size' => '尺寸',
            'hard_drive' => '硬碟',
            'ram' => '記憶體',
            'resolution' => '解析度',
            'price' => '建議售價',
            'content' => '內文敘述',
            'is_top' => '置頂',
            'status' => '狀態',
        ];
    }
}
