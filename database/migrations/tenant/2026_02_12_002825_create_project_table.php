<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            // Relación con el usuario del tenant que subió la licitación
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            
            $table->string('title');
            $table->string('issuing_entity'); // La entidad que licita
            $table->decimal('valuation', 15, 2)->nullable();
            $table->timestamp('deadline')->nullable();
            $table->string('file_path')->nullable();
            
            // Estados: uploading, processing, analyzed, failed
            $table->string('status')->default('processing');
            
            // Aquí guardamos el score de 0 a 100 y el veredicto (High/Low Viability)
            $table->integer('ai_score')->nullable();
            $table->string('ai_verdict')->nullable(); 
            
            // Datos crudos del análisis de n8n/IA
            $table->jsonb('ai_analysis_data')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project');
    }
};
