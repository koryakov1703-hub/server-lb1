<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\DTO\RoleDTO;
use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    /**
     * Определяет, может ли пользователь выполнить запрос.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Возвращает правила валидации запроса.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'unique:roles,name'],
            'slug' => ['required', 'string', 'regex:/^[a-zA-Z0-9_-]+$/', 'unique:roles,slug'],
            'description' => ['nullable', 'string'],
        ];
    }

    /**
     * Преобразует данные запроса в DTO.
     */
    public function toDTO(): RoleDTO
    {
        return new RoleDTO(
            id: 0,
            name: (string) $this->input('name'),
            slug: (string) $this->input('slug'),
            description: $this->input('description'),
            createdAt: ''
        );
    }
}
