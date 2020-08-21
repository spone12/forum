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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    //translate concrect pole
    public function attributes()
    {
        return [
            'image' => 'изображение'
        ];
    }

    public function messages()
    {
        return [
            'image.image' => 'Файл должен быть изображением',
            'image.mimes' => 'Файл должен быть расширения jpeg,png,jpg,gif или svg',
            'image.max'     => 'Максимальный размер фотографии не должен превышать 2048 КБ',
        ];
    }
}
