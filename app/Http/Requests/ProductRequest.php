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
        return true;
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
            'product_code.required' => 'Mã sản phẩm là bắt buộc.',
            'product_code.max' => 'Mã sản phẩm không được vượt quá 10 ký tự.',
            'product_code.unique' => 'Mã sản phẩm đã tồn tại. Vui lòng chọn mã khác.',       
            'product_name.required' => 'Tên sản phẩm là bắt buộc.',
            'product_name.max' => 'Tên sản phẩm không được vượt quá 255 ký tự.',        
            'image.image' => 'Tệp phải là một hình ảnh.',
            'image.mimes' => 'Hình ảnh phải có định dạng: png, jpg hoặc jpeg.',  
            'category_id.required' => 'Vui lòng chọn danh mục cho sản phẩm.',
            'category_id.exists' => 'Danh mục được chọn không tồn tại.',
        ];
    }
}
