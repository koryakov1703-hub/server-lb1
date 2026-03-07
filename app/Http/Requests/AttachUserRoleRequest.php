<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class AttachUserRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'role_id' => ['required', 'integer', 'exists:roles,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $userId = (int) $this->route('user');
            $roleId = (int) $this->input('role_id');

            if ($roleId === 0) {
                return;
            }

            $exists = UserRole::query()
                ->where('user_id', $userId)
                ->where('role_id', $roleId)
                ->whereNull('deleted_at')
                ->exists();

            if ($exists) {
                $validator->errors()->add('role_id', 'Эта роль уже назначена пользователю.');
            }
        });
    }

    public function roleId(): int
    {
        return (int) $this->input('role_id');
    }
}
