<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;

class ListBannerResquest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'img' => 'nullable|string',
            'img_mobile' => 'nullable|string',
            'kicker' => 'nullable|string|max:100',
            'title' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
            'kicker_color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'title_color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'desc_color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
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
            'img' => 'Banner 圖片（電腦版）',
            'img_mobile' => 'Banner 圖片（手機版）',
            'kicker' => '小標題',
            'title' => '大標題',
            'description' => '說明文字',
            'kicker_color' => '小標題顏色',
            'title_color' => '大標題顏色',
            'desc_color' => '說明文字顏色',
        ];
    }
}
