<?php

namespace App\Http\Controllers\Tenant;

use App\Enums\Tenant\ProjectStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\ProjectRequest;
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
use App\Models\Tenant\Employee;

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
    public function store(ProjectRequest $request)
    {
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
        SendProjectToN8n::dispatch($project->id, tenant('id'));

        return response()->json([
            'success' => true,
            'message' => 'Neural Engine Restarted with new parameters.'
        ]);
    }

    public function squadRecommendations(Project $project)
    {
        // 1. Extraemos TODAS las skills requeridas del roadmap
        $analysisData = $project->ai_analysis_data ?? [];
        $roadmap = $analysisData['suggested_roadmap'] ?? [];

        $requiredSkills = collect($roadmap)->flatMap(function ($phase) {
            return $phase['required_skills'] ?? [];
        })->unique()->values()->toArray();

        if (empty($requiredSkills)) {
            return $this->success([]);
        }

        // 2. Buscamos empleados activos (y disponibles)
        // CTO FIX: Agregamos with('user') para matar el problema N+1 de base de datos
        $employees = Employee::with('user')->where('status', 'active')->get();

        // 3. Calculamos el "Match Score" con "Fuzzy Matching"
        $reqSkillsLower = array_map('strtolower', $requiredSkills);

        $recommendedSquad = $employees->map(function ($employee) use ($requiredSkills, $reqSkillsLower) {

            $employeeSkills = $employee->extracted_skills ?? [];
            $empSkillsLower = array_map('strtolower', $employeeSkills);

            $matchedSkills = [];

            // Búsqueda inteligente: "IoT" hará match con "IoT Implementation"
            foreach ($empSkillsLower as $empSkill) {
                foreach ($reqSkillsLower as $index => $reqSkill) {
                    // Verificamos si la skill del empleado está dentro de la requerida o viceversa
                    if (str_contains($reqSkill, $empSkill) || str_contains($empSkill, $reqSkill)) {
                        // Guardamos el nombre original de la skill requerida para que la UI se vea limpia
                        $matchedSkills[] = $requiredSkills[$index];
                    }
                }
            }

            $matchedSkills = array_unique($matchedSkills);
            $matchCount = count($matchedSkills);
            $totalRequired = count($requiredSkills);

            $matchPercentage = $totalRequired > 0 ? round(($matchCount / $totalRequired) * 100) : 0;

            return [
                'id' => $employee->id,
                'user_id' => $employee->user_id,
                'name' => $employee->user->name ?? 'Unknown Agent',
                'job_title' => $employee->job_title,
                'avatar_secure_url' => $employee->avatar_secure_url, // Asegúrate de llamar a la URL segura que creamos
                'match_percentage' => $matchPercentage,
                'matched_skills' => array_values($matchedSkills),
                'missing_skills' => array_values(array_diff($requiredSkills, $matchedSkills))
            ];
        })
            ->filter(function ($candidate) {
                // Para pruebas, puedes cambiar esto a >= 0 si quieres ver a todos los empleados
                return $candidate['match_percentage'] > 0;
            })
            ->sortByDesc('match_percentage')
            ->take(5)
            ->values();

        return $this->success($recommendedSquad);
    }
}
