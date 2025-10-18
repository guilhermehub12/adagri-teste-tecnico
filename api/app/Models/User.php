<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    /**
     * Get the role attribute with guaranteed enum conversion.
     *
     * This accessor ensures that the role is always returned as an Enum,
     * even if the casting hasn't been applied yet by Laravel.
     */
    protected function role(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => is_string($value) ? UserRole::from($value) : $value,
            set: fn ($value) => is_string($value) ? $value : $value->value,
        );
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(UserRole|string $role): bool
    {
        if (is_string($role)) {
            $role = UserRole::from($role);
        }

        return $this->role === $role;
    }

    /**
     * Check if user can create resources.
     */
    public function canCreate(): bool
    {
        return $this->role->canCreate();
    }

    /**
     * Check if user can edit resources.
     */
    public function canEdit(): bool
    {
        return $this->role->canEdit();
    }

    /**
     * Check if user can delete resources.
     */
    public function canDelete(): bool
    {
        return $this->role->canDelete();
    }

    /**
     * Check if user can manage other users.
     */
    public function canManageUsers(): bool
    {
        return $this->role->canManageUsers();
    }
}
