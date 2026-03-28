<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\URL;

class Employee extends Model
{
    /** @use HasFactory<\Database\Factories\Tenant\EmployeeFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'job_title',
        'department',
        'avatar_path',
        'cv_path',
        'extracted_skills',
        'status',
    ];
    protected $casts = [
        'extracted_skills' => 'array',
        'status' => \App\Enums\Tenant\EmployeeStatus::class, 
    ];
    // Aseguramos que el frontend siempre reciba este campo virtual
    protected $appends = ['allocation_status', 'avatar_secure_url'];

    public function getAllocationStatusAttribute()
    {
        // 1. Si está de vacaciones o despedido, no importa su carga.
        if ($this->status !== 'active') {
            return 'UNAVAILABLE';
        }

        // 2. Aquí luego conectaremos con la tabla de Tareas/Sprints
        // Ejemplo ficticio: $activeTasksCount = $this->tasks()->where('status', 'in_progress')->count();
        $activeTasksCount = 0; // Por ahora mockeado en 0

        // 3. Lógica de cálculo
        if ($activeTasksCount > 0) {
            return 'ASSIGNED'; // O podríamos calcular % de carga (ej. "80% LOAD")
        }

        return 'AVAILABLE';
    }
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Helper: Obtener la URL pública del Avatar
     */
    public function getAvatarUrlAttribute()
    {
        return $this->avatar_path 
            ? asset('storage/' . $this->avatar_path) 
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->user->name ?? 'User') . '&background=0df2f2&color=0a192f';
    }
    public function getAvatarSecureUrlAttribute()
    {
        // Si no tiene avatar en la DB, devuelve null (en Vue puedes mostrar un ícono por defecto)
        if (!$this->avatar_path) {
            return null; 
        }
        $tenantId = explode('/', $this->avatar_path)[0];
        // Generamos la URL. Caducará en 2 horas (120 minutos).
        return URL::temporarySignedRoute(
            'tenant.files.avatar', // El nombre de la ruta que creamos en el paso 1
            now()->addMinutes(120),
            [
                // Pasamos los parámetros que requiere la ruta
                'tenant' => $tenantId, // Asumiendo que tus rutas necesitan el tenant_id
                'path' => $this->avatar_path
            ]
        );
    }
}
