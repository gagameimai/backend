<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;

class BannerResquest extends BaseRequest
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
            'url' => 'nullable|url',
            'img' => 'required',
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
            'url' => '外部連結',
            'img' => '圖片',
            'status' => '狀態',
        ];
    }
}
