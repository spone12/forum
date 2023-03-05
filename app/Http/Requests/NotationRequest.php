<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class NotationRequest
 * @package App\Http\Requests
 */
class NotationRequest extends FormRequest
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
            'name_tema'     => 'required|min:3|max:150',
            'text_notation' => 'required|min:30'
        ];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'name_tema' => 'имя темы'
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'name_tema.required' => 'Необходимо заполнить имя темы',
            'text_notation.required' => 'Необходимо заполнить текст новости',
            'name_tema.min' => 'Минимальная длина темы 5 символов',
            'name_tema.max' => 'Максимальная длина темы 150 символов',
            'text_notation.min' => 'Минимальная длина сообщения 30 символов'
        ];
    }
}
