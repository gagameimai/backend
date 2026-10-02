<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;

class HomeSectionResquest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'content' => 'nullable|string',
            'img' => 'nullable|string',
            'img_mobile' => 'nullable|string',
            'bg_color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
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
            'content' => '區塊內容',
            'img' => '背景圖（電腦版）',
            'img_mobile' => '背景圖（手機版）',
            'bg_color' => '區塊底色',
        ];
    }
}
