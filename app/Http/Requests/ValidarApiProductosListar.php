<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarApiProductosListar extends FormRequest
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
        return [
            'orderBy'       => 'required|integer',
            'dir'           => 'required|min:1|max:2',
            'fromPrice'     => 'required|between:0,99.99',
            'toPrice'       => 'required|between:0,99.99',
            'name'          => 'required|min:1|max:100',
            'limit'         => 'required|min:1|max:2',
            'offset'         => 'required',
            'category'      => 'required|integer',
        ];
    }

    public function messages()
    {
      return [
            'orderBy.required'     => 'El :attribute es obligatorio.',
            'orderBy.integer'      => 'El :attribute debe ser entero.'   
      ];
    }
    public function attributes()
    {
      return [
             'orderBy' => 'orderBy',  
      ];
    }
}
