export enum ProjectStatus {
    PENDING = 'pending',       // Creado, sin archivo aún
    PROCESSING = 'processing', // Enviado a n8n / En cola
    ANALYZED = 'analyzed',     // n8n respondió con éxito
    FAILED = 'failed',         // Error en n8n o validación
    REJECTED = 'rejected',     // Rechazado
    APPROVED = 'approved',     // Aprobado
}
export type RecommendationVerdict = 'GO' | 'NO-GO' | 'conditional_GO';

export type ScopeClarity = 'HIGH' | 'MEDIUM' | 'LOW (RISKY)';
// 2. Interfaces de Componentes Internos del JSON
export interface AiAnalysisDataMetadata {
    currency: string;
    industry: string;
    detected_budget: string; // A veces la IA devuelve "Not Disclosed", por eso string está bien
}

// RENOMBRADO: De AiAnalysisScore a AiAnalysisCore para coincidir con el key del JSON
export interface AiAnalysisCore {
    executive_summary: string;
    scope_clarity_level: ScopeClarity; // Tipado estricto
}

export interface ScoreEngineBreakdown {
    timeline_score: number;
    financial_score: number;
    technical_scope_score: number;
    legal_compliance_score: number;
}

export interface AiAnalysisScoreEngine {
    breakdown: ScoreEngineBreakdown;
    total_score: number;
}

export interface AiAnalysisDecisionMatrix {
    reasoning: string;
    recommendation: RecommendationVerdict; // Tipado estricto
}

export interface AiAnalysisMarginProtection {
    financial_red_flags: string[];
    hidden_costs_detected: boolean;
}

// --- NUEVO: Estructuras para las Secciones "Mock" (Futuro Backend) ---
export interface AiResourceMember {
    name: string;
    role: string;
    match_score: number;
    skills_matched: string[];
}

export interface AiRoadmapPhase {
    title: string;
    description: string;
    sprints: string;
    hours: number;
}

// 3. El Objeto JSON Completo (Columna ai_analysis_data)
export interface AiAnalysisData {
    meta_data: AiAnalysisDataMetadata;
    analysis_core: AiAnalysisCore;
    scoring_engine: AiAnalysisScoreEngine;
    decision_matrix: AiAnalysisDecisionMatrix;
    margin_protection: AiAnalysisMarginProtection;
    
    // Opcionales (?) porque el backend actual aún no los envía siempre
    resource_allocation?: AiResourceMember[]; 
    project_roadmap?: AiRoadmapPhase[];
}

// 4. El Modelo Eloquent Completo
export interface Project {
    id: string;
    user_id: string;
    // user?: User; // Descomentar si incluyes la relación 'user' en la respuesta
    title: string;
    issuing_entity: string;
    valuation: number; // Laravel suele enviar números si no usas decimal cast string, pero cuidado aquí
    
    // Fechas vienen como string ISO desde la API
    deadline: string | null; 
    
    file_path: string | null;
    status: ProjectStatus;
    
    ai_score: number;
    ai_verdict: string; // O RecommendationVerdict si lo normalizas en DB
    ai_analysis_data: AiAnalysisData; // Aquí vive toda la estructura de arriba
    
    created_at: string;
    updated_at: string;
    deleted_at: string | null; // Puede ser null
}
export interface ApiResponse<T = any> {
    content: T;
    success: boolean;
    message: string;
}
export interface Tenant {
    id: string;
    company_name: string;
    plan: string;
}
export enum UserRole {
    MANAGER = 'manager',
    PROJECT_MANAGER = 'project_manager',
    DEVELOPER = 'developer'
}
export interface User {
    id: string;
    name: string;
    role: UserRole;
    tenant?: Tenant;
}
export interface LoginResponse {
    user: User;
    tenant: Tenant;
    token: string;
}
