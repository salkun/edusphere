<?php

namespace App\Http\Requests\Api;

use App\Support\RoleMapper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
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
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(['siswa', 'guru', 'admin', 'student', 'teacher'])],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated($key, $default);

        return [
            'name' => $validated['username'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => RoleMapper::toDatabase($validated['role']),
        ];
    }
}
