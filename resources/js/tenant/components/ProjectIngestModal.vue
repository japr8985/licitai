<script setup lang="ts">
import { ref, reactive } from 'vue';
import api from '@tenant/api/axios';
import { ApiResponse, ValidationErrors } from '@tenant/types/api';
import { ChunkUploader, UploadResult } from '@tenant/services/ChunkUploader';
import axios, { AxiosError } from 'axios';
import { useUIStore, Toast } from '@tenant/store/ui';
import Dropzone from './ui/Dropzone.vue';
import InputField from './ui/InputField.vue';
import NumberInput from './ui/NumberInput.vue';
import RadioGroup from './ui/RadioGroup.vue';
import CyberChatEngine, { ChatMessage } from './ui/CyberChatEngine.vue';
import CyberModal from './ui/CyberModal.vue';
import CyberChatInput from './CyberChatEngine/CyberChatInput.vue';
const props = defineProps<{ isOpen: boolean }>();
const emit = defineEmits(['close', 'projectCreated']);
const ui = useUIStore();
const activeTab = ref<'structured' | 'agent'>('structured');
const isUploading = ref(false);
const fileInput = ref<HTMLInputElement | null>(null);
const selectedFileName = ref<string | null>(null);
const currentUploader = ref<ChunkUploader | null>(null);
const uploadProgress = ref(0);
const selectedFile = ref<File | null>(null);
const form = reactive({
    title: '',
    issuing_entity: '',
    valuation: 0
});

const formErrors = ref<ValidationErrors>({})

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
    if (!selectedFile.value) return; // Validación simple

    const formData = new FormData();
    formData.append('title', form.title);
    formData.append('issuing_entity', form.issuing_entity);
    formData.append('valuation', form.valuation);

    try {
        isUploading.value = true;
        uploadProgress.value = 0;

        // 1. Iniciar subida
        const uploader = new ChunkUploader(selectedFile.value!, {
            onProgress: (p) => uploadProgress.value = p
        });
        currentUploader.value = uploader;

        // 2. Esperar resultado
        const result = await uploader.start();

        if (!result || !result.upload_uuid) {
            throw new Error("La subida terminó pero no devolvió un UUID válido.");
        }

        const finalPayload = {
            title: form.title,
            issuing_entity: form.issuing_entity,
            valuation: form.valuation,
            upload_uuid: result.upload_uuid,
            file_name: result.file_name,
            total_chunks: result.total_chunks
        };

        const response = await api.post('/projects/finalize', finalPayload);

        emit('projectCreated', response.data.content);
        handleClose();
        ui.notify('info', 'Project procesing...', 'Procesing')

    } catch (error: any) {
        if (error?.status) {
            if (error.status === 422) {

                const errorData = error.data as ApiResponse<ValidationErrors>;
                formErrors.value = errorData.content;
                ui.notify('error', 'Invalid data', errorData.message);
                return;
            }
            ui.notify('error', 'System Error', error.response?.data?.message || 'Unexpected error occurred.');
        } else {
            console.error(error)
        }
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
    uploadProgress.value = 0;
    emit('close');
}

