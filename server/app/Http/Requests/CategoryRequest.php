<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        if (request()->isMethod('put')) {
            $id = request()->input('id');
            return [
                'name' => [
                    'required',
                    //Rule::unique('categories', 'name')->ignore(request()->input('id')),
                    Rule::unique('categories')->where(function ($query) use ($id) {
                        return $query->where('id', $id)
                            ->where('deleted_at', null);
                    }),
                    'max:100'
                ]
            ];
        }
        return [
            'name' => 'required|unique:categories,name,deleted_at|max:100'
        ];
    }
}
