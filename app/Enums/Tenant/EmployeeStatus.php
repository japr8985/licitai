<?php

namespace App\Enums\Tenant;

enum EmployeeStatus: string
{
    case ACTIVE = 'active'; // active: El empleado está trabajando normalmente en la empresa.
    case ON_LEAVE = 'on_leave'; // on_leave: Vacaciones, permiso, reposo médico (La IA ignora a esta persona).
    case INACTIVE = 'inactive'; // inactive: Ya no trabaja en la empresa.

    // Opcional: Un método helper para obtener etiquetas bonitas para la UI
    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Active',
            self::ON_LEAVE => 'On Leave',
            self::INACTIVE => 'Inactive',
        };
    }
}
