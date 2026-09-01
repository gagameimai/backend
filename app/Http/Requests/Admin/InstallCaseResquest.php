<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;

class InstallCaseResquest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'category' => 'required|integer|in:0,1,2,3,4,5,6,7,8',
            'name' => 'required',
            'img' => 'required',
            'sort' => 'nullable|integer',
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
            'category' => '分類',
            'name' => '名稱',
            'img' => '案例圖片',
            'sort' => '排序',
            'status' => '狀態',
        ];
    }
}
