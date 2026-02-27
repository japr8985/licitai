<script setup lang="ts">
import { ref, computed, onMounted, Ref } from 'vue';
import { useRoute } from 'vue-router';

import DownloadOriginalPdf from '@tenant/components/project/buttons/DownloadOriginalPdf.vue';
import ExportReport from '@tenant/components/project/buttons/ExportReport.vue';
import ExecutiveTerminal from '@tenant/components/project/ExecutiveTerminal.vue';
import ScoreGauge from '@tenant/components/project/ScoreGauge.vue';
import RiskRadar from '@tenant/components/project/RiskRadar.vue';
import MetricAnalysis from '@tenant/components/project/MetricAnalysis.vue';
import RecommendedSquad from '@tenant/components/project/RecommendedSquad.vue';
import SuggestedRoadmap from '@tenant/components/project/SuggestedRoadmap.vue';
import ReanalysisModal from '@tenant/components/project/ReanalysisModal.vue'
import api from '@tenant/api/axios';
import { ApiResponse, Project } from '@tenant/types/api';
import { useUIStore } from '@tenant/store/ui';
import ProjectStatusBadge from '@tenant/components/project/ProjectStatusBadge.vue';

const route = useRoute()
const ui = useUIStore();
const project: Ref<Project> = ref({} as Project);

onMounted(async () => {
    await loadData()
});
const loadData = async() => {
    const id = route.params.id.toString()
    const res = await api.get<ApiResponse<Project>>(`/projects/${id}`);
    const { content } = res.data;
    project.value = content;
}
const projectId = computed((): string => {
    return route.params.id.toString();
});
const budget = computed((): string => {
    return Intl.NumberFormat(undefined, {currency: 'USD', style: 'currency'}).format(project.value?.valuation || 0);
})
const handleRefresh = async () => {
    await loadData()
};
const onReanalysisStarted = () => {
    // 1. Actualización Optimista: 
    // Cambiamos el estado localmente para feedback instantáneo
    project.value.status = 'processing';
    
    // 2. (Opcional) Polling o Websocket
    // Aquí podrías iniciar un intervalo para consultar el estado cada 5 segundos
    // hasta que vuelva a ser 'analyzed'.
    pollStatus();
};
const pollStatus = () => {
    const interval = setInterval(async () => {
        try {
            // Consultamos solo el estado
            const { data } = await api.get(`/projects/${project.value.id}`);
            
            if (data.content.status === 'analyzed') {
                // Si terminó, actualizamos todo el objeto project y paramos el poll
                Object.assign(project, data.content); // Reactividad de Vue 3
                clearInterval(interval);
                ui.notify('success', 'Analysis Complete', 'DATA UPDATED');
            }
        } catch (e) {
            clearInterval(interval);
        }
    }, 60000); // Check cada 5 segundos
};
</script>

