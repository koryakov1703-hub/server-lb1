<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\DTO\PermissionDTO;
use Illuminate\Foundation\Http\FormRequest;

class StorePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'unique:permissions,name'],
            'slug' => ['required', 'string', 'regex:/^[a-zA-Z0-9_-]+$/', 'unique:permissions,slug'],
            'description' => ['nullable', 'string'],
        ];
    }

    public function toDTO(): PermissionDTO
    {
        return new PermissionDTO(
            id: 0,
            name: (string) $this->input('name'),
            slug: (string) $this->input('slug'),
            description: $this->input('description'),
            createdAt: ''
        );
    }
}
