<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\DTO\PermissionDTO;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $permissionId = $this->route('permission');

        return [
            'name' => ['required', 'string', "unique:permissions,name,$permissionId"],
            'slug' => ['required', 'string', 'regex:/^[a-zA-Z0-9_-]+$/', "unique:permissions,slug,$permissionId"],
            'description' => ['nullable', 'string'],
        ];
    }

    public function toDTO(): PermissionDTO
    {
        return new PermissionDTO(
            id: (int) $this->route('permission'),
            name: (string) $this->input('name'),
            slug: (string) $this->input('slug'),
            description: $this->input('description'),
            createdAt: ''
        );
    }
}
