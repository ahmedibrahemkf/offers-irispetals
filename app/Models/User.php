<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'username',
        'phone',
        'email',
        'role',
        'is_active',
        'can_create_records',
        'can_update_records',
        'can_delete_records',
        'base_salary',
        'hire_date',
        'avatar_url',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'can_create_records' => 'boolean',
            'can_update_records' => 'boolean',
            'can_delete_records' => 'boolean',
            'base_salary' => 'decimal:2',
            'hire_date' => 'date',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canCreateRecords(): bool
    {
        if ($this->role === 'owner') {
            return true;
        }

        $fallback = in_array($this->role, ['manager', 'staff'], true);

        return $this->resolveCrudPermission('can_create_records', $fallback);
    }

    public function canUpdateRecords(): bool
    {
        if ($this->role === 'owner') {
            return true;
        }

        $fallback = in_array($this->role, ['manager', 'staff'], true);

        return $this->resolveCrudPermission('can_update_records', $fallback);
    }

    public function canDeleteRecords(): bool
    {
        if ($this->role === 'owner') {
            return true;
        }

        $fallback = $this->role === 'manager';

        return $this->resolveCrudPermission('can_delete_records', $fallback);
    }

    private function resolveCrudPermission(string $column, bool $fallback): bool
    {
        if (array_key_exists($column, $this->attributes) && $this->attributes[$column] !== null) {
            return (bool) $this->attributes[$column];
        }

        return $fallback;
    }

    public function financials(): HasMany
    {
        return $this->hasMany(EmployeeFinancial::class, 'employee_id');
    }
}
