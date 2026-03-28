<template>
    <div class="h-full glass-card rounded-xl p-6">
        <div class="flex items-center justify-between mb-8 border-b border-white/10 pb-4">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-primary">groups</span>
                <h3 class="text-white font-bold text-lg uppercase tracking-tight">Recommended Squad /
                    Resource Allocation</h3>
            </div>
            <div class="text-[10px] font-mono text-slate-500 uppercase tracking-widest">Team ID:
                UNIT_402_ALPHA</div>
        </div>
        
        <div v-if="!enrichedSquad.length" class="text-center py-10 opacity-50">
            <span class="material-symbols-outlined text-4xl mb-2">engineering</span>
            <p class="text-xs font-mono uppercase tracking-widest">No squad members assigned yet.</p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-4 gap-4">
            
            <div v-for="member in enrichedSquad" :key="member.employee_id"
                class="rounded-xl p-4 flex flex-col gap-4 relative overflow-hidden transition-all duration-300"
                :class="[
                    member.match_percentage >= 80 ? 'bg-primary/5 border-2 border-primary/50 glow-teal' : 'bg-slate-800/20 border border-white/10 hover:border-white/20'
                ]">
                
                <div v-if="member.isLoading" class="absolute inset-0 bg-slate-800 animate-pulse z-10 flex items-center justify-center">
                    <span class="text-[10px] font-mono text-primary/50 animate-bounce">SYNCING_DATA...</span>
                </div>

                <div v-if="member.match_percentage >= 80" class="absolute top-2 right-2">
                    <span class="bg-primary text-slate-900 text-[8px] font-black px-2 py-0.5 rounded uppercase shadow-[0_0_10px_rgba(13,242,242,0.4)]">
                        Top Match
                    </span>
                </div>

                <div class="flex items-center gap-3 mt-4">
                    <div class="size-12 rounded border p-0.5 overflow-hidden shrink-0"
                         :class="member.match_percentage >= 80 ? 'bg-slate-800 border-primary/30' : 'bg-slate-800 border-white/10'">
                        <img 
                            v-if="member.employee_details?.avatar_secure_url" 
                            :alt="member.employee_details?.user.name" 
                            class="w-full h-full object-cover"
                            :src="member.employee_details.avatar_secure_url" 
                        />
                        <div v-else class="w-full h-full bg-navy-deep flex items-center justify-center">
                            <span class="material-symbols-outlined text-slate-500 text-xl">person</span>
                        </div>
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <p class="text-white font-bold text-sm truncate" :title="member.employee_details?.name || 'Unknown Agent'">
                            {{ member.employee_details?.user.name || 'Unknown Agent' }}
                        </p>
                        <p class="text-[10px] font-mono uppercase truncate"
                           :class="member.match_percentage >= 80 ? 'text-primary' : 'text-slate-400'">
                            {{ member.employee_details?.job_title || 'Pending Data' }}
                        </p>
                    </div>
                </div>

                <div class="space-y-1">
                    <div class="flex justify-between text-[10px] font-mono">
                        <span class="text-slate-400 uppercase">Match Score</span>
                        <span :class="member.match_percentage >= 80 ? 'text-primary font-bold' : 'text-slate-300'">
                            {{ member.match_percentage }}% Fit
                        </span>
                    </div>
                    <div class="h-1 bg-slate-800 rounded-full overflow-hidden">
                        <div class="h-full transition-all duration-1000" 
                             :class="member.match_percentage >= 80 ? 'bg-primary' : 'bg-slate-400'"
                             :style="{ width: `${member.match_percentage}%` }">
                        </div>
                    </div>
                </div>

                <div class="mt-auto pt-2 border-t border-white/5">
                    <p class="text-[9px] text-slate-400 font-mono italic leading-relaxed line-clamp-3" :title="member.reasoning">
                        "{{ member.reasoning }}"
                    </p>
                </div>
            </div>
            
        </div>
    </div>
</template>
<script setup lang="ts">
import api from '@tenant/api/axios';
import { ApiResponse, Employee, SuggestedSquad } from '@tenant/types/api';
import { onMounted, ref } from 'vue';

const props = defineProps<{
    squad: SuggestedSquad[]
}>();
const employees = ref<SuggestedSquad[]>([]);
// Creamos una interfaz extendida para nuestro objeto enriquecido final
interface EnrichedSquadMember extends SuggestedSquad {
    employee_details?: Employee;
    isLoading: boolean;
}
const enrichedSquad = ref<EnrichedSquadMember[]>(
    props.squad.map(member => ({ ...member, isLoading: true }))
);
const loadEmployees = async () => {
    try {
        // Ejecutamos todas las peticiones en paralelo para mayor velocidad
        const requests = enrichedSquad.value.map(member => 
            api.get<ApiResponse<Employee>>(`/employees/${member.employee_id}`)
               .catch(err => {
                   console.error(`Failed to load employee ${member.employee_id}`, err);
                   return null; // Si uno falla, no rompemos el resto
               })
        );

        const responses = await Promise.all(requests);

        // Mapeamos las respuestas de vuelta a nuestro array reactivo
        responses.forEach((response, index) => {
            if (response && response.data.success) {
                enrichedSquad.value[index].employee_details = response.data.content;
            }
            // Independientemente de si falló o no, apagamos el loader
            enrichedSquad.value[index].isLoading = false; 
        });

    } catch (error) {
        console.error("Critical error fetching squad details:", error);
    }
};
onMounted(async () => {
    if (props.squad && props.squad.length > 0) {
        await loadEmployees();
    }
});
</script>