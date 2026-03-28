<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import api from '@tenant/api/axios';
import { User, Employee } from '@tenant/types/api';
import InjectEspecialist from '@tenant/components/Talent/InjectEspecialist.vue';


const employees = ref<Employee[]>([]);
const isLoading = ref(true);
const searchQuery = ref('');

// Computed para filtrar la lista según el input de búsqueda
const filteredEmployees = computed(() => {
    if (!searchQuery.value) return employees.value;
    
    const query = searchQuery.value.toLowerCase();
    return employees.value.filter(emp => {
        const nameMatch = emp.user.name.toLowerCase().includes(query);
        const titleMatch = emp.job_title.toLowerCase().includes(query);
        const skillMatch = emp.extracted_skills?.some(skill => skill.toLowerCase().includes(query));
        
        return nameMatch || titleMatch || skillMatch;
    });
});

onMounted(async () => {
    try {
        const response = await api.get('/employees');
        // Asegúrate de que la API devuelva la data en response.data.content
        employees.value = response.data.content;
    } catch (error) {
        console.error("Error fetching talent pool:", error);
    } finally {
        isLoading.value = false;
    }
});

// Helper para obtener la URL del avatar.
const getAvatar = (emp: Employee) => {
    if (emp.avatar_secure_url) {
        return emp.avatar_secure_url;
    }
    return `https://ui-avatars.com/api/?name=${encodeURIComponent(emp.user.name)}&background=0df2f2&color=0a192f&bold=true`;
};

