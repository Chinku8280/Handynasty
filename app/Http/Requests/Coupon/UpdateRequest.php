<?php

namespace App\Http\Requests\Coupon;

use App\Http\Requests\CoreRequest;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends CoreRequest
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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'coupon_title' => 'required',
            'coupon_code' => 'required|regex:/(^[A-Za-z0-9]+$)+/|unique:coupons,coupon_code,'.$this->route('coupon'),
            'start_time' => 'required',
            'end_time' => 'required',
            'points' => 'nullable|integer',
            'minimum_purchase_amount' => 'nullable',
            'min_age' => 'required|integer',
            'max_age' => 'required|integer',
            'outlet_id' => 'required',
            'services' => 'required',
            'gender' => 'required',
            'short_description' => 'nullable',
        ];

        if($this->get('is_customer_specific') == 1)
        {
            $rules['customer_id'] = 'required';
        }

        if($this->get('amount') == null && $this->get('percent') == null ){
            $rules['amount'] = 'required';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'title.required' => __('app.coupon').' '.__('app.code').' '.__('errors.fieldRequired'),
        ];
    }
}
