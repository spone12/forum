<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NotationPhotoRequest extends FormRequest
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
            'images'   => 'array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    //translate concrect pole
    public function attributes()
    {
        return [
            'images.*' => 'изображение'
        ];
    }

    public function messages()
    {
        return [
            'images.*.image' => 'Файл должен быть изображением',
            'images.*.mimes' => 'Файл должен быть расширения jpeg,png,jpg,gif или svg',
            'images.*.max'     => 'Максимальный размер фотографии не должен превышать 2048 КБ',
        ];
    }
}
