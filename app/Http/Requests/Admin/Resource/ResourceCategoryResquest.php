<?php

namespace App\Http\Requests\Admin\Resource;

use App\Http\Requests\BaseRequest;

class ResourceCategoryResquest extends BaseRequest
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
            'memo' => 'nullable',
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
            'memo' => '簡述',
            'status' => '狀態',
        ];
    }
}
