<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ImageRequest extends FormRequest
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
        $rules['type'] = 'required|string|in:avatar,topic';

        // 不同图片类型，不一样的尺寸要求
        if ($this->type == 'avatar') {
            $rules['image'] = 'required|mimes:jpeg,png,gif,bpm|dimensions:min_width=200,min_height=200';
        } else {
            $rules['image'] = 'required|mimes:jpeg,png,gif,bpm';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'image.dimensions' => '图片清晰度不够，要200 * 200以上规格!',
        ];
    }
}
