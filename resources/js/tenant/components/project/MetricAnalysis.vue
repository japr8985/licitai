<script setup lang="ts">
import { ScoreEngineBreakdown } from '@tenant/types/api';
import { computed } from 'vue';



const props = defineProps<{
    breakdown: ScoreEngineBreakdown
}>();

// 2. Lógica "Cyberpunk" para evaluar cada métrica
const getMetricConfig = (score: number, max: number, type: string) => {
    // Evitar división por cero o errores raros
    const safeScore = Math.max(0, Math.min(score, max));
    const pct = (safeScore / max) * 100;
    
    let colorClass = '';
    let glowClass = '';
    let statusText = '';

    // Evaluación: ÓPTIMO (>= 80%)
    if (pct >= 80) {
        colorClass = 'text-primary';
        glowClass = 'bg-primary shadow-[0_0_8px_#0df2f2]';
        
        if (type === 'financial') statusText = '[OPTIMAL]';
        else if (type === 'scope') statusText = '[CRYSTAL_CLEAR]';
        else if (type === 'legal') statusText = '[COMPLIANT]';
        else statusText = '[FEASIBLE]';
    } 
    // Evaluación: ADVERTENCIA (50% - 79%)
    else if (pct >= 50) {
        colorClass = 'text-amber-500';
        glowClass = 'bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.5)]';
        
        if (type === 'financial') statusText = '[STRETCHED]';
        else if (type === 'scope') statusText = '[VAGUE_TERMS]';
        else if (type === 'legal') statusText = '[MINOR_GAPS]';
        else statusText = '[AGGRESSIVE]';
    } 
    // Evaluación: PELIGRO (< 50%)
    else {
        colorClass = 'text-red-500';
        glowClass = 'bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.6)]';
        
        if (type === 'financial') statusText = '[CRITICAL_RISK]';
        else if (type === 'scope') statusText = '[AMBIGUOUS]';
        else if (type === 'legal') statusText = '[HIGH_EXPOSURE]';
        else statusText = '[UNREALISTIC]';
    }

    return { pct, colorClass, glowClass, statusText };
};

// 3. Estructuramos la data para iterarla fácilmente en el template
const metrics = computed(() => {
    // Si breakdown es undefined, usamos este objeto por defecto con ceros
    const bd = props.breakdown || {
        financial_score: 0,
        technical_scope_score: 0,
        legal_compliance_score: 0,
        timeline_score: 0
    };

    return [
        {
            label: 'Financial Health',
            score: bd.financial_score,
            max: 40,
            config: getMetricConfig(bd.financial_score, 40, 'financial')
        },
        {
            label: 'Scope Clarity',
            score: bd.technical_scope_score,
            max: 30,
            config: getMetricConfig(bd.technical_scope_score, 30, 'scope')
        },
        {
            label: 'Legal Compliance',
            score: bd.legal_compliance_score,
            max: 20,
            config: getMetricConfig(bd.legal_compliance_score, 20, 'legal')
        },
        {
            label: 'Timeline Feasibility',
            score: bd.timeline_score,
            max: 10,
            config: getMetricConfig(bd.timeline_score, 10, 'timeline')
        }
    ];
});

// Helper para formatear el número (ej: 05/10 en vez de 5/10)
const formatScore = (num: number) => num.toString().padStart(2, '0');
</script>

<template>
    <div class="h-full glass-card rounded-xl p-6 font-mono">
        <h3 class="text-white font-bold text-sm uppercase tracking-widest mb-8 border-b border-white/10 pb-4 flex items-center justify-between">
            Metric Analysis
            <span class="text-primary text-[10px] animate-pulse">[SYS_DIAGNOSTICS: ACTIVE]</span>
        </h3>
        
        <div class="space-y-8">
            <div v-for="metric in metrics" :key="metric.label">
                <div class="flex justify-between text-[11px] mb-2">
                    <span class="text-slate-400 uppercase">{{ metric.label }}</span>
                    <span :class="metric.config.colorClass" class="font-bold">
                        {{ formatScore(metric.score) }}/{{ metric.max }} 
                        <span class="text-slate-600 font-normal ml-1">{{ metric.config.statusText }}</span>
                    </span>
                </div>
                <div class="h-1.5 w-full bg-slate-800 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-1000 ease-out"
                         :class="metric.config.glowClass"
                         :style="{ width: `${metric.config.pct}%` }">
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@reference "../../../../css/app.css";
.glass-card {
    @apply bg-slate-900/40 backdrop-blur-md border border-white/5;
}
</style>