<script setup lang="ts">
import { computed } from 'vue';
import type { Project } from '@tenant/types/api'; // Asegúrate de tener este tipo o usa 'any'
import ProjectStatusBadge from '@tenant/components/project/ProjectStatusBadge.vue';

const props = defineProps<{
    projects: Project[]
}>();

// Función helper para formatear moneda
const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('en-US', { 
        style: 'currency', 
        currency: 'USD',
        maximumFractionDigits: 0 
    }).format(value);
};

// Función para tiempo relativo (simple)
const timeAgo = (dateString: string) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now.getTime() - date.getTime()) / 1000);
    
    if (diffInSeconds < 60) return 'Just now';
    if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`;
    if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`;
    return `${Math.floor(diffInSeconds / 86400)}d ago`;
};

// Agrupamos los proyectos por columnas lógicas
const columns = computed(() => {
    return {
        inbox: props.projects.filter(p => p.status === 'pending'),
        processing: props.projects.filter(p => p.status === 'processing'),
        analyzed: props.projects.filter(p => p.status === 'analyzed'),
        decision: props.projects.filter(p => ['approved', 'rejected', 'failed'].includes(p.status))
    };
});
</script>

<template>
    <div class="h-full w-full overflow-x-auto overflow-y-hidden relative z-0">
        
        <div class="absolute inset-0 bg-[linear-gradient(rgba(255,255,255,0.03)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.03)_1px,transparent_1px)] bg-[size:40px_40px] pointer-events-none"></div>

        <div class="flex h-full gap-6 min-w-max pb-4 px-2">

            <div class="flex flex-col w-80 h-full bg-[#111818]/60 backdrop-blur-sm rounded-xl border border-[#283939] group/col">
                <div class="p-4 flex items-center justify-between border-b border-[#283939] bg-[#111818]/80 rounded-t-xl">
                    <div class="flex items-center gap-2">
                        <span class="size-2 rounded-full bg-slate-400/50"></span>
                        <h3 class="text-sm font-bold text-slate-400 tracking-wide uppercase">INBOX</h3>
                        <span class="ml-1 px-1.5 py-0.5 rounded bg-[#283939] text-[10px] font-mono text-white font-bold">
                            {{ columns.inbox.length }}
                        </span>
                    </div>
                </div>
                
                <div class="flex-1 overflow-y-auto p-3 space-y-3 custom-scrollbar">
                    <div v-for="project in columns.inbox" :key="project.id"
                         class="group bg-[#1a2626] hover:bg-[#202e2e] border border-[#283939] hover:border-primary/40 rounded-lg p-3 cursor-grab active:cursor-grabbing transition-all hover:shadow-glow-card relative overflow-hidden">
                        
                        <div class="absolute top-0 left-0 w-[3px] h-full bg-slate-500/30"></div>
                        
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-[10px] font-mono text-slate-400 px-1.5 py-0.5 rounded bg-[#111818] border border-[#283939]">
                                #{{ project.id.substring(0, 6) }}
                            </span>
                             <router-link :to="{name: 'manager.projects.show', params: {id: project.id}}" class="text-slate-400 hover:text-white">
                                <span class="material-symbols-outlined text-[16px]">visibility</span>
                             </router-link>
                        </div>
                        
                        <h4 class="text-white text-sm font-semibold mb-3 leading-snug group-hover:text-primary transition-colors line-clamp-2">
                            {{ project.title }}
                        </h4>
                        
                        <div class="flex items-center justify-between text-[11px] text-slate-500 font-mono">
                            <span class="truncate max-w-[120px]">{{ project.issuing_entity }}</span>
                            <span>{{ timeAgo(project.created_at) }}</span>
                        </div>
                    </div>

                    <div v-if="columns.inbox.length === 0" class="text-center py-10 opacity-30">
                        <span class="material-symbols-outlined text-4xl mb-2">inbox</span>
                        <p class="text-xs font-mono">NO NEW INTEL</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col w-80 h-full bg-[#111818]/60 backdrop-blur-sm rounded-xl border border-primary/30 shadow-[0_0_20px_rgba(13,242,242,0.05)] animate-pulse-border relative">
                <div class="p-4 flex items-center justify-between border-b border-primary/30 bg-[#111818]/80 rounded-t-xl relative overflow-hidden">
                    <div class="absolute inset-0 bg-primary/5"></div>
                    <div class="relative flex items-center gap-2 z-10">
                        <span class="relative flex size-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                            <span class="relative inline-flex rounded-full size-2.5 bg-primary"></span>
                        </span>
                        <h3 class="text-sm font-bold text-primary tracking-wide uppercase drop-shadow-[0_0_5px_rgba(13,242,242,0.8)]">PROCESSING</h3>
                        <span class="ml-1 px-1.5 py-0.5 rounded bg-primary/20 text-[10px] font-mono text-primary font-bold border border-primary/30">
                            {{ columns.processing.length }}
                        </span>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto p-3 space-y-3 custom-scrollbar">
                    <div v-for="project in columns.processing" :key="project.id"
                         class="group bg-[#1a2626] border border-primary/50 rounded-lg p-3 cursor-wait transition-all shadow-[0_0_10px_rgba(13,242,242,0.1)] relative overflow-hidden">
                        
                        <div class="absolute bottom-0 left-0 h-1 bg-primary/20 w-full overflow-hidden">
                            <div class="h-full bg-primary w-[50%] animate-indeterminate shadow-[0_0_10px_rgba(13,242,242,0.8)]"></div>
                        </div>

                        <div class="flex justify-between items-start mb-2">
                             <span class="text-[10px] font-mono text-primary px-1.5 py-0.5 rounded bg-primary/10 border border-primary/30">
                                NODE-{{ project.id.substring(0, 4) }}
                            </span>
                            <span class="text-primary animate-spin-slow">
                                <span class="material-symbols-outlined text-[16px]">settings</span>
                            </span>
                        </div>

                        <h4 class="text-white text-sm font-semibold mb-3 leading-snug line-clamp-2">
                            {{ project.title }}
                        </h4>

                        <div class="text-[10px] text-slate-400 font-mono flex items-center gap-2">
                            <span class="material-symbols-outlined text-[12px] animate-pulse">memory</span>
                            <span>Neural Analysis in Progress...</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col w-80 h-full bg-[#111818]/60 backdrop-blur-sm rounded-xl border border-[#283939] group/col hover:border-accent-cyan/30 transition-colors">
                <div class="p-4 flex items-center justify-between border-b border-[#283939] bg-[#111818]/80 rounded-t-xl group-hover/col:border-accent-cyan/30">
                    <div class="flex items-center gap-2">
                        <span class="size-2 rounded-full bg-cyan-400 box-shadow-[0_0_8px_#00e5ff]"></span>
                        <h3 class="text-sm font-bold text-cyan-400 tracking-wide uppercase">ANALYZED</h3>
                        <span class="ml-1 px-1.5 py-0.5 rounded bg-cyan-400/10 text-[10px] font-mono text-cyan-400 font-bold border border-cyan-400/20">
                            {{ columns.analyzed.length }}
                        </span>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto p-3 space-y-3 custom-scrollbar">
                    <div v-for="project in columns.analyzed" :key="project.id"
                         class="group bg-[#1a2626] hover:bg-[#202e2e] border border-l-4 border-y-[#283939] border-r-[#283939] rounded-lg p-3 cursor-grab active:cursor-grabbing transition-all hover:shadow-glow-card"
                         :class="project.ai_score >= 80 ? 'border-l-primary' : (project.ai_score >= 50 ? 'border-l-amber-500' : 'border-l-red-500')">
                        
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-[10px] font-mono font-bold px-1.5 py-0.5 rounded"
                                :class="project.ai_score >= 80 ? 'text-primary bg-primary/10' : (project.ai_score >= 50 ? 'text-amber-500 bg-amber-500/10' : 'text-red-500 bg-red-500/10')">
                                {{ project.ai_score }}/100 SCORE
                            </span>
                            <span :class="project.ai_score >= 80 ? 'text-primary' : 'text-slate-500'">
                                <span class="material-symbols-outlined text-[16px]">check_circle</span>
                            </span>
                        </div>

                        <h4 class="text-white text-sm font-semibold mb-2 leading-snug line-clamp-2">
                            {{ project.title }}
                        </h4>
                        
                        <p class="text-[11px] text-slate-400 leading-relaxed mb-3 line-clamp-2">
                            {{ project.ai_analysis_data?.analysis_core?.executive_summary || 'Analysis complete.' }}
                        </p>

                        <div class="flex items-center justify-between pt-2 border-t border-[#283939]">
                            <span class="text-[10px] text-slate-500 font-mono">{{ formatCurrency(project.valuation) }}</span>
                            
                            <router-link :to="{name: 'manager.projects.show', params: {id: project.id}}"
                                  class="text-[10px] text-white hover:text-cyan-400 uppercase font-bold tracking-wider flex items-center gap-1">
                                VIEW REPORT <span class="material-symbols-outlined text-[12px]">arrow_forward</span>
                            </router-link>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col w-80 h-full bg-[#111818]/60 backdrop-blur-sm rounded-xl border border-[#283939] group/col">
                <div class="p-4 flex items-center justify-between border-b border-[#283939] bg-[#000]/40 rounded-t-xl">
                    <div class="flex items-center gap-2">
                        <span class="size-2 rounded-full bg-white shadow-[0_0_8px_#fff]"></span>
                        <h3 class="text-sm font-black text-white tracking-wide uppercase">DECISION</h3>
                        <span class="ml-1 px-1.5 py-0.5 rounded bg-white/20 text-[10px] font-mono text-white font-bold border border-white/30">
                            {{ columns.decision.length }}
                        </span>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto p-3 space-y-3 custom-scrollbar">
                    <div v-for="project in columns.decision" :key="project.id"
                         class="group bg-gradient-to-b from-[#1a2626] to-[#0f1616] border border-white/10 rounded-lg p-4 cursor-pointer transition-all hover:border-white/30 relative overflow-hidden">
                        
                        <div class="flex justify-between items-start mb-3">
                            <ProjectStatusBadge :status="project.status" />
                            <router-link :to="{name: 'manager.projects.show', params: {id: project.id}}">
                                <span class="material-symbols-outlined text-[18px] text-slate-500 hover:text-white">open_in_new</span>
                            </router-link>
                        </div>

                        <h4 class="text-white text-sm font-bold mb-2 leading-snug line-clamp-2">
                            {{ project.title }}
                        </h4>

                        <div class="flex flex-col gap-2 mb-2">
                            <div class="flex justify-between text-[11px] font-mono text-slate-500 border-b border-white/5 pb-1">
                                <span>BUDGET</span>
                                <span class="text-white">{{ formatCurrency(project.valuation) }}</span>
                            </div>
                        </div>
                        
                        <div class="absolute bottom-0 left-0 w-full h-1" 
                             :class="project.status === 'approved' ? 'bg-emerald-500' : 'bg-red-500'"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<style scoped>
/* Animaciones personalizadas traídas del HTML de Stitch */
@keyframes border-pulse {
    0% { box-shadow: 0 0 0 0 rgba(13, 242, 242, 0.4); border-color: rgba(13, 242, 242, 0.4); }
    70% { box-shadow: 0 0 0 6px rgba(13, 242, 242, 0); border-color: rgba(13, 242, 242, 0.8); }
    100% { box-shadow: 0 0 0 0 rgba(13, 242, 242, 0); border-color: rgba(13, 242, 242, 0.4); }
}

@keyframes indeterminate {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(200%); }
}

.animate-pulse-border {
    animation: border-pulse 2s infinite;
}

.animate-indeterminate {
    animation: indeterminate 1.5s infinite linear;
}

.animate-spin-slow {
    animation: spin 3s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #0a0f12; 
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #283939; 
    border-radius: 3px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #0df2f2; 
}
</style>