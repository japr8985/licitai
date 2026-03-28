<?php

namespace App\Jobs\Tenant;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Tenant\Project;
use App\Tenant\Service\FileService;

class SendProjectToN8n implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [30, 60, 120];

    /**
     * Create a new job instance.
     */
    public function __construct(protected string $projectId, protected string $tenantId)
    {
        #
    }
    /**
     * Execute the job.
     */
    public function handle(FileService $fileService): void
    {
        
        
        // 1. Forzamos la inicialización del Tenant con el ID que recibimos
        if (!tenant('id')) {
            tenancy()->initialize($this->tenantId);
        }

        // 2. Ahora que el contexto es el correcto, buscamos el modelo
        $project = Project::find($this->projectId);
        
        $downloadUrl = $fileService->generateTemporaryUrl($project->file_path, 120);

        // 2. Preparamos el Payload con contexto industrial
        $payload = [
            'event' => 'tender_ingested',
            'project_id' => $project->id,
            'tenant_id' => tenant('id'),
            'company_name' => tenant('company_name'),
            'industry' => tenant('industry') ?? 'GENERAL_CONSULTING',
            'download_url' => $downloadUrl,
            'metadata' => [
                'title' => $project->title,
                'entity' => $project->issuing_entity,
                'valuation' => $project->valuation,
                'timestamp' => now()->toIso8601String(),
            ]
        ];

        // // 3. Disparo al Webhook de n8n
        $webhookUrl = config('services.n8n.webhook_url');

        if (!$webhookUrl) {
            Log::error("N8N_WEBHOOK_URL no configurado en el entorno.");
            return;
        }

        $response = Http::timeout(10)->post($webhookUrl, $payload);

        if ($response->failed()) {
            Log::warning("Fallo al contactar n8n para el proyecto {$project->id}. Estado: " . $response->status());
            throw new \Exception("N8n Webhook failed"); // Esto dispara el reintento (backoff)
        }

        Log::info("Proyecto enviado exitosamente a n8n: {$project->id}");
    }
}
