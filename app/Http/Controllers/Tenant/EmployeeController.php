<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Employee;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Enums\EmployeeStatus;
use App\Http\Requests\Tenant\EmployeeRequest;
use App\Jobs\Tenant\ExtractSkillsFromCvJob;
use App\Tenant\Service\FileService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class EmployeeController extends Controller
{
    use ApiResponse;

    public function __construct(protected FileService $fileService)
    {
        // throw new \Exception('Not implemented');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::with('user:id,name,email')
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->success($employees);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmployeeRequest $request)
    {

        try {
            // 2. Transacción: Todo o nada
            $employee = DB::transaction(function () use ($request) {
                
                // Crear el Usuario base
                // Si no envían password, le generamos uno aleatorio (ej. para que luego lo recupere)
                $password = $request->password?? Str::random(12);
                
                $user = \App\Models\User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($password),
                ]);

                $cvPath = null;
                $avatarPath = null;
                
                if ($request->has('cv')) {
                    $cvPath = $this->fileService->assembleChunks(
                    $request->cv['upload_uuid'],
                    $request->cv['total_chunks'],
                    $request->cv['file_name'],
                    'CV'
                    );
                }

                if ($request->has('avatar')) {
                    $avatarPath = $this->fileService->assembleChunks(
                        $request->avatar['upload_uuid'],
                        $request->avatar['total_chunks'],
                        $request->avatar['file_name'],
                        'avatars'
                    );
                }
                

                // Crear el Perfil de Empleado
                $newEmployee = Employee::create([
                    'user_id' => $user->id,
                    'job_title' => $request->job_title,
                    'department' => $request->department,
                    'avatar_path' => $avatarPath,
                    'cv_path' => $cvPath,
                    'status' => \App\Enums\Tenant\EmployeeStatus::ACTIVE,
                ]);

                // Cargamos la relación para devolverla en la respuesta
                $newEmployee->load('user');

                return $newEmployee;
            });

            if ($employee->cv_path) {
                ExtractSkillsFromCvJob::dispatch($employee->id, tenant('id'))->onConnection('central_queue');
            }

            return response()->json([
                'success' => true,
                'message' => 'Specialist successfully ingested into the Neural Network.',
                'content' => $employee
            ], 201);

        } catch (\Exception $e) {
            // Si algo falla, Laravel hace un "rollback" automático de la base de datos
            // pero debemos borrar los archivos si se llegaron a subir
            return response()->json([
                'success' => false,
                'message' => 'System Failure: Unable to register specialist.',
                'error' => $e->getMessage()
            ], 500);
        }
    
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        $employee->load('user');
        $employee->secure_cv_path = $this->fileService->generateTemporaryUrl($employee->cv_path, 30);
        return $this->success($employee);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $employee->fill($request->all());
        $employee->update();
        return $this->success($employee);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        //
    }

    public function matchmaking_candidates()
    {

        $data = Employee::select(['id', 'job_title', 'extracted_skills'])->where('status', '=', \App\Enums\Tenant\EmployeeStatus::ACTIVE)
            ->get();

        return $this->success($data);
    }

}
