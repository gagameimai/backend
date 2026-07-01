<?php

namespace App\Http\Requests\Admin\Product;

use App\Http\Requests\BaseRequest;

class CarBrandResquest extends BaseRequest
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
            'status' => '狀態',
        ];
    }
}
