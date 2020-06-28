<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileAvatarRequest extends FormRequest
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
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ];
    }

    public function attributes()
    {
        return [
            'avatar' => 'аватар'
        ];
    }

    public function messages()
    {
        return [
            'avatar.image' => 'Файл должен быть изображением',
            'avatar.mimes' => 'Файл должен быть расширения jpeg,png,jpg,gif или svg',
            'avatar.max' => 'Максимальный размер файла не должен превышать 2048 КБ',
        ];
    }
}
