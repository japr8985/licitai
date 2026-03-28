
<template>
    <div class="h-full glass-card rounded-xl p-6 relative overflow-hidden">
        <div class="absolute top-0 right-0 p-4 font-mono text-[10px] text-primary/20 pointer-events-none">
            PLAN_REV: 0.9.4b
        </div>
        <div class="flex items-center gap-3 mb-10 border-b border-white/10 pb-4">
            <span class="material-symbols-outlined text-amber-500">route</span>
            <h3 class="text-white font-bold text-lg uppercase tracking-tight">AI Suggested Roadmap / Sprints</h3>
        </div>
        
        <div v-if="!enrichedPhases.length" class="text-center py-10 opacity-50">
            <span class="material-symbols-outlined text-4xl mb-2">pending</span>
            <p class="text-xs font-mono uppercase tracking-widest">Awaiting Neural Analysis...</p>
        </div>

        <div v-else class="relative pl-8 space-y-10">
            <div class="absolute left-[11px] top-2 bottom-2 w-px timeline-line"></div>
            
            <div v-for="phase in enrichedPhases" :key="phase.phase" 
                 class="relative flex flex-col md:flex-row md:items-start justify-between gap-4 transition-all duration-300"
                 :class="{ 'opacity-40': phase.status === 'pending' }">
                 
                <div :class="[
                        'absolute -left-10 mt-1 size-6 rounded border flex items-center justify-center z-10 transition-all',
                        phase.status === 'completed' ? 'bg-primary border-background-dark shadow-[0_0_10px_rgba(13,242,242,0.5)]' : '',
                        phase.status === 'in_progress' ? 'bg-slate-900 border-primary shadow-[0_0_10px_rgba(13,242,242,0.2)]' : '',
                        phase.status === 'pending' ? 'bg-slate-900 border-white/10' : ''
                    ]">
                    <span v-if="phase.status === 'completed'" class="material-symbols-outlined text-slate-900 text-xs font-black">check</span>
                    <span v-else-if="phase.status === 'in_progress'" class="material-symbols-outlined text-primary text-xs font-black animate-spin-slow">autorenew</span>
                    <span v-else class="material-symbols-outlined text-white/20 text-xs font-black">lock</span>
                </div>

                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-1">
                        <span :class="[
                                'font-mono text-[10px] uppercase font-bold tracking-widest',
                                phase.status === 'pending' ? 'text-slate-600' : 'text-primary'
                            ]">
                            Phase {{ phase.phase }}
                        </span>
                        <h4 :class="[
                                'font-bold text-base uppercase',
                                phase.status === 'pending' ? 'text-slate-500' : 'text-white'
                            ]">
                            {{ phase.title }}
                        </h4>
                    </div>
                    <p :class="[
                            'text-xs font-mono',
                            phase.status === 'pending' ? 'text-slate-600 italic' : 'text-slate-400'
                        ]">
                        {{ phase.description }}
                    </p>
                    
                    <div v-if="phase.required_skills?.length && phase.status !== 'pending'" class="flex flex-wrap gap-2 mt-3">
                        <span v-for="skill in phase.required_skills" :key="skill" 
                              class="px-1.5 py-0.5 bg-white/5 border border-white/10 rounded text-[9px] text-slate-300 font-mono uppercase">
                            {{ skill }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-4 min-w-[200px] justify-end">
                    <div class="px-3 py-1 bg-slate-900 border border-white/10 rounded flex items-center gap-2">
                        <span :class="['text-[10px] font-bold uppercase', phase.status === 'pending' ? 'text-slate-600' : 'text-slate-500']">
                            {{ phase.sprintLabel }}
                        </span>
                        <div class="h-3 w-px bg-white/10"></div>
                        <span :class="['text-[10px] font-bold', phase.status === 'pending' ? 'text-slate-600' : 'text-primary']">
                            {{ phase.estimatedHours }} HRS
                        </span>
                    </div>
                    
                    <div class="w-24 h-1 bg-slate-800 rounded-full overflow-hidden">
                        <div :class="[
                                'h-full transition-all duration-1000',
                                phase.status === 'completed' ? 'bg-primary w-full' : '',
                                phase.status === 'in_progress' ? 'bg-primary w-[35%] animate-pulse' : '',
                                phase.status === 'pending' ? 'w-0' : ''
                            ]">
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</template>
<script setup lang="ts">
import { Phase } from '@tenant/types/api';
import { computed } from 'vue';

const props = defineProps<{
    roadmap: Phase[]
}>();

// Constante de la empresa: 1 Sprint = 14 días
const SPRINT_LENGTH_DAYS = 14; 
const HOURS_PER_DAY = 8; // Para convertir los días a horas hombre (si quieres mantener la estética de "120 HOURS")

// Creamos un computed que enriquezca los datos de la IA con la lógica de Sprints
const enrichedPhases = computed(() => {
    let currentSprintStart = 1;

    return (props.roadmap || []).map((phase, index) => {
        // ¿Cuántos sprints (de 14 días) caben en esta fase? Redondeamos hacia arriba.
        // Ej: Fase de 30 días / 14 = 2.14 -> 3 Sprints requeridos.
        const sprintsNeeded = Math.ceil(phase.duration_days / SPRINT_LENGTH_DAYS);
        
        // Calculamos el rango de Sprints. Ej: "Sprint 1-3"
        const sprintEnd = currentSprintStart + sprintsNeeded - 1;
        const sprintLabel = sprintsNeeded === 1 
            ? `Sprint ${currentSprintStart}` 
            : `Sprint ${currentSprintStart}-${sprintEnd}`;

        // Calculamos las horas estimadas (opcional, para tu diseño)
        const estimatedHours = phase.duration_days * HOURS_PER_DAY;

        // Simulador de estado (Solo para visualización hasta que tengas lógica de estado real)
        // Asumiremos que la Fase 1 está "Completed", la Fase 2 "In Progress" y el resto "Pending"
        let status = 'pending';
        if (index === 0) status = 'completed';
        if (index === 1) status = 'in_progress';

        // Actualizamos el contador global de sprints para la siguiente fase
        currentSprintStart = sprintEnd + 1;

        return {
            ...phase,
            sprintLabel,
            estimatedHours,
            status // 'completed', 'in_progress', 'pending'
        };
    });
});
</script>
<style>
.timeline-line {
    /* Crea una línea de 1px de ancho */
    width: 1px;
    /* El truco: un gradiente que es 50% color y 50% transparente */
    background: linear-gradient(to bottom, #0df2f2 50%, transparent 50%);
    /* Define el tamaño del patrón (1px ancho, 12px alto para que se repita) */
    background-size: 1px 12px;
    /* Opcional: opacidad para que no compita con el texto */
    opacity: 0.7;
}
</style>