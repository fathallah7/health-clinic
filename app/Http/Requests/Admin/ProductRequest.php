<?php

namespace App\Http\Requests\Admin;

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
            'name' => 'required',
            'description' => 'max:255',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'mimes:jpg,jpeg,png|max:2048',
            'category_id' => 'required|exists:categories,id',
        ];
    }
}
