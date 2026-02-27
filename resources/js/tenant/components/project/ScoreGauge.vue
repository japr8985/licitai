<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps({
    score: { type: Number, required: true },
    verdict: { type: String, default: 'PENDING' }
});

// Configuración del SVG
const radius = 45;
const circumference = 2 * Math.PI * radius; // Aprox 282.7

// Calcula cuánto pintar del círculo
const strokeDashoffset = computed(() => {
    const progress = props.score / 100;
    return circumference * (1 - progress);
});

// Lógica de colores (Semáforo)
const colorClass = computed(() => {
    if (props.score >= 80) return 'text-primary drop-shadow-[0_0_8px_rgba(13,242,242,0.8)]';
    if (props.score >= 50) return 'text-amber-400 drop-shadow-[0_0_8px_rgba(251,191,36,0.8)]';
    return 'text-red-500 drop-shadow-[0_0_8px_rgba(239,68,68,0.8)]';
});

const verdictColor = computed(() => {
    if (props.score >= 80) return 'bg-primary/10 text-primary border-primary/40';
    if (props.score >= 50) return 'bg-amber-500/10 text-amber-500 border-amber-500/40';
    return 'bg-red-500/10 text-red-500 border-red-500/40';
});
</script>

<template>
    <div class="h-full glass-card rounded-xl p-8 flex flex-col items-center justify-center relative overflow-hidden group">
        <div class="absolute inset-0 bg-primary/5 opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
        
        <h3 class="text-slate-400 text-xs font-bold uppercase tracking-[0.2em] mb-6 z-10">Viability Score</h3>

        <div class="relative size-48 md:size-56 z-10">
            <svg class="size-full transform -rotate-90" viewBox="0 0 100 100">
                <circle class="text-slate-800" cx="50" cy="50" fill="none" r="45" stroke="currentColor" stroke-width="6"></circle>
                <circle 
                    :class="colorClass" 
                    cx="50" cy="50" fill="none" r="45" 
                    stroke="currentColor" 
                    :stroke-dasharray="circumference" 
                    :stroke-dashoffset="strokeDashoffset" 
                    stroke-linecap="round" 
                    stroke-width="6"
                    class="transition-all duration-1000 ease-out"
                ></circle>
            </svg>
            
            <div class="absolute inset-0 flex flex-col items-center justify-center">
                <span class="text-5xl md:text-6xl font-black text-white leading-none tracking-tighter">{{ score }}</span>
                <span class="text-slate-500 text-sm font-bold mt-1">/ 100</span>
            </div>
        </div>

        <div class="mt-6 z-10">
            <div :class="`px-4 py-1.5 border rounded-full font-black text-xs tracking-widest flex items-center gap-2 uppercase ${verdictColor}`">
                <span class="material-symbols-outlined text-sm">verified</span>
                {{ verdict.replace('_', ' ') }}
            </div>
        </div>
    </div>
</template>