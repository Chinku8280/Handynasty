<?php

namespace App\Http\Requests\Voucher;

use App\Http\Requests\CoreRequest;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends CoreRequest
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
    // public function rules()
    // {
    //     $rules = [
    //         'title'                 => 'required|unique:vouchers,title',
    //         'applied_between_dates' => 'required',
    //         'open_time'             => 'required',
    //         'close_time'            => 'required',   
    //         'discount'              => 'required',    
    //         'max_discount' => 'required',             
    //         'customer_uses_time'    => 'required',
    //         'loyalty_point'         => 'nullable',
    //         'uses_time'             => 'nullable',
    //         'min_age' => 'required|integer',
    //         'max_age' => 'required|integer',
    //         'outlet_id' => 'required',
    //         'services' => 'required',
    //         'gender' => 'required',
    //     ];

    //     if($this->get('is_customer_specific') == 1)
    //     {
    //         $rules['customer_id'] = 'required';
    //     }

    //     return $rules;
    // }

    public function rules()
    {
        $rules = [
            'title'                 => 'required|unique:voucher_discount_packages,title',
            'slug'                  => 'required|unique:voucher_discount_packages,slug',
            'applied_between_dates' => 'nullable',
            // 'open_time'             => 'required',
            // 'close_time'            => 'required',     
            'max_discount'          => 'nullable',             
            'loyalty_point'         => 'nullable',
            'uses_time'             => 'nullable',
            'min_age' => 'required|integer',
            'max_age' => 'required|integer',
            'outlet_id' => 'required',
            'services' => 'required',
            'gender' => 'required',
            'discount_type' => 'required',
            'amount.*' => 'required|numeric|min:0',
            'qty.*' => 'required|numeric|min:0',
            'total_amount.*' => 'required|numeric|min:0',
            'minimum_purchase_amount' => 'nullable',
            'status' => 'required',
            'is_customer_specific' => 'nullable',
            'validity' => 'required|integer|min:0',
            'validity_type' => 'required',
        ];

        if($this->get('is_customer_specific') == 1)
        {
            $rules['customer_id'] = 'required';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'title.required' => __('app.title').' '.__('errors.fieldRequired'),
            'original_amount.required' => __('messages.the').' '.__('app.originalPrice').' '.__('messages.field_is_required'),
            'discount_amount.required' => __('messages.the').' '.__('app.voucherPrice').' '.__('messages.field_is_required'),
            'discount_amount.required' => __('messages.the').' '.__('app.voucherPrice').' '.__('messages.field_is_required'),
        ];
    }
}
