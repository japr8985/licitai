<script setup lang="ts">
import { ref, reactive } from 'vue';
import api from '@tenant/api/axios';
import { ApiResponse } from '@tenant/types/api';
import { ChunkUploader, UploadResult } from '@tenant/services/ChunkUploader';

const props = defineProps<{ isOpen: boolean }>();
const emit = defineEmits(['close', 'projectCreated']);

const activeTab = ref<'structured' | 'agent'>('structured');
const isUploading = ref(false);
const fileInput = ref<HTMLInputElement | null>(null);
const selectedFileName = ref<string | null>(null);
const currentUploader = ref<ChunkUploader | null>(null);
const uploadProgress = ref(0);
const selectedFile = ref<File | null>(null);
const form = reactive({
    title: 'Implementación de una Infraestructura de Nube Híbrida y Sistema de Gestión de Identidades Distribuidas',
    issuing_entity: 'MINISTERIO DE MODERNIZACIÓN TECNOLÓGICA',
    valuation: "1250000"
});

const handleFileUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    
    if (!file) return;

    selectedFile.value = file;
    selectedFileName.value = file.name;
    
    console.log("PDF asegurado en memoria:", file.name);
};

const triggerFileInput = () => {
    fileInput.value?.click();
};

const establishProject = async () => {
    const formData = new FormData();
    formData.append('title', form.title);
    formData.append('issuing_entity', form.issuing_entity);
    formData.append('valuation', form.valuation);

    try {
        isUploading.value = true;
        
        // 1. Iniciar subida
        const uploader = new ChunkUploader(selectedFile.value!, {
            onProgress: (p) => uploadProgress.value = p
        });
        
        // 2. Esperar resultado
        // Si start() falla, irá directo al catch. 
        // Si tiene éxito, 'result' tendrá los datos.
        const result = await uploader.start(); 

        // 3. Verificación de seguridad antes de seguir
        if (!result || !result.upload_uuid) {
            throw new Error("La subida terminó pero no devolvió un UUID válido.");
        }

        const finalPayload = {
            title: form.title,
            issuing_entity: form.issuing_entity,
            valuation: form.valuation,
            upload_uuid: result.upload_uuid, // Asegúrate que el servicio devuelva esto
            file_name: result.file_name,
            total_chunks: result.total_chunks
        };

        const response = await api.post('/projects/finalize', finalPayload);

        emit('projectCreated', response.data.content);
        handleClose(); 

    } catch (error: any) {
        const errorMessage = error instanceof Error ? error.message : "Internal Neural Link Failure";
        // alert(`ERROR: ${errorMessage}`);
    } finally {
        isUploading.value = false;
        currentUploader.value = null;
    }
};
const handleClose = () => {
    if (isUploading.value && currentUploader.value) {
        currentUploader.value.abort();
    }
    form.title = '';
    form.issuing_entity = '';
    form.valuation = '';
    selectedFile.value = null;     
    selectedFileName.value = null;
    emit('close');
}
</script>

