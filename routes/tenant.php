<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Stancl\Tenancy\Middleware\InitializeTenancyByPath;

use App\Http\Controllers\Tenant\AuthController;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;
use App\Http\Controllers\Tenant\ProjectController;
use App\Http\Controllers\Tenant\UploadFileController;
use App\Http\Controllers\Tenant\FileDownloadController;
use App\Http\Middleware\VerifyN8nSignature;
use App\Http\Controllers\Tenant\EmployeeController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

Route::middleware([
    'web',
    InitializeTenancyByPath::class,
    // PreventAccessFromCentralDomains::class,
])->prefix('app/{tenant}')
    ->group(function () {
        
        Route::get('/files/avatar', [FileDownloadController::class, 'avatar'])
                    ->name('tenant.files.avatar')
                    ->middleware('signed');
        Route::prefix('api')->group(function () {
            Route::get('/sanctum/csrf-cookie', [CsrfCookieController::class, 'show'])->name('tenant.sanctum.csrf');
            Route::post('/login', [AuthController::class, 'login'])->name('tenant.login');
            // Esta ruta debe ser accesible sin auth de usuario si n8n la va a usar, 
            // pero la FIRMA (signature) es la que protege el archivo.
            Route::get('/files/download/{path}', [FileDownloadController::class, 'download'])
                ->name('tenant.files.download')
                ->where('path', '.*');
            
            
            Route::middleware([VerifyN8nSignature::class])->group(function () {
                // endpoint para guardar el analisis de la licitacion    
                Route::patch('/projects/{project}/analysis',[ProjectController::class, 'analysis']);
                // agregar summary y skills generados por agente
                Route::put('/employees/{employee}/neural-update', [EmployeeController::class, 'update']);
                // obtener un empleado
                Route::get('/employees/{employee}', [EmployeeController::class, 'show']);
                // listado de empleados disponibles (id, job title, skills)
                Route::get('/matchmaking-candidates', [EmployeeController::class, 'matchmaking_candidates']);
            });
            
            

            Route::middleware([
                // 'Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class',
                'auth:sanctum'
            ])->group(function () {
                
                
                Route::post('/upload/chunk', [UploadFileController::class, 'uploadChunk']);

                Route::group(['prefix' => 'projects'], function() {
                    Route::get('/', [ProjectController::class, 'index']);
                    Route::get('/{project}', [ProjectController::class, 'show']);
                    Route::get('/{project}/export-report', [ProjectController::class, 'export_report']);
                    Route::get('/{project}/download', [ProjectController::class, 'download_original_document']);
                    Route::post('/finalize', [ProjectController::class, 'store']);
                    Route::post('/{project}/reanalyze', [ProjectController::class, 'reanalyze']);
                    Route::get('/{project}/squad-recommendations', [ProjectController::class, 'squadRecommendations']);
                });

                Route::group(['prefix' => 'employees'], function() {
                    Route::get('/', [EmployeeController::class, 'index']);
                    Route::post('/finalize', [EmployeeController::class, 'store']);
                    Route::put('/{employee}', [EmployeeController::class, 'update']);
                    Route::get('/{employee}', [EmployeeController::class, 'show']);
                    // Route::post('/finalize', [ProjectController::class, 'reanalyze']);
                });
                // perfil del usuario
                Route::get('/user', [AuthController::class, 'me']);
            });
        });

        // --- SECCIÓN DE FRONTEND (SPA) ---
        Route::get('/{any?}', function () {
            return view('tenant');
        })->where('any', '^(?!api).*$'); // La expresión regular evita que esta ruta choque con los endpoints de la API
    });
