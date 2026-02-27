<script setup lang="ts">
import type { Project } from '@tenant/types/api';
import ProjectStatusBadge from '@tenant/components/project/ProjectStatusBadge.vue';
// import DownloadButton from '@/Components/Project/DownloadButton.vue'; // Reutilizamos si quieres, o usamos iconos simples

const props = defineProps<{
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
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString('en-CA'); // YYYY-MM-DD format
};

const getScoreColor = (score: number) => {
    if (score >= 80) return 'bg-primary shadow-[0_0_8px_rgba(13,242,242,0.6)]';
    if (score >= 50) return 'bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.6)]';
    return 'bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.6)]';
};

const getScoreWidth = (score: number) => {
    return `${Math.min(100, Math.max(0, score))}%`;
};
</script>

<template>
    <div class="flex-1 overflow-hidden z-10 h-full flex flex-col">
        
        <div class="min-w-[1000px] border border-slate-800 rounded-lg bg-[#0a192f]/50 shadow-2xl flex flex-col h-full">
            
            <div class="grid grid-cols-12 gap-4 bg-[#0a192f]/95 backdrop-blur px-4 py-3 border-b border-slate-800 sticky top-0 z-20">
                <div class="col-span-2 text-xs font-bold text-slate-500 uppercase tracking-widest">Status</div>
                <div class="col-span-3 text-xs font-bold text-slate-500 uppercase tracking-widest">Project / Entity</div>
                <div class="col-span-2 text-xs font-bold text-slate-500 uppercase tracking-widest text-right">Budget</div>
                <div class="col-span-2 text-xs font-bold text-slate-500 uppercase tracking-widest">AI Score</div>
                <div class="col-span-2 text-xs font-bold text-slate-500 uppercase tracking-widest">Timeline</div>
                <div class="col-span-1 text-xs font-bold text-slate-500 uppercase tracking-widest text-right">Actions</div>
            </div>

            <div class="flex-1 overflow-y-auto custom-scrollbar">
                
                <div v-if="projects.length === 0" class="p-10 text-center text-slate-500">
                    <span class="material-symbols-outlined text-4xl mb-2">table_rows</span>
                    <p class="font-mono text-sm">NO DATA AVAILABLE</p>
                </div>

                <div v-for="project in projects" :key="project.id" 
                     class="group grid grid-cols-12 gap-4 px-4 py-4 border-b border-slate-800/50 hover:bg-white/[0.02] transition-colors relative items-center">
                    
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-primary opacity-0 group-hover:opacity-100 transition-opacity shadow-[0_0_10px_rgba(13,242,242,0.8)]"></div>

                    <div class="col-span-2 pl-2">
                        <ProjectStatusBadge :status="project.status" />
                    </div>

                    <div class="col-span-3 pr-4">
                        <div class="flex flex-col">
                            <router-link :to="{name: 'manager.projects.show', params: {id: project.id}}"
                                  class="text-sm font-bold text-slate-200 group-hover:text-primary transition-colors truncate block"
                                  :title="project.title">
                                {{ project.title }}
                            </router-link>
                            <span class="text-xs text-slate-500 mt-0.5 truncate flex items-center gap-1">
                                <span class="material-symbols-outlined text-[10px]">domain</span>
                                {{ project.issuing_entity }}
                            </span>
                        </div>
                    </div>

                    <div class="col-span-2 text-right">
                         <span class="font-mono text-sm font-medium" 
                               :class="project.valuation > 0 ? 'text-emerald-400' : 'text-slate-600'">
                            {{ project.valuation > 0 ? formatCurrency(project.valuation) : 'N/A' }}
                        </span>
                    </div>

                    <div class="col-span-2 pr-4">
                        <div v-if="project.status === 'analyzed' || project.status === 'approved' || project.status === 'rejected'" class="flex flex-col gap-1.5">
                            <div class="flex justify-between items-end">
                                <span class="text-xs font-mono text-white">{{ project.ai_score }}<span class="text-slate-600">/100</span></span>
                            </div>
                            <div class="h-1.5 w-full bg-slate-800 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-1000"
                                     :class="getScoreColor(project.ai_score)"
                                     :style="{ width: getScoreWidth(project.ai_score) }">
                                </div>
                            </div>
                        </div>
                        <div v-else-if="project.status === 'processing'" class="flex items-center gap-2 text-amber-500 text-xs font-mono">
                             <span class="material-symbols-outlined text-sm animate-spin">sync</span>
                             Scanning...
                        </div>
                        <div v-else class="text-xs text-slate-600 font-mono">Pending</div>
                    </div>

                    <div class="col-span-2">
                        <div class="flex flex-col gap-1">
                            <div class="flex items-center gap-2 text-[10px] font-mono text-slate-500">
                                <span class="material-symbols-outlined text-[12px]">event_available</span>
                                <span>{{ formatDate(project.created_at) }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-[10px] font-mono" 
                                 :class="!project.deadline ? 'text-slate-600' : 'text-slate-300'">
                                <span class="material-symbols-outlined text-[12px]">event_busy</span>
                                <span>{{ formatDate(project.deadline) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-1 flex justify-end gap-1 opacity-60 group-hover:opacity-100 transition-opacity">
                        <router-link :to="{name: 'manager.projects.show', params: {id: project.id}}" 
                              class="p-1.5 rounded hover:bg-white/10 text-slate-400 hover:text-primary transition-colors" 
                              title="View Analysis">
                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                        </router-link>
                        
                        <a v-if="project.file_path" :href="`/storage/${project.file_path}`" target="_blank"
                           class="p-1.5 rounded hover:bg-white/10 text-slate-400 hover:text-primary transition-colors"
                           title="Download PDF">
                            <span class="material-symbols-outlined text-[18px]">download</span>
                        </a>

                        <button class="p-1.5 rounded hover:bg-white/10 text-slate-400 hover:text-red-500 transition-colors" title="Delete">
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                        </button>
                    </div>

                </div>
            </div>

            <div class="px-4 py-2 border-t border-slate-800 bg-[#0a192f] flex justify-between items-center text-xs text-slate-500">
                <span>Total Nodes: {{ projects.length }}</span>
                <span class="font-mono">SYNCED</span>
            </div>

        </div>
    </div>
</template>

<style scoped>
/* Scrollbar personalizado traído del HTML de Stitch */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #0a192f;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #1e293b;
    border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #0df2f2;
}
</style>