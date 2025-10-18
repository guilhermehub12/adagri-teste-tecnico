<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case GESTOR = 'gestor';
    case TECNICO = 'tecnico';
    case EXTENSIONISTA = 'extensionista';

    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrador',
            self::GESTOR => 'Gestor',
            self::TECNICO => 'Técnico de Campo',
            self::EXTENSIONISTA => 'Extensionista Rural',
        };
    }

    public function canCreate(): bool
    {
        return in_array($this, [self::ADMIN, self::GESTOR, self::TECNICO]);
    }

    public function canEdit(): bool
    {
        return in_array($this, [self::ADMIN, self::GESTOR]);
    }

    public function canDelete(): bool
    {
        return $this === self::ADMIN;
    }

    public function canManageUsers(): bool
    {
        return $this === self::ADMIN;
    }
}
