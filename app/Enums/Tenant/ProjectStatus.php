<?php

namespace App\Enums\Tenant;

enum ProjectStatus: string
{
    case PENDING = 'pending';       // Creado, sin archivo aún
    case PROCESSING = 'processing'; // Enviado a n8n / En cola
    case ANALYZED = 'analyzed';     // n8n respondió con éxito
    case FAILED = 'failed';         // Error en n8n o validación
    case REJECTED = 'rejected';     // Rechazado
    case APPROVED = 'approved';     // Aprobado
}
