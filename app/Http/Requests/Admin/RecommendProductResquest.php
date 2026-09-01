<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;

class RecommendProductResquest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $types = array_keys(config('recommend_product.types', []));

        return [
            'product_type' => 'required|string|in:' . implode(',', $types),
            'product_id' => 'required|integer',
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
            'product_type' => '商品分類',
            'product_id' => '商品',
            'sort' => '排序',
            'status' => '狀態',
        ];
    }

    /**
     * 額外驗證：確認選擇的 product_id 在該分類底下真的存在。
     *
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $type = $this->input('product_type');
            $id = $this->input('product_id');

            if (!$type || !$id) {
                return;
            }

            $conf = config("recommend_product.types.{$type}");
            if (!$conf || !isset($conf['model'])) {
                return;
            }

            $exists = $conf['model']::where('id', $id)->exists();
            if (!$exists) {
                $validator->errors()->add('product_id', '選擇的商品不存在，請重新選擇');
            }
        });
    }
}
