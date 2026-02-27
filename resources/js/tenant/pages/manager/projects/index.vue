<script setup lang="ts">
import { ref, onMounted, defineAsyncComponent, computed } from 'vue';
import type { ApiResponse, Project } from '@tenant/types/api';
import api from '@tenant/api/axios';
import { useUIStore } from '@tenant/store/ui';

const ui = useUIStore();
const views = {
    grid: defineAsyncComponent(() => import('@tenant/components/project/views/ProjectGridView.vue')),
    list: defineAsyncComponent(() => import('@tenant/components/project/views/ProjectListView.vue')),
    kanban: defineAsyncComponent(() => import('@tenant/components/project/views/ProjectKanbanView.vue'))
};

const projects = ref<Project[]>([]);
const isLoading = ref(true);
const currentView = ref<'grid' | 'list' | 'kanban'>('grid'); // Vista por defecto
const activeComponent = computed(() => views[currentView.value]);


onMounted(async () => {
    try {
        const response = await api.get<ApiResponse<Project[]>>('/projects');
        console.log(response.data.content)
        projects.value = response.data.content;
        // projects.value = projects || [];
    } catch (error) {
        console.error("Error loading projects:", error);
        // ui.notify('error', error)
        
    } finally {
        isLoading.value = false;
    }
});
</script>

<template>
    <div class="flex flex-col h-full w-full bg-[#0a192f] overflow-hidden">
        
        <header
            class="flex-none px-6 py-4 border-b border-slate-800/80 bg-[#0a192f]/90 backdrop-blur-md z-30 flex flex-wrap items-center justify-between gap-4">

            <div class="flex flex-col">
                <div
                    class="flex items-center gap-2 text-primary/60 text-[10px] font-mono uppercase tracking-[0.2em] mb-1">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                    </span>
                    Live System Status: Optimal
                </div>
                <h1 class="text-2xl font-black text-white uppercase tracking-tight">Active Operations</h1>
                <p class="text-slate-400 text-xs font-mono">
                    {{ projects.length }} NODES DETECTED // TERMINAL V4.2
                </p>
            </div>

            <div class="flex items-center gap-6">

                <div class="bg-[#112240] p-1 rounded-lg border border-slate-700 flex items-center gap-1 shadow-inner">
                    <button @click="currentView = 'grid'"
                        class="p-2 rounded transition-all flex items-center justify-center"
                        :class="currentView === 'grid' ? 'bg-primary/20 text-primary shadow-[0_0_10px_rgba(13,242,242,0.2)]' : 'text-slate-500 hover:text-slate-300 hover:bg-white/5'"
                        title="Grid View">
                        <span class="material-symbols-outlined text-lg">grid_view</span>
                    </button>

                    <button @click="currentView = 'list'"
                        class="p-2 rounded transition-all flex items-center justify-center"
                        :class="currentView === 'list' ? 'bg-primary/20 text-primary shadow-[0_0_10px_rgba(13,242,242,0.2)]' : 'text-slate-500 hover:text-slate-300 hover:bg-white/5'"
                        title="List View">
                        <span class="material-symbols-outlined text-lg">view_list</span>
                    </button>

                    <button @click="currentView = 'kanban'"
                        class="p-2 rounded transition-all flex items-center justify-center"
                        :class="currentView === 'kanban' ? 'bg-primary/20 text-primary shadow-[0_0_10px_rgba(13,242,242,0.2)]' : 'text-slate-500 hover:text-slate-300 hover:bg-white/5'"
                        title="Kanban Board">
                        <span class="material-symbols-outlined text-lg">view_kanban</span>
                    </button>
                </div>
            </div>
        </header>

        <div class="flex-1 overflow-hidden relative">

            <div class="absolute inset-0 z-0 opacity-[0.02] pointer-events-none"
                style="background-image: linear-gradient(#0df2f2 1px, transparent 1px), linear-gradient(90deg, #0df2f2 1px, transparent 1px); background-size: 40px 40px;">
            </div>

            <div v-if="isLoading" class="absolute inset-0 flex flex-col items-center justify-center z-10 bg-[#0a192f]">
                <div class="relative flex items-center justify-center mb-4">
                    <div class="absolute inset-0 border-t-2 border-primary rounded-full animate-spin"></div>
                    <span class="material-symbols-outlined text-4xl text-primary animate-pulse">hub</span>
                </div>
                <p class="text-primary font-mono text-xs uppercase tracking-widest animate-pulse">Establishing secure
                    link...</p>
            </div>

            <div v-else class="h-full w-full relative z-10 p-6 custom-scr">
                <Transition name="fade-view" mode="out-in">
                    <KeepAlive>
                        <Suspense>
                            
                            <component 
                                :is="activeComponent" 
                                :projects="projects" 
                                :key="currentView" 
                            />
                            
                            <template #fallback>
                                <div class="flex flex-col items-center justify-center h-64 text-primary opacity-50">
                                    <span class="material-symbols-outlined animate-spin text-3xl mb-2">autorenew</span>
                                    <span class="text-[10px] font-mono tracking-widest uppercase">Loading Interface Chunk...</span>
                                </div>
                            </template>
                        </Suspense>
                    </KeepAlive>
                </Transition>
            </div>
        </div>

    </div>
</template>

<style scoped>
/* Animación suave al cambiar de vista */
.fade-view-enter-active,
.fade-view-leave-active {
    transition: opacity 0.2s ease, transform 0.2s ease;
}

.fade-view-enter-from {
    opacity: 0;
    transform: translateY(10px);
}

.fade-view-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}
</style>