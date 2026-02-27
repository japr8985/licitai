<?php

namespace App\Enums\Tenant;

enum UserRole: string
{
    case MANAGER = 'manager';
    case PROJECT_MANAGER = 'project_manager';
    case DEVELOPER = 'developer';

    // Método útil para visualización o etiquetas
    public function label(): string
    {
        return match($this) {
            self::MANAGER => 'Gerente General',
            self::PROJECT_MANAGER => 'Project Manager',
            self::DEVELOPER => 'Desarrollador Senior',
        };
    }
}
