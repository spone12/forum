<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class NotationPhotoRequest
 *
 * @package App\Http\Requests
 */
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

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'images.*' => 'изображение'
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'images.*.image' => 'Файл должен быть изображением',
            'images.*.mimes' => 'Файл должен быть расширения jpeg,png,jpg,gif или svg',
            'images.*.max'   => 'Максимальный размер фотографии не должен превышать 2048 КБ',
        ];
    }
}
