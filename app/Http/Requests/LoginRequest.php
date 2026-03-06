<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\DTO\LoginDTO;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => [
                'required',
                'string',
                'min:7',
                'regex:/^[A-Z][a-zA-Z]*$/',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\w\s]).+$/',
            ],
        ];
    }

    public function toDTO(): LoginDTO
    {
        return new LoginDTO(
            username: $this->validated('username'),
            password: $this->validated('password'),
        );
    }
}
