<?php

namespace App\Jobs\Tenant;

use App\Models\Tenant\Employee;
use App\Tenant\Service\FileService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class ExtractSkillsFromCvJob implements ShouldQueue
{
    use Queueable,Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [30, 60, 120];

    /**
     * Create a new job instance.
     */
    public function __construct(protected string $employeeId, protected string $tenantId)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(FileService $fileService): void
    {
        if (!tenant('id')) {
            tenancy()->initialize($this->tenantId);
        }

        $employee = Employee::find($this->employeeId);

        $url = $fileService->generateTemporaryUrl($employee->cv_path, 120);

        $payload = [
            'tenant_id' => tenant('id'),
            'event' => 'extract_skills_employee',
            ...$employee->toArray(),
            'download_url' => $url
        ];
        $webhook = config('services.n8n.webhook_url_cv');

        if (!$webhook) {
            Log::error("webhook_url_cv no configurado en el entorno.");
            return;
        }

        Log::info("Webhook: ".$webhook);
        $response = Http::timeout(10)->post($webhook, $payload);

        if ($response->failed()) {
            Log::warning("Fallo al contactar n8n para el proyecto {$employee->id}. Estado: " . $response->status());
            throw new \Exception("N8n Webhook failed"); // Esto dispara el reintento (backoff)
        }

        Log::info("Empleado enviado exitosamente a n8n: {$employee->id}");
    }
}