// Helpers para estilos según el estado de asignación
const getStatusConfig = (status: string, allocation: string = 'AVAILABLE') => {
    if (status === 'on_leave') {
        return {
            label: '[ON_LEAVE]',
            badgeClass: 'border-slate-500/50 text-slate-400 bg-slate-900',
            dotClass: 'bg-slate-400',
            pulse: false
        };
    }
    
    if (allocation === 'ASSIGNED') {
         return {
            label: '[ASSIGNED]',
            badgeClass: 'border-amber-500/50 text-amber-500 bg-amber-900/20',
            dotClass: 'bg-amber-500',
            pulse: false
        };
    }

    return {
        label: '[AVAILABLE]',
        badgeClass: 'border-[#10b981]/50 text-[#10b981] bg-[#10b981]/10',
        dotClass: 'bg-[#10b981]',
        pulse: true
    };
};
</script>
<template>
    <div class="h-full w-full bg-[#0a192f] text-[#ccd6f6] flex flex-col overflow-hidden">
        <header class="flex-none px-6 py-6 border-b border-[#112240] bg-[#0a192f]/90 backdrop-blur-md z-30 flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <div class="w-3 h-3 bg-[#10b981] rounded-full pulse-emerald"></div>
                    <h1 class="text-2xl md:text-3xl font-bold tracking-tighter uppercase font-mono text-white">
                        Neural Talent Pool
                    </h1>
                </div>
                <p class="text-[#8892b0] font-mono text-xs tracking-widest uppercase">
                    Command_Center // System_Active // LicitAI_V4.0
                </p>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-4 items-center">
                <div class="relative w-full sm:w-80 group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="material-symbols-outlined text-[#0df2f2] text-sm">search</span>
                    </div>
                    <input v-model="searchQuery" 
                           class="block w-full pl-10 pr-3 py-2.5 border border-[#112240] bg-[#112240]/50 text-[#0df2f2] placeholder-[#8892b0] focus:outline-none focus:ring-1 focus:ring-[#0df2f2] focus:border-[#0df2f2] sm:text-sm font-mono transition-all rounded-none" 
                           placeholder="Search_Database..." 
                           type="text" />
                </div>
                <InjectEspecialist />
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-6 custom-scrollbar relative">
            
            <div v-if="isLoading" class="absolute inset-0 flex flex-col items-center justify-center z-10 bg-[#0a192f]">
                <div class="relative flex items-center justify-center mb-4">
                    <div class="absolute inset-0 border-t-2 border-[#0df2f2] rounded-full animate-spin"></div>
                    <span class="material-symbols-outlined text-4xl text-[#0df2f2] animate-pulse">memory</span>
                </div>
                <p class="text-[#0df2f2] font-mono text-xs uppercase tracking-widest animate-pulse">Scanning Roster...</p>
            </div>

            <div v-else-if="filteredEmployees.length === 0" class="flex flex-col items-center justify-center h-64 opacity-50">
                <span class="material-symbols-outlined text-5xl text-[#0df2f2] mb-4">person_search</span>
                <p class="font-mono text-[#0df2f2] tracking-widest uppercase">NO_MATCHES_FOUND</p>
            </div>

            <section v-else class="grid grid-cols-1 lg:grid-cols-2 2xl:grid-cols-3 gap-6">
                
                <article v-for="emp in filteredEmployees" :key="emp.id"
                         class="cyber-card relative group p-6 bg-[#112240]/60 backdrop-blur-md border border-[#0df2f2]/20 hover:border-[#0df2f2] transition-all duration-500 rounded-sm flex flex-col">
                    
                    <div class="flex justify-between items-start mb-6">
                        <div class="relative">
                            <div class="relative w-16 h-16 bg-[#233554] p-1">
                                <div class="tech-corner-tl"></div>
                                <div class="tech-corner-br"></div>
                                <img :src="getAvatar(emp)" :alt="emp.user.name" 
                                     class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500" />
                            </div>
                        </div>
                        
                        <div class="text-right">
                            <span :class="getStatusConfig(emp.status, emp.allocation_status).badgeClass" 
                                  class="inline-flex items-center gap-2 px-2 py-1 border font-mono text-[9px] tracking-tighter uppercase">
                                <span :class="[
                                        getStatusConfig(emp.status, emp.allocation_status).dotClass, 
                                        getStatusConfig(emp.status, emp.allocation_status).pulse ? 'animate-pulse' : ''
                                      ]" 
                                      class="w-1.5 h-1.5 rounded-full"></span>
                                {{ getStatusConfig(emp.status, emp.allocation_status).label }}
                            </span>
                            <p class="mt-2 text-[#8892b0] font-mono text-[9px]">ID: {{ emp.id.substring(0,8).toUpperCase() }}</p>
                        </div>
                    </div>

                    <div class="space-y-4 flex-grow">
                        <div>
                            <h3 class="text-lg font-bold text-white tracking-tight truncate">{{ emp.user.name }}</h3>
                            <p class="text-[#0df2f2] font-mono text-[10px] uppercase tracking-widest mt-1 truncate">{{ emp.job_title }}</p>
                        </div>
                        
                        <div class="space-y-2">
                            <p class="text-[9px] font-mono text-[#8892b0] uppercase tracking-[0.2em] border-b border-[#112240] pb-1">NEURAL_EXTRACTED_SKILLS</p>
                            <div class="flex flex-wrap gap-1.5 pt-1">
                                <template v-if="emp.extracted_skills && emp.extracted_skills.length">
                                    <span v-for="skill in emp.extracted_skills.slice(0, 4)" :key="skill"
                                          class="px-2 py-0.5 border border-[#233554] text-[#ccd6f6] font-mono text-[9px] bg-[#0a192f]/40 whitespace-nowrap">
                                        {{ skill }}
                                    </span>
                                    <span v-if="emp.extracted_skills.length > 4" class="px-2 py-0.5 text-[#0df2f2] font-mono text-[9px]">
                                        +{{ emp.extracted_skills.length - 4 }}
                                    </span>
                                </template>
                                <span v-else class="text-[9px] font-mono text-slate-500 italic">No data injected yet.</span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mt-6 pt-4 border-t border-[#112240]">
                        <button :disabled="!emp.cv_path"
                                :class="emp.cv_path ? 'border-[#0df2f2]/50 text-[#0df2f2] hover:bg-[#0df2f2]/10 cursor-pointer' : 'border-slate-700 text-slate-600 cursor-not-allowed'"
                                class="px-3 py-2 border font-mono text-[10px] uppercase tracking-widest transition-colors flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[14px]">description</span>
                            CV
                        </button>
                        <button class="px-3 py-2 bg-[#0df2f2]/10 border border-[#0df2f2] text-[#0df2f2] font-bold font-mono text-[10px] uppercase tracking-widest hover:bg-[#0df2f2] hover:text-[#0a192f] transition-all flex items-center justify-center gap-2">
                            Assign
                            <span class="material-symbols-outlined text-[14px]">bolt</span>
                        </button>
                    </div>
                </article>

            </section>
        </div>
    </div>
</template>
<style scoped>
/* Scrollbar Minimalista */
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(13, 242, 242, 0.2); border-radius: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(13, 242, 242, 0.6); }

/* Animaciones Cyberpunk de Stitch */
@keyframes pulse-emerald {
    0%, 100% { transform: scale(1); opacity: 1; box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
    50% { transform: scale(1.1); opacity: 0.8; box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
}
.pulse-emerald { animation: pulse-emerald 2s infinite; }

@keyframes scanline {
    0% { transform: translateY(-100%); }
    100% { transform: translateY(100%); }
}
.cyber-card { overflow: hidden; }
.cyber-card::after {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 2px;
    background: linear-gradient(90deg, transparent, #0df2f2, transparent);
    animation: scanline 4s linear infinite;
    opacity: 0.1;
    pointer-events: none;
}

/* Esquinas Tecnológicas para Avatar */
.tech-corner-tl {
    position: absolute;
    top: -4px;
    left: -4px;
    width: 10px;
    height: 10px;
    border-top: 2px solid #0df2f2;
    border-left: 2px solid #0df2f2;
}
.tech-corner-br {
    position: absolute;
    bottom: -4px;
    right: -4px;
    width: 10px;
    height: 10px;
    border-bottom: 2px solid #0df2f2;
    border-right: 2px solid #0df2f2;
}
</style>