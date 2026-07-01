<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;

class NewsResquest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required',
            'content' => 'required',
            'status' => 'required|integer'
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
            'title' => '標題',
            'content' => '內容',
            'status' => '狀態'
        ];
    }
}
