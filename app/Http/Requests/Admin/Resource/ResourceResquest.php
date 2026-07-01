<?php

namespace App\Http\Requests\Admin\Resource;

use App\Http\Requests\BaseRequest;

class ResourceResquest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'resource_category_id' => 'required|integer',
            'name' => 'required',
            'url' => 'required|url',
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
            'resource_category_id' => '分類',
            'name' => '名稱',
            'url' => '外部連結',
            'status' => '狀態',
        ];
    }
}