const onFileSelected = (file: File) => {
    selectedFile.value = file;
    selectedFileName.value = file.name
};
//
const isAiThinking = ref(false);
const chatHistory = ref<ChatMessage[]>([
    {
        id: 1,
        role: 'agent',
        type: 'text',
        content: 'Greetings, Operative. Secure link verified. I am ready to structure your internal project. Describe the parameters.'
    }
]);
// 2. Lógica para enviar mensajes (Simulando a n8n)
const handleNewCommand = async (command: string) => {
    // A) Agregamos lo que el usuario escribió
    chatHistory.value.push({
        id: Date.now(),
        role: 'user',
        type: 'text',
        content: command
    });

    // B) Encendemos la animación de "Procesando..." y bloqueamos el input
    isAiThinking.value = true;

    // C) Simulamos el tiempo que tarda Laravel/n8n en responder (ej. 2 segundos)
    setTimeout(() => {
        isAiThinking.value = false;

        // D) Respondemos con un panel de datos estructurados
        chatHistory.value.push({
            id: Date.now() + 1,
            role: 'agent',
            type: 'structured',
            content: {
                viability: '88.5%',
                budget: '$250,000',
                deadline: 'Q3 2026'
            }
        });
    }, 2000);
};
</script>
<template>
    <CyberModal 
        :open="isOpen" 
        @update:open="val => { if (!val) handleClose() }"
        maxWidth="4xl"
        :persistent="isUploading"
    >
        
        <template #header>
            <div class="w-full flex flex-col md:flex-row items-end justify-between border-b border-cyber-teal/20 pb-0 gap-4">
                
                <div class="flex gap-1">
                    <button @click="activeTab = 'structured'"
                        class="py-4 px-4 text-[11px] font-mono tracking-[0.2em] border-b-2 transition-all group outline-none"
                        :class="activeTab === 'structured' ? 'border-cyber-teal text-cyber-teal font-bold bg-cyber-teal/5 shadow-[0_-10px_20px_rgba(13,242,242,0.1)]' : 'border-transparent text-slate-tech hover:text-white hover:bg-white/5'">
                        STRUCTURED_INGEST
                    </button>
                    <button @click="activeTab = 'agent'"
                        class="py-4 px-4 text-[11px] font-mono tracking-[0.2em] border-b-2 transition-all outline-none"
                        :class="activeTab === 'agent' ? 'border-cyber-teal text-cyber-teal font-bold bg-cyber-teal/5 shadow-[0_-10px_20px_rgba(13,242,242,0.1)]' : 'border-transparent text-slate-tech hover:text-white hover:bg-white/5'">
                        AGENT_DIALOGUE
                    </button>
                </div>
                
                <div class="flex items-center gap-3 bg-navy-deep/50 px-3 py-1.5 rounded border border-white/5 mb-2">
                    <span class="text-[10px] font-mono text-emerald-accent uppercase crt-flicker">Workbench_Active</span>
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-accent opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-accent"></span>
                    </span>
                </div>
            </div>
        </template>

        <div class="relative w-full h-full min-h-[350px]">
            <div class="absolute inset-0 tech-grid-bg opacity-10 pointer-events-none"></div>

            <div v-if="activeTab === 'structured'" class="grid grid-cols-1 md:grid-cols-2 gap-10 relative z-10 py-2">
                <div class="space-y-4">
                    <InputField 
                        label="Project Title" 
                        placeholder="e.g. QUANTUM_MESH_EXPANSION" 
                        v-model="form.title"
                        :error="formErrors.title" 
                    />
                    <InputField 
                        label="Issuing Entity" 
                        placeholder="e.g. GLOBAL_DEFENSE_CMD"
                        v-model="form.issuing_entity" 
                        :error="formErrors.issuing_entity" 
                    />
                    <NumberInput 
                        v-model="form.valuation" 
                        label="Estimated Valuation" 
                        prefix="$" 
                        placeholder="0.00"
                        :error="formErrors.valuation" 
                    />
                </div>

                <div class="flex flex-col h-full min-h-[200px]">
                    <Dropzone 
                        variant="rectangular" 
                        accept=".pdf" 
                        icon="upload_file" 
                        title="INJECT PDF DATA"
                        subtitle="Drag & Drop Sequence" 
                        limitText="MAX_PAYLOAD: 128MB"
                        activeText="READY FOR PARSING" 
                        :file="selectedFile" 
                        @file-selected="onFileSelected"
                        :pulsing="true" 
                    />
                </div>
            </div>

            <div v-else-if="activeTab === 'agent'" class="h-[500px] w-full relative z-10">
                <CyberChatEngine
                    :messages="chatHistory" 
                    :isProcessing="isAiThinking"
                    @send-message="handleNewCommand"
                />
            </div>
        </div>

        <template #footer>
            <div v-if="activeTab === 'structured'" class="w-full flex flex-wrap items-center justify-between gap-4">
                
                <div class="flex flex-col gap-1 w-full md:w-auto flex-1 max-w-xs">
                    <div class="flex items-center gap-4 text-[10px] font-mono text-slate-tech">
                        <div class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-sm shadow-[0_0_8px_#0df2f2]"
                                :class="selectedFileName ? 'bg-cyber-teal' : 'bg-slate-600'"></span>
                            {{ selectedFileName ? 'PAYLOAD_SECURED' : 'AWAITING_DATA' }}
                        </div>
                        <div v-if="isUploading" class="h-3 w-[1px] bg-slate-700"></div>
                        <div v-if="isUploading" class="flex items-center gap-2 text-cyber-teal">
                            <span class="material-symbols-outlined text-[12px] animate-spin">sync</span>
                            UPLOADING_{{ uploadProgress }}%
                        </div>
                    </div>
                    <div class="w-full h-[2px] bg-navy-deep mt-2 relative overflow-hidden rounded-full">
                        <div class="absolute inset-y-0 left-0 bg-cyber-teal shadow-[0_0_8px_#0df2f2] transition-all duration-300"
                            :style="{ width: uploadProgress + '%' }"></div>
                    </div>
                </div>

                <div class="flex gap-4 w-full md:w-auto">
                    <button @click="handleClose"
                        class="px-6 py-3 rounded-none border border-transparent hover:border-red-500/50 text-[11px] font-mono font-bold tracking-widest text-slate-tech hover:text-red-400 hover:bg-red-500/5 transition-all uppercase">
                        Abort_Ingest
                    </button>

                    <button @click="establishProject" :disabled="isUploading || !selectedFileName"
                        class="relative px-8 py-3 bg-cyber-teal text-black text-[11px] font-mono font-bold tracking-widest uppercase hover:bg-white hover:shadow-[0_0_20px_rgba(255,255,255,0.4)] transition-all duration-300 group overflow-hidden disabled:opacity-50 disabled:cursor-not-allowed">
                        <span class="relative z-10">{{ isUploading ? 'UPLOADING...' : 'Establish_Project' }}</span>
                        <div v-if="!isUploading && selectedFileName"
                            class="absolute inset-0 bg-white/20 -translate-x-full group-hover:skew-x-12 group-hover:translate-x-full transition-transform duration-700">
                        </div>
                    </button>
                </div>
            </div>
            <div v-if="activeTab === 'agent'" class="w-full">
                <CyberChatInput />
            </div>
        </template>
        
    </CyberModal>
</template>