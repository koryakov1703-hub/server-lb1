<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\PermissionRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class AttachRolePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'permission_id' => ['required', 'integer', 'exists:permissions,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $roleId = (int) $this->route('role');
            $permissionId = (int) $this->input('permission_id');

            if ($permissionId === 0) {
                return;
            }

            $exists = PermissionRole::query()
                ->where('role_id', $roleId)
                ->where('permission_id', $permissionId)
                ->whereNull('deleted_at')
                ->exists();

            if ($exists) {
                $validator->errors()->add('permission_id', 'Это разрешение уже назначено роли.');
            }
        });
    }

    public function permissionId(): int
    {
        return (int) $this->input('permission_id');
    }
}
