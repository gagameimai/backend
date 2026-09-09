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
            'img' => 'Banner 圖片',
        ];
    }
}
