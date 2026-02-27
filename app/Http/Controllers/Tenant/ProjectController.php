<?php

namespace App\Http\Controllers\Tenant;

use App\Enums\Tenant\ProjectStatus;
use App\Http\Controllers\Controller;
use App\Jobs\Tenant\SendProjectToN8n;
use App\Models\Tenant\Project;
use App\Tenant\Reports\ProjectAnalysisReport;
use App\Tenant\Service\FileService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    use ApiResponse;

    public function __construct(protected FileService $fileService)
    {
        #
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Traemos los proyectos ordenados por fecha de creación
        $projects = Project::with('user:id,name')->orderBy('created_at', 'desc')->get();

        // Calculamos métricas rápidas para las cards del Dashboard
        $stats = [
            'total_valuation' => $projects->sum('valuation'),
            'mean_viability' => $projects->avg('ai_score') ?? 0,
            'active_count' => $projects->count(),
        ];
        return $this->success($projects);
        // return $this->success([
        //     'projects' => $projects,
        //     'stats' => $stats
        // ], 'SYNC_COMPLETE');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'issuing_entity' => 'required|string',
            'valuation' => 'nullable|string',
            'upload_uuid' => 'required|string', // El ID con el que subimos los chunks
            'file_name' => 'required|string',
            'total_chunks' => 'required|integer'
        ]);

        $finalPath = $this->fileService->assembleChunks(
            $request->upload_uuid,
            $request->total_chunks,
            $request->file_name,
            'projects'
        );

        $project = Project::create([
            'title' => $request->title,
            'issuing_entity' => $request->issuing_entity,
            'valuation' => $request->valuation,
            'status' => ProjectStatus::PENDING,
            'ai_verdict' => 'Pending Neural Analysis...',
            'file_path' => $finalPath,
            'user_id' => auth('sanctum')->user()->id
        ]);
        SendProjectToN8n::dispatch($project->id, tenant('id'))->onConnection('central_queue');
        return $this->success($project, 'ENTRY_CREATED', 201);
    }
    public function analysis(Project $project, Request $request): JsonResponse
    {

        $data = $request->validate([
            'ai_score' => 'required|integer',
            'ai_verdict' => 'required|string',
            'ai_analysis_data' => 'required|array',
        ]);

        $project->update([
            'ai_score' => $data['ai_score'],
            'ai_verdict' => $data['ai_verdict'],
            'ai_analysis_data' => $data['ai_analysis_data'],
            'status' => ProjectStatus::ANALYZED, // Cambiamos el estado para que el frontend lo muestre
        ]);
        return $this->success($project, '');
    }
    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        return $this->success($project);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        //
    }
    public function download_original_document(Project $project)
    {
        // Verificamos que el archivo exista
        if (!$project->file_path || !Storage::disk('tenants')->exists($project->file_path)) {

            return $this->error('File not found', 404);
        }

        // Forzamos la descarga con el nombre original o uno limpio
        return Storage::disk('tenants')->download(
            $project->file_path,
            $project->title . '.pdf' // Renombramos al descargar para mejor UX
        );
    }
    public function export_report(Project $project)
    {
        // Validamos que esté analizado
        if (!$project->ai_analysis_data) {
            return $this->error('Project analysis not ready yet.', 400);
        }

        $pdf = new ProjectAnalysisReport($project);
        $content = $pdf->build();

        return Response::make($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="LicitAI_Report_' . $project->id . '.pdf"',
        ]);
    }

    public function reanalyze(Project $project, Request $request) 
    {
    
        // 1. Validamos que envíen texto (opcional pero recomendado)
        $data = $request->validate([
            'additional_instructions' => 'nullable|string|max:2000'
        ]);

        // 2. Guardamos las instrucciones en el JSON para que la IA tenga contexto
        //    Esto sirve de "memoria" del último prompt de refinamiento.
        $analysisData = $project->ai_analysis_data ?? [];
        $analysisData['meta_data']['last_refinement_prompt'] = $data['additional_instructions'] ?? null;

        // 3. Actualizamos estado y datos
        $project->update([
            'status' => 'processing', // Esto activará el loader en el Frontend
            'ai_analysis_data' => $analysisData
        ]);

        // 4. Disparamos el Job
        // El Job deberá leer $project->ai_analysis_data['meta_data']['last_refinement_prompt']
        SendProjectToN8n::dispatch($project, tenant('id'));

        return response()->json([
            'success' => true,
            'message' => 'Neural Engine Restarted with new parameters.'
        ]);
    }
}
