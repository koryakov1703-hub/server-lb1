<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\DTO\RegisterDTO;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
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
                Rule::unique('users', 'username'),
            ],
            'email' => [
                'required',
                'string',
                'email',
                Rule::unique('users', 'email'),
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\w\s]).+$/',
            ],
            'c_password' => [
                'required',
                'same:password',
            ],
            'birthday' => [
                'required',
                'date',
                'date_format:Y-m-d',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (Carbon::parse($value)->age < 14) {
                        $fail('Возраст должен быть не менее 14 лет.');
                    }
                },
            ],
        ];
    }

    public function toDTO(): RegisterDTO
    {
        return new RegisterDTO(
            username: $this->validated('username'),
            email: $this->validated('email'),
            password: $this->validated('password'),
            birthday: $this->validated('birthday'),
        );
    }
}
