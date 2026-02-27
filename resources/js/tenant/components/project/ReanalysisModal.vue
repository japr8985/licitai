<script setup lang="ts">
import { ref } from 'vue';
import api from '@tenant/api/axios';
import { useUIStore } from '@tenant/store/ui';

// Props: Qué proyecto vamos a reventar 💥
const props = defineProps<{ 
    projectId: string 
}>();

// Emits: Avisar al padre para que refresque la vista
const emit = defineEmits(['reanalysis-started']);

// State
const isOpen = ref(false);
const isLoading = ref(false);
const instructions = ref('');
const ui = useUIStore();

// Actions
const openModal = () => {
    isOpen.value = true;
    document.body.style.overflow = 'hidden'; // Bloquear scroll del body
};

const closeModal = () => {
    if (isLoading.value) return; // No cerrar si está trabajando
    isOpen.value = false;
    document.body.style.overflow = ''; // Restaurar scroll
    instructions.value = ''; // Limpiar input
};

const handleExecute = async () => {
    if (!instructions.value.trim()) {
        ui.notify('warning', 'Please provide some instructions for the AI.', 'MISSING INPUT');
        return;
    }

    isLoading.value = true;

    try {
        // 1. Enviamos la petición al Backend
        await api.post(`/projects/${props.projectId}/reanalyze`, {
            additional_instructions: instructions.value
        });

        // 2. Feedback de éxito
        ui.notify('success', 'Neural Engine restarted. Analysis in progress.', 'SYSTEM UPDATE');
        
        // 3. Avisamos al padre (Show.vue) para que actualice la vista
        emit('reanalysis-started');
        
        closeModal();

    } catch (error: any) {
        console.error("Reanalysis Error:", error);
        
        // Feedback de error usando el mensaje del backend si existe
        const msg = error.response?.data?.message || 'Connection to Neural Engine failed.';
        ui.notify('error', msg, 'PROTOCOL ERROR');
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <button 
        @click="openModal"
        class="group flex items-center gap-2 px-4 py-2 border border-amber-500/50 text-amber-500 rounded-lg font-bold text-sm hover:bg-amber-500/10 hover:border-amber-500 transition-all duration-300 shadow-[0_0_10px_rgba(245,158,11,0.1)] hover:shadow-[0_0_15px_rgba(245,158,11,0.3)]"
    >
        <span class="material-symbols-outlined text-sm group-hover:rotate-180 transition-transform duration-700">autorenew</span>
        <span>Re-run Analysis</span>
    </button>

    <Teleport to="body">
        <Transition name="modal-fade">
            <div v-if="isOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                
                <div class="absolute inset-0 bg-deep-navy/80 backdrop-blur-sm" @click="closeModal"></div>

                <div class="glass-modal relative w-full max-w-lg rounded-xl overflow-hidden shadow-2xl transform transition-all">
                    
                    <div class="px-6 py-4 border-b border-amber-500/20 flex justify-between items-center bg-deep-navy/50">
                        <div class="flex items-center gap-3">
                            <div class="relative flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-500 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                            </div>
                            <h2 class="text-lg font-bold text-slate-100 tracking-tight uppercase">Refine Neural Analysis</h2>
                        </div>
                        <button @click="closeModal" class="text-slate-400 hover:text-white transition-colors">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <div class="p-6 space-y-4">
                        <p class="text-sm text-slate-400 leading-relaxed font-mono">
                            > Provide additional context parameters to recalibrate the AI processing engine.
                        </p>
                        
                        <div class="space-y-2">
                            <label class="block text-[10px] font-mono uppercase tracking-widest text-amber-500/80">
                                Additional Instructions
                            </label>
                            <textarea 
                                v-model="instructions"
                                :disabled="isLoading"
                                class="w-full bg-black/40 border border-slate-700 rounded-lg p-4 text-slate-200 text-sm font-sans focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 outline-none transition-all placeholder:text-slate-600 resize-none custom-scrollbar"
                                placeholder="E.g., Prioritize risk assessment in Clause 4.2 or analyze against 2024 compliance updates..."
                                rows="5"
                            ></textarea>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-black/20 flex justify-end gap-3 border-t border-white/5">
                        <button 
                            @click="closeModal" 
                            :disabled="isLoading"
                            class="px-4 py-2 text-xs font-bold uppercase tracking-wider text-slate-500 hover:text-white transition-colors disabled:opacity-50"
                        >
                            Cancel
                        </button>
                        
                        <button 
                            @click="handleExecute"
                            :disabled="isLoading"
                            class="group relative px-6 py-2 bg-amber-500 hover:bg-amber-400 text-deep-navy font-bold rounded text-xs uppercase tracking-wider shadow-lg shadow-amber-500/20 transition-all active:scale-95 disabled:opacity-70 disabled:cursor-not-allowed flex items-center gap-2 overflow-hidden"
                        >
                            <span v-if="isLoading" class="material-symbols-outlined text-sm animate-spin">progress_activity</span>
                            <span v-else class="material-symbols-outlined text-sm group-hover:rotate-12 transition-transform">rocket_launch</span>
                            
                            <span>{{ isLoading ? 'Processing...' : 'Execute Refinement' }}</span>

                            <div v-if="!isLoading" class="absolute inset-0 bg-white/20 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700 skew-x-12"></div>
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
/* Glassmorphism Custom Class */
.glass-modal {
    background: rgba(10, 25, 47, 0.95);
    border: 1px solid rgba(245, 158, 11, 0.2);
    box-shadow: 0 0 50px rgba(0, 0, 0, 0.8), 0 0 15px rgba(245, 158, 11, 0.1);
}

/* Scrollbar para el textarea */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(0,0,0,0.2);
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(245, 158, 11, 0.3);
    border-radius: 2px;
}

/* Animaciones de Entrada/Salida del Modal */
.modal-fade-enter-active,
.modal-fade-leave-active {
    transition: all 0.3s ease;
}
.modal-fade-enter-from,
.modal-fade-leave-to {
    opacity: 0;
}
.modal-fade-enter-from .glass-modal,
.modal-fade-leave-to .glass-modal {
    transform: scale(0.95) translateY(10px);
    opacity: 0;
}
</style>