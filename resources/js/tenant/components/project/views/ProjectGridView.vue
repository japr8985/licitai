<script setup lang="ts">
import type { Project } from '@tenant/types/api';
import ProjectStatusBadge from '@tenant/components/project/ProjectStatusBadge.vue';

defineProps<{
    projects: Project[]
}>();

// --- HELPERS ---
const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        maximumFractionDigits: 0
    }).format(value);
};

const formatDate = (dateString: string | null) => {
    if (!dateString) return '---';
    return new Date(dateString).toLocaleDateString('en-CA'); // YYYY-MM-DD
};

// Determina el estilo del borde y sombra según el estado
const getCardStyle = (project: Project) => {
    const status = project.status;

    if (status === 'processing') return 'border-amber-500/50 shadow-[0_0_15px_rgba(245,158,11,0.15)] animate-pulse-border';
    if (status === 'analyzed' && project.ai_score >= 80) return 'border-primary/50 shadow-[0_0_15px_rgba(13,242,242,0.15)] hover:border-primary';
    if (status === 'rejected' || (project.ai_score && project.ai_score < 50)) return 'border-red-500/40 shadow-[0_0_15px_rgba(239,68,68,0.15)] hover:border-red-500';

    // Default
    return 'border-slate-700 hover:border-slate-500';
};
</script>

<template>
    <div class="h-full w-full overflow-y-auto custom-scrollbar pr-2 pb-10">
        <div class="grid grid-cols-[repeat(auto-fill,minmax(350px,1fr))] overflow-y-auto custom-scrollbar gap-6 pb-10 pt-1">

            <div v-for="project in projects" :key="project.id"
                class="glass-card rounded-xl border flex flex-col h-full group transition-all duration-300 hover:-translate-y-1 relative overflow-hidden"
                :class="getCardStyle(project)">

                <router-link :to="{ name: 'manager.projects.show', params: { id: project.id } }"
                    class="absolute inset-0 z-0" />

                <div class="p-5 flex flex-col gap-4 flex-1 relative z-10 pointer-events-none">

                    <div class="flex justify-between items-start gap-3">
                        <h3
                            class="text-white text-lg font-bold leading-snug line-clamp-2 group-hover:text-primary transition-colors">
                            {{ project.title }}
                        </h3>
                        <div class="shrink-0">
                            <ProjectStatusBadge :status="project.status" />
                        </div>
                    </div>

                    <div class="flex items-center gap-2 text-slate-400 text-sm">
                        <span class="material-symbols-outlined text-lg">account_balance</span>
                        <span class="font-medium truncate">{{ project.issuing_entity }}</span>
                    </div>

                    <div class="mt-2 space-y-1">
                        <p class="text-[10px] text-slate-500 font-mono uppercase tracking-wider">Allocated Budget</p>
                        <div class="flex items-center gap-1"
                            :class="project.valuation > 0 ? 'text-white' : 'text-slate-600'">
                            <span class="material-symbols-outlined text-xl">attach_money</span>
                            <span class="text-2xl font-mono font-bold tracking-tight">
                                {{ project.valuation > 0 ? formatCurrency(project.valuation).replace('$', '') : 'N/A' }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mt-2 border-t border-white/5 pt-3">
                        <div class="space-y-0.5">
                            <p class="text-[10px] text-slate-500 font-mono uppercase tracking-tighter">Created</p>
                            <p class="text-xs font-mono text-slate-300">{{ formatDate(project.created_at) }}</p>
                        </div>
                        <div class="space-y-0.5">
                            <p class="text-[10px] text-slate-500 font-mono uppercase tracking-tighter">Deadline</p>
                            <p class="text-xs font-mono" :class="project.deadline ? 'text-primary' : 'text-slate-500'">
                                {{ formatDate(project.deadline) }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="px-5 pb-5 pt-2 flex flex-col gap-4 relative z-20">

                    <div v-if="['analyzed', 'approved', 'rejected'].includes(project.status)" class="space-y-2">
                        <div class="flex justify-between items-center text-[10px] font-mono uppercase tracking-widest">
                            <span class="text-slate-400">AI Intelligence Score</span>
                            <span class="font-bold"
                                :class="project.ai_score >= 80 ? 'text-primary' : (project.ai_score >= 50 ? 'text-amber-500' : 'text-red-500')">
                                {{ project.ai_score }}%
                            </span>
                        </div>
                        <div class="h-1.5 w-full bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full rounded-full shadow-[0_0_8px_currentColor]"
                                :class="project.ai_score >= 80 ? 'bg-primary text-primary' : (project.ai_score >= 50 ? 'bg-amber-500 text-amber-500' : 'bg-red-500 text-red-500')"
                                :style="{ width: `${project.ai_score}%` }">
                            </div>
                        </div>
                    </div>

                    <div v-else-if="project.status === 'processing'" class="space-y-2">
                        <div
                            class="flex justify-between items-center text-[10px] font-mono uppercase tracking-widest italic">
                            <span class="text-amber-500 animate-pulse">Running Neural Scan...</span>
                        </div>
                        <div class="h-1.5 w-full bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full bg-amber-500 w-[40%] animate-indeterminate"></div>
                        </div>
                    </div>

                    <div v-else class="h-8 flex items-center text-xs text-slate-600 font-mono">
                        Waiting for files...
                    </div>

                    <div class="flex justify-between gap-2 mt-1 border-t border-slate-800/50 pt-3">
                        <div class="flex gap-2">
                            <router-link :to="{ name: 'manager.projects.show', params: { id: project.id } }"
                                class="h-8 w-8 flex items-center justify-center rounded-lg bg-slate-800 hover:bg-primary hover:text-background-dark transition-all border border-slate-700 group/btn"
                                title="View Analysis">
                                <span class="material-symbols-outlined text-lg">visibility</span>
                            </router-link>

                            <a v-if="project.file_path" :href="`/storage/${project.file_path}`" target="_blank"
                                class="h-8 w-8 flex items-center justify-center rounded-lg bg-slate-800 hover:bg-primary hover:text-background-dark transition-all border border-slate-700"
                                title="Download PDF">
                                <span class="material-symbols-outlined text-lg">file_download</span>
                            </a>
                        </div>

                        <button
                            class="h-8 w-8 flex items-center justify-center rounded-lg bg-slate-800 hover:bg-red-500 hover:text-white transition-all border border-slate-700"
                            title="Delete Project">
                            <span class="material-symbols-outlined text-lg">delete</span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

</template>

<style scoped>
/* Animación de carga indefinida para 'Processing' */
@keyframes indeterminate {
    0% {
        transform: translateX(-100%);
    }

    100% {
        transform: translateX(200%);
    }
}

.animate-indeterminate {
    animation: indeterminate 1.5s infinite linear;
}

/* Pulso del borde para 'Processing' */
@keyframes border-pulse {
    0% {
        border-color: rgba(245, 158, 11, 0.3);
    }

    50% {
        border-color: rgba(245, 158, 11, 0.8);
    }

    100% {
        border-color: rgba(245, 158, 11, 0.3);
    }
}

.animate-pulse-border {
    animation: border-pulse 2s infinite;
}

/* Glassmorphism base */
.glass-card {
    background: rgba(30, 41, 59, 0.4);
    /* Slate-800 con opacidad */
    backdrop-filter: blur(12px);
}
</style>