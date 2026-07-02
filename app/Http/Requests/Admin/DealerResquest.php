<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;

class DealerResquest extends BaseRequest
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
            'county' => 'required',
            'address' => 'required',
            'tel' => 'required',
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
            'county' => '縣市',
            'address' => '地址',
            'tel' => '聯絡電話',
            'status' => '狀態',
        ];
    }
}
