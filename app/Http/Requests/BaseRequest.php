<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

use Route;

class BaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation($validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => validator_message_change($validator->messages())
        ], 422));
    }

    /**
     * 取得路徑下的method
     *
     * @return string
     */
    public function getControllerMethod()
    {
        // 當前route
        $currentAction = Route::currentRouteAction();
        return substr($currentAction, strpos($currentAction, '@') + 1);
    }
}
