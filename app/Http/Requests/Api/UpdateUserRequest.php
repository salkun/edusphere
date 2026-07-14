<?php

namespace App\Http\Requests\Api;

use App\Support\RoleMapper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'username' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->route('user'))],
            'password' => ['sometimes', 'nullable', 'string', 'min:8'],
            'role' => ['sometimes', 'required', Rule::in(['siswa', 'guru', 'admin', 'student', 'teacher'])],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function userAttributes(): array
    {
        $attributes = [];

        if ($this->has('username')) {
            $attributes['name'] = $this->input('username');
        }

        if ($this->has('email')) {
            $attributes['email'] = $this->input('email');
        }

        if ($this->filled('password')) {
            $attributes['password'] = $this->input('password');
        }

        if ($this->has('role')) {
            $attributes['role'] = RoleMapper::toDatabase($this->input('role'));
        }

        return $attributes;
    }
}
