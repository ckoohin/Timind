<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
<<<<<<< HEAD
            'email' => 'required|email|exists:users,email',
            'password' => 'required'
=======
            'email' => 'required',
            'password' => 'required',
        ];
    }
    public function messages() {
        return [
            'email.required' => "Vui lòng điền Email",
            'email.email' => 'Email không hợp lệ',
            'password.required' => "Vui lòng điền mật khẩu",
>>>>>>> be490f0617e04cab9bb59357c07635e0ab0bb723
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không hợp lệ',
            'email.exists' => 'Email không tồn tại',
            'password.required' => 'Vui lòng nhập mật khẩu'
        ];
    }
}