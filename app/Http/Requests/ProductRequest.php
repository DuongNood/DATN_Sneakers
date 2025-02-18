<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            'product_code'=>'required|max:10|unique:products,product_code,' . $this->route('id'),
            'product_name'=>'required|max:255',
            'image'=>'image|mimes:png,jpg,jpeg',
            'category_id'=>'required|exists:"categories",id',
        ];
    }
     /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
     public function messages(): array
    {
        return [
        'product_code.required' => 'Product code is required.',
        'product_code.max' => 'Product code must not exceed 10 characters.',
        'product_code.unique' => 'Product code already exists. Please choose another one.',       
        'product_name.required' => 'Product name is required.',
        'product_name.max' => 'Product name must not exceed 255 characters.',        
        'image.image' => 'The file must be an image.',
        'image.mimes' => 'Image must be of type: png, jpg, or jpeg.',  
        'category_id.required' => 'Please select a category for the product.',
        'category_id.exists' => 'The selected category does not exist.',
        ];
    }
}
