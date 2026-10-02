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
            'county' => ['required', function ($attr, $value, $fail) {
                $list = config('county', []);
                $name = is_string($value) ? str_replace('臺', '台', trim($value)) : $value;
                $okIdx = (is_int($value) || ctype_digit((string) $value)) && isset($list[(int) $value]);
                if (!$okIdx && !in_array($name, $list, true)) {
                    $fail('縣市請給代碼 0～' . (count($list) - 1) . ' 或名稱（例：桃園市）；可用：' . implode('、', $list));
                }
            }],
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