<template>
    <Transition name="neural">
        <div v-if="isOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            
            <div 
                class="absolute inset-0 bg-navy-deep/80 backdrop-blur-sm cursor-pointer" 
                @click="emit('close')"
            ></div>

            <div class="glass-panel w-full max-w-4xl rounded-2xl overflow-hidden shadow-2xl flex flex-col max-h-[90vh] relative z-10">

                <div class="flex items-center justify-between border-b border-white/10 px-8 bg-navy-light/60 shrink-0">
                    <div class="flex gap-8">
                        <button @click="activeTab = 'structured'"
                            :class="['py-5 text-[11px] font-mono tracking-[0.2em] border-b-2 transition-all outline-none',
                                activeTab === 'structured' ? 'border-cyber-teal text-cyber-teal font-bold' : 'border-transparent text-slate-tech hover:text-white']">
                            STRUCTURED_INGEST
                        </button>
                        <button @click="activeTab = 'agent'"
                            :class="['py-5 text-[11px] font-mono tracking-[0.2em] border-b-2 transition-all outline-none',
                                activeTab === 'agent' ? 'border-cyber-teal text-cyber-teal font-bold' : 'border-transparent text-slate-tech hover:text-white']">
                            AGENT_DIALOGUE
                        </button>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-mono text-emerald-accent uppercase">Workbench_Active</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-accent animate-pulse"></span>
                    </div>
                </div>

                <div class="flex-grow overflow-y-auto p-8 custom-scrollbar">
                    
                    <div v-if="activeTab === 'structured'" class="grid grid-cols-1 md:grid-cols-2 gap-8 h-full">
                        <div class="space-y-6">
                            <div class="space-y-2">
                                <label class="block text-[10px] font-mono text-slate-tech tracking-widest uppercase">Project Title</label>
                                <input v-model="form.title" class="input-cyber" placeholder="e.g. Quantum Mesh Expansion" type="text" />
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-mono text-slate-tech tracking-widest uppercase">Issuing Entity</label>
                                <input v-model="form.issuing_entity" class="input-cyber" placeholder="e.g. Global Defense Command" type="text" />
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-mono text-slate-tech tracking-widest uppercase">Estimated Valuation</label>
                                <div class="relative group">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-cyber-teal font-mono text-sm pointer-events-none transition-colors group-focus-within:text-white">$</span>
                                    <input v-model="form.valuation" class="input-cyber !pl-10 font-mono" placeholder="0.00" type="text" />
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col h-full">
                            <label class="block text-[10px] font-mono text-slate-tech tracking-widest uppercase mb-2">Neural Dropzone</label>
                            <div class="neural-dropzone flex-grow group cursor-pointer relative" @click="triggerFileInput">
                                <input type="file" ref="fileInput" class="hidden" @change="handleFileUpload" accept=".pdf" />
                                <template v-if="!selectedFileName">
                                    <span class="material-symbols-outlined text-4xl text-cyber-teal mb-3 group-hover:scale-110 transition-transform">upload_file</span>
                                    <div class="text-sm font-semibold text-white mb-1">Inject PDF Data</div>
                                    <div class="text-[10px] font-mono text-slate-tech uppercase">Drag & Drop or Click to Browse</div>
                                    <div class="mt-6 text-[9px] font-mono text-cyber-teal bg-cyber-teal/10 px-3 py-1 rounded border border-cyber-teal/20">MAX_PAYLOAD: 128MB</div>
                                </template>
                                <template v-else>
                                    <span class="material-symbols-outlined text-4xl text-emerald-accent mb-3">description</span>
                                    <div class="text-sm font-semibold text-white mb-1 break-all px-4 text-center">{{ selectedFileName }}</div>
                                    <div class="text-[10px] font-mono text-emerald-accent uppercase mt-2">READY FOR PARSING</div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div v-if="activeTab === 'agent'" class="h-full flex flex-col min-h-[400px]">
                        <div class="flex-grow bg-navy-deep/40 rounded border border-white/5 p-4 mb-4 font-mono text-[11px] text-slate-tech overflow-y-auto custom-scrollbar space-y-3">
                            <div class="flex gap-3">
                                <div class="w-6 h-6 rounded bg-cyber-teal/20 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-[14px] text-cyber-teal">smart_toy</span>
                                </div>
                                <div class="bg-navy-light/80 p-3 rounded-tr-lg rounded-br-lg rounded-bl-lg text-slate-300">
                                    <p class="mb-1 text-cyber-teal text-[10px]">NEURAL_AGENT_V1</p>
                                    <p>Awaiting project specs... Envía un mensaje para comenzar a estructurar la licitación vía diálogo.</p>
                                </div>
                            </div>
                        </div>
                        <div class="relative mt-auto">
                            <input type="text" class="input-cyber pr-12 font-mono" placeholder="TYPE_COMMAND_HERE...">
                            <button class="absolute right-3 top-1/2 -translate-y-1/2 text-cyber-teal hover:text-white transition-colors">
                                <span class="material-symbols-outlined text-lg">send</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="p-8 border-t border-white/10 bg-navy-light/40 flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-4 text-[10px] font-mono text-slate-tech">
                        <div class="flex items-center gap-1.5">
                            <span :class="['w-2 h-2 rounded-full transition-colors', selectedFileName ? 'bg-emerald-accent' : 'bg-slate-600']"></span>
                            {{ selectedFileName ? 'READY' : 'IDLE' }}
                        </div>
                        <div class="flex items-center gap-1.5" v-if="isUploading">
                            <span class="w-2 h-2 rounded-full bg-cyber-teal animate-pulse"></span>
                            UPLOADING...
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <button @click="emit('close')" class="px-6 py-3 rounded text-[11px] font-mono font-bold tracking-widest text-slate-tech hover:text-white transition-colors uppercase">
                            Abort_Ingest
                        </button>
                        <button @click="establishProject" :disabled="isUploading" class="px-8 py-3 bg-cyber-teal rounded text-[11px] font-mono font-bold tracking-widest text-navy-deep hover:bg-white transition-all shadow-lg shadow-cyber-teal/20 uppercase disabled:opacity-50 disabled:cursor-not-allowed">
                            {{ isUploading ? 'Processing...' : 'Establish_Project' }}
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </Transition>
</template>

<style scoped>
@reference "../../../css/app.css";

/* Estilos Base */
.glass-panel {
    @apply bg-navy-light/40 backdrop-blur-xl border border-white/10;
}
.input-cyber {
    @apply w-full bg-navy-deep/60 border border-white/10 rounded-md px-4 py-3 text-sm text-white transition-all outline-none;
}
.input-cyber:focus {
    @apply border-cyber-teal/50 ring-1 ring-cyber-teal/50;
}
.neural-dropzone {
    @apply border-2 border-dashed border-cyber-teal/30 bg-cyber-teal/5 flex flex-col items-center justify-center p-8 rounded-lg transition-all;
    box-shadow: inset 0 0 20px rgba(13, 242, 242, 0.05);
}
.neural-dropzone:hover {
    @apply border-cyber-teal/60 bg-cyber-teal/10;
    box-shadow: 0 0 15px rgba(13, 242, 242, 0.2);
}

/* Scrollbar */
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: rgba(10, 25, 47, 0.5); }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(136, 146, 176, 0.2); border-radius: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(13, 242, 242, 0.4); }

/* --- ANIMACIÓN NEURAL --- */
.neural-enter-active,
.neural-leave-active {
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

/* Animamos el backdrop (que ahora es un div hijo con clase .absolute) */
.neural-enter-active .absolute,
.neural-leave-active .absolute {
    transition: opacity 0.3s ease;
}

/* Animamos el panel (que ahora es un div hijo con clase .glass-panel) */
.neural-enter-active .glass-panel,
.neural-leave-active .glass-panel {
    transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
}

/* Estados Inicial y Final */
.neural-enter-from,
.neural-leave-to {
    opacity: 0;
}

/* Backdrop Fade */
.neural-enter-from .absolute,
.neural-leave-to .absolute {
    opacity: 0;
}

/* Panel Pop & Blur */
.neural-enter-from .glass-panel,
.neural-leave-to .glass-panel {
    opacity: 0;
    transform: scale(0.95) translateY(10px);
    filter: blur(8px);
}
</style>