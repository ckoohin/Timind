<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
    public function rules(): array {
        return [
<<<<<<< HEAD
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed'
=======
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    public function messages() {
        return [
            'name.required' => "Vui lòng nhập tên",
            'name.string' => "Tên phải là dạng chuỗi",
            'email.required' => "Vui lòng nhập email",
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email đã tồn tại',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.confirmed' => "Mật khẩu không trùng nhau",
>>>>>>> be490f0617e04cab9bb59357c07635e0ab0bb723
        ];
    }

    public function messages()
    {
        return [
            'name.required' => "Vui lòng nhập tên",
            'name.string' => "Tên phải là dạng chuỗi",
            'email.required' => "Vui lòng nhập trường name",
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email đã tồn tại',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.confirmed' => "Mật khẩu không trùng nhau",
        ];
    }
}