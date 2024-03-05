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
            'notationName'  => 'required|min:3|max:150',
            'notationText' => 'required|min:30'
        ];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'notationName' => 'Имя темы'
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'notationName.required' => 'Необходимо заполнить имя темы',
            'notationText.required' => 'Необходимо заполнить текст новости',
            'notationName.min' => 'Минимальная длина темы 5 символов',
            'notationName.max' => 'Максимальная длина темы 150 символов',
            'notationText.min' => 'Минимальная длина сообщения 30 символов'
        ];
    }
}
