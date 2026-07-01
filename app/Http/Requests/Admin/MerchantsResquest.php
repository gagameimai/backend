<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;

class MerchantsResquest extends BaseRequest
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
                'agent_id' => $this->input('agent_id') == 0 ? 'required|integer' : 'required|integer|exists:agents,id',
                'account' => 'required|unique:merchants,account',
                'password' => 'required',
                'name' => 'required',
                'status' => 'required|integer'
            ];
        } else {
            $rules = [
                'agent_id' => $this->input('agent_id') == 0 ? 'required|integer' : 'required|integer|exists:agents,id',
                'password' => 'nullable',
                'name' => 'required',
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
            'agent_id' => '代理商',
            'account' => '帳號',
            'password' => '密碼',
            'name' => '名稱',
            'status' => '狀態'
        ];
    }
}
