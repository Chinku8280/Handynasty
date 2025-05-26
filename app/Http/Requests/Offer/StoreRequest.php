<?php

namespace App\Http\Requests\Offer;

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
    public function rules()
    {
        $rules = [
            'title'                 => 'required|unique:offers,title',
            'discount'              => 'required',          
          
    
        ];
        return $rules;
    }

    public function messages()
    {
        return [
            'title.required' => __('app.title').' '.__('errors.fieldRequired'),
        
        ];
    }
}
