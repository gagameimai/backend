<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;

class UsersResquest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $method = $this->getControllerMethod();
        if ($method == 'create') {
            $rules = [
                'merchant_id' => 'required|integer|exists:merchants,id',
                'account' => 'required',
                'status' => 'required|integer'
            ];
        } else {
            $rules = [
                'merchant_id' => 'required|integer|exists:merchants,id',
                'status' => 'required|integer'
            ];
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'merchant_id' => '商戶',
            'account' => '帳號',
            'status' => '狀態'
        ];
    }
}
