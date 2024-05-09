<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatMessageRequest extends FormRequest
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
            'message' => 'required|string|min:1',
            'dialogId' => 'required|integer',
            'dialogWithId' => 'required|integer'
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'message.string' => 'Сообщение должно быть строкой',
            'message.min' => 'Минимальная длина сообщения: 1 символ'
        ];
    }
}
