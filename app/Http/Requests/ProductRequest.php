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
            'care_instructions'=>'required',
            'image'=>'image|mimes:png,jpg,jpeg',
            'category_id'=>'required|exists:"categories",id',
            'brand_id'=>'required|exists:"brands",id',
            'gender_id'=>'required|exists:"genders",id',
            'original_price'=>'required|numeric|min:0',
            'discounted_price'=>'required|numeric|min:0|lte:original_price',
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
            'care_instructions.required' => 'Hưỡng dẫn chăm sóc giày là bắt buộc.',       
            'image.image' => 'Tệp phải là một hình ảnh.',
            'image.mimes' => 'Hình ảnh phải có định dạng: png, jpg hoặc jpeg.',  
            'category_id.required' => 'Vui lòng chọn danh mục cho sản phẩm.',
            'category_id.exists' => 'Danh mục được chọn không tồn tại.',
            'brand_id.required' => 'Vui lòng chọn thương hiệu cho sản phẩm.',
            'brand_id.exists' => 'Thương hiệu được chọn không tồn tại.',
            'gender_id.required' => 'Vui lòng chọn giới tính cho sản phẩm.',
            'gender_id.exists' => 'Giới tính được chọn không tồn tại.',
            'original_price.required' => 'Giá gốc là bắt buộc.',
            'original_price.numeric' => 'Giá gốc phải là một số.',
            'original_price.min' => 'Giá gốc phải lớn hơn hoặc bằng 0.',
            'discounted_price.required' => 'Giá khuyến mãi là bắt buộc.',
            'discounted_price.numeric' => 'Giá khuyến mãi phải là một số.',
            'discounted_price.min' => 'Giá khuyến mãi phải lớn hơn hoặc bằng 0.',
            'discounted_price.lte' => 'Giá khuyến mãi không được lớn hơn giá gốc.',
        ];
    }
}