<template>
    <div>
        <header class="w-full border-b border-white/10 bg-background-dark/80 backdrop-blur-md sticky top-0 z-50">
            <div class="max-w-[1400px] mx-auto px-6 py-4 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-6">
                    <div class="h-8 w-px bg-white/10 hidden md:block"></div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-3">
                            <h2 class="text-white text-lg font-bold leading-none truncate max-w-[300px] md:max-w-md">
                                {{ project.title}}
                            </h2>
                            <ProjectStatusBadge :status="project.status" />
                        </div>
                        <p class="text-slate-400 text-xs font-medium tracking-widest uppercase mt-1">
                            {{ project.issuing_entity }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <DownloadOriginalPdf :id="projectId"/>
                    <ReanalysisModal :project-id="projectId" @reanalysis-started="onReanalysisStarted"/>
                    <ExportReport :project="project" />
                </div>
            </div>
        </header>
        <div class="max-w-[1400px] mx-auto w-full p-6 lg:p-10">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 pb-40">
                <div class="md:col-span-8">
                    <ExecutiveTerminal summary="xz"/>
                </div>
                <div class="md:col-span-4">
                    <ScoreGauge :score="project.ai_score" :verdict="'conditional_go'"/>
                </div>
                <div class="md:col-span-7">
                    <RiskRadar />
                </div>
                <div class="md:col-span-5">
                    <MetricAnalysis />
                </div>
                <div class="md:col-span-12">
                    <RecommendedSquad />
                </div>
                <div class="md:col-span-12">
                    <SuggestedRoadmap />
                </div>
                <div class="md:col-span-12">
                    <div
                        class="glass-card rounded-xl p-4 flex flex-wrap items-center justify-between gap-6 border-t-2 border-primary/30 mt-6">
                        <div class="flex items-center gap-8">
                            <div class="flex flex-col">
                                <span class="text-slate-500 text-[10px] font-bold uppercase tracking-widest">
                                    Target Budget
                                </span>
                                <span class="text-white text-xl font-black font-mono">
                                    {{ budget }}
                                    <!-- $1,250,000 USD -->
                                </span>
                            </div>
                            <div class="h-10 w-px bg-white/10"></div>
                            <div class="flex flex-col">
                                <span class="text-slate-500 text-[10px] font-bold uppercase tracking-widest">Industry
                                    Segment</span>
                                <span class="text-primary text-sm font-bold uppercase flex items-center gap-2">
                                    <span class="material-symbols-outlined text-sm">cloud_done</span>
                                    {{ project?.ai_analysis_data?.meta_data?.industry }}
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center gap-6">
                            <div
                                class="flex items-center gap-3 px-4 py-2 bg-slate-900/50 rounded-lg border border-white/5">
                                <div class="flex items-center gap-1.5">
                                    <div class="size-2 rounded-full bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.8)]">
                                    </div>
                                    <span class="text-[10px] text-slate-400 font-bold uppercase">Hidden Costs
                                        Detected</span>
                                </div>
                                <span class="text-white font-bold font-mono text-sm">EST. +$45K</span>
                            </div>
                            <div class="flex gap-2">
                                <div
                                    class="size-10 rounded-lg border border-white/10 flex items-center justify-center text-slate-400 hover:text-white hover:bg-white/5 cursor-pointer transition-all">
                                    <span class="material-symbols-outlined text-xl">share</span>
                                </div>
                                <div
                                    class="size-10 rounded-lg border border-white/10 flex items-center justify-center text-slate-400 hover:text-white hover:bg-white/5 cursor-pointer transition-all">
                                    <span class="material-symbols-outlined text-xl">bookmark</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                    
                </div>
            <div class="fixed bottom-8 left-1/2 -translate-x-1/2 z-50">
                <div
                    class="bg-slate-900/80 backdrop-blur-xl border border-white/10 rounded-2xl px-6 py-4 flex items-center gap-8 shadow-2xl">
                    <div class="flex items-center gap-4">
                        <span class="text-[10px] text-slate-500 font-black uppercase tracking-tighter">Command
                            Center</span>
                        <div class="h-4 w-px bg-white/10"></div>
                    </div>
                    <div class="flex items-center gap-4">
                        <button
                            class="px-6 py-2 rounded-lg border border-red-500/30 text-red-500 text-sm font-bold hover:bg-red-500/10 transition-all">
                            Reject
                        </button>
                        <button
                            class="px-6 py-2 rounded-lg bg-white/5 border border-white/10 text-white text-sm font-bold hover:bg-white/10 transition-all">
                            Needs Revision
                        </button>
                        <button
                            class="px-6 py-2 rounded-lg border border-primary/30 text-primary text-sm font-bold bg-transparent hover:bg-primary/10 hover:shadow-[0_0_15px_rgba(13,242,242,0.2)] transition-all">
                            Authorize
                        </button>
                    </div>
                </div>
            </div>
            <div class="fixed top-0 left-0 w-full h-full pointer-events-none -z-10 opacity-20">
                <div class="absolute top-[10%] left-[5%] size-96 bg-primary/20 blur-[120px] rounded-full"></div>
                <div class="absolute bottom-[10%] right-[5%] size-96 bg-blue-500/10 blur-[120px] rounded-full"></div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@layer utilities {
    .glass-card {
        background: rgba(30, 41, 59, 0.4);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .glow-teal {
        box-shadow: 0 0 15px rgba(13, 242, 242, 0.3);
    }

    .glow-red {
        box-shadow: 0 0 15px rgba(239, 68, 68, 0.2);
    }

    .scanline {
        background: linear-gradient(to bottom, transparent 50%, rgba(13, 242, 242, 0.05) 50%);
        background-size: 100% 4px;
    }

    .hover-spin-effect:hover .material-symbols-outlined {
        transform: rotate(180deg);
        transition: transform 0.5s ease;
    }

    .timeline-line {
        background: linear-gradient(to bottom, #0df2f2 50%, transparent 50%);
        background-size: 1px 12px;
    }
}

/* Transición suave para la barra de comandos */
.slide-fade-enter-active,
.slide-fade-leave-active {
    transition: all 0.5s ease;
}

.slide-fade-enter-from,
.slide-fade-leave-to {
    transform: translateY(100px) translateX(-50%);
    /* Ajustado por el left-1/2 */
    opacity: 0;
}
</style>