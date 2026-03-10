<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\DTO\RoleDTO;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    /**
     * Проверяет, может ли пользователь выполнить запрос.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Правила валидации.
     */
    public function rules(): array
    {
        $roleId = $this->route('role');

        return [
            'name' => ['required', 'string', "unique:roles,name,$roleId"],
            'slug' => ['required', 'string', 'regex:/^[a-zA-Z0-9_-]+$/', "unique:roles,slug,$roleId"],
            'description' => ['nullable', 'string'],
        ];
    }

    /**
     * Преобразует данные запроса в DTO.
     */
    public function toDTO(): RoleDTO
    {
        return new RoleDTO(
            id: (int) $this->route('role'),
            name: (string) $this->input('name'),
            slug: (string) $this->input('slug'),
            description: $this->input('description'),
            createdAt: ''
        );
    }
}
