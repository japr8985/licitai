<script setup lang="ts">
import { ref, reactive, computed } from 'vue';
import api from '@tenant/api/axios';
import { ChunkUploader } from '@tenant/services/ChunkUploader'; // <-- Importamos tu servicio
import { useUIStore } from '@tenant/store/ui';
import { ApiResponse, ValidationErrors } from '@tenant/types/api';
import Dropzone from '../ui/Dropzone.vue';
import CyberModal from '../ui/CyberModal.vue';
import InputField from '../ui/InputField.vue';

const emit = defineEmits(['specialistInjected']);

const isOpen = ref(false);
const openModal = () => { isOpen.value = true; };

// --- ESTADO DEL FORMULARIO ---
const form = reactive({
    name: '',
    email: '',
    job_title: '',
    department: ''
});
const ui = useUIStore();
// --- ESTADO DE ARCHIVOS ---
const avatarFile = ref<File | null>(null);
const avatarPreview = ref<string | null>(null);
const cvFile = ref<File | null>(null);

const avatarInput = ref<HTMLInputElement | null>(null);
const cvInput = ref<HTMLInputElement | null>(null);

// Nuevas variables para el control de subida
const isSubmitting = ref(false);
const currentUploadProcess = ref('');
const uploadProgress = ref(0);
const currentUploader = ref<ChunkUploader | null>(null);

// --- HANDLERS DE ARCHIVOS ---
const triggerAvatarInput = () => avatarInput.value?.click();
const triggerCvInput = () => cvInput.value?.click();

const handleAvatarUpload = (file: File) => {
    if (!file) return;
    avatarFile.value = file
    avatarPreview.value = URL.createObjectURL(file)
};

const handleCvUpload = (file: File) => {
    if (!file) return;
    cvFile.value = file;
};

// --- ESTADO DINÁMICO DEL SISTEMA ---
const systemStatus = computed(() => {
    if (isSubmitting.value) {
        return `${currentUploadProcess.value} [${uploadProgress.value}%]`;
    }
    if (avatarFile.value && cvFile.value) return 'ALL_SYSTEMS_GO // READY_TO_REGISTER';
    if (avatarFile.value) return 'AWAITING_NEURAL_PAYLOAD_(CV)';
    if (cvFile.value) return 'AWAITING_BIOMETRICS_(AVATAR)';
    return 'SYSTEM_READY // AWAITING_INPUT';
});
const formErrors = ref<ValidationErrors>({})
// --- SUBMIT (AHORA CON CHUNKS) ---
const registerOperative = async () => {
    // if (!form.name || !form.email || !form.job_title) {
    //     alert("CRITICAL ERROR: Core identity fields missing.");
    //     return;
    // }

    try {
        isSubmitting.value = true;
        let avatarData = null;
        let cvData = null;

        // 1. Subir Avatar por Chunks
        if (avatarFile.value) {
            currentUploadProcess.value = 'UPLOADING_BIOMETRICS';
            uploadProgress.value = 0;

            const avatarUploader = new ChunkUploader(avatarFile.value, {
                onProgress: (p) => uploadProgress.value = p
            });
            currentUploader.value = avatarUploader;
            avatarData = await avatarUploader.start();
        }

        // 2. Subir CV por Chunks
        if (cvFile.value) {
            currentUploadProcess.value = 'UPLOADING_NEURAL_PAYLOAD';
            uploadProgress.value = 0;

            const cvUploader = new ChunkUploader(cvFile.value, {
                onProgress: (p) => uploadProgress.value = p
            });
            currentUploader.value = cvUploader;
            cvData = await cvUploader.start();
        }

        // 3. Finalizar Ingesta
        currentUploadProcess.value = 'INITIALIZING_OPERATIVE';
        uploadProgress.value = 100;
        currentUploader.value = null;

        // Ya no usamos FormData, enviamos un JSON limpio con los UUIDs
        const finalPayload = {
            name: form.name,
            email: form.email,
            job_title: form.job_title,
            department: form.department,
            avatar: {
                upload_uuid: avatarData?.upload_uuid,
                file_name: avatarData?.file_name,
                total_chunks: avatarData?.total_chunks
            },
            cv: {
                upload_uuid: cvData?.upload_uuid,
                file_name: cvData?.file_name,
                total_chunks: cvData?.total_chunks
            }
        };

        // NOTA: Ajusta esta ruta a tu endpoint de finalización en el backend
        const response = await api.post('/employees/finalize', finalPayload);

        emit('specialistInjected', response.data.content);
        handleClose();

    } catch (error: any) {
        // console.error("System Failure:", error);
        // alert("SYSTEM FAILURE: Unable to register operative. Link aborted.");
        if (error?.status) {
            if (error.status === 422) {
                const errorData = error.data
                formErrors.value = errorData.errors;
                ui.notify('error', errorData.message)
                return;
            }
            ui.notify('error', 'System Error', error.response?.data?.message || 'Unexpected error occurred.');
        }

    } finally {
        isSubmitting.value = false;
        currentUploader.value = null;
    }
};

const handleClose = () => {
    if (isSubmitting.value && currentUploader.value) {
        currentUploader.value.abort();
    }
    form.name = '';
    form.email = '';
    form.job_title = '';
    form.department = '';
    avatarFile.value = null;
    cvFile.value = null;
    uploadProgress.value = 0;
    if (avatarPreview.value) {
        URL.revokeObjectURL(avatarPreview.value);
        avatarPreview.value = null;
    }
    isOpen.value = false;
};
</script>
<template>
    <CyberModal title="INITIALIZE_SPECIALIST" subtitle="LicitAI Secure Terminal v4.0" status-text="status-text" max-width="6xl">
        <template #trigger="{ open }">
            <button @click="open"
                class="w-full sm:w-auto px-6 py-2.5 bg-[#0df2f2] text-[#0a192f] font-bold uppercase tracking-widest shadow-[0_0_15px_rgba(13,242,242,0.3)] hover:shadow-[0_0_25px_rgba(13,242,242,0.6)] transition-all duration-300 transform hover:-translate-y-1 font-mono text-xs border-2 border-transparent hover:border-white">
                Inject Specialist
            </button>
        </template>
        <form @submit.prevent="registerOperative" class="flex flex-col lg:flex-row gap-10">
            <div class="flex-1 flex flex-col gap-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <InputField v-model="form.name" label="Full Legal Name" label-icon="badge"
                        placeholder="Enter operative name" :error="formErrors.name"/>
                    <InputField v-model="form.email" label="Email" label-icon="email"
                        placeholder="Secure frequency email" :error="formErrors.email"/>
                    <InputField v-model="form.job_title" label="Designation (Title)" label-icon="military_tech"
                        placeholder="e.g. Netrunner" :error="formErrors.job_title"/>
                    <InputField v-model="form.department" label="Sector (Department)" label-icon="location_city"
                        placeholder="e.g. Developer" :error="formErrors.department"/>
                </div>
                
            </div>
            <div class="w-full lg:w-[350px] shrink-0 flex flex-col gap-6">
                <Dropzone variant="circular" accept="image/*" icon="face_5" title="Biometric Profile"
                    subtitle="Click to scan face" activeText="BIOMETRICS_LOADED" :file="avatarFile"
                    :previewUrl="avatarPreview" @file-selected="handleAvatarUpload" />
                <Dropzone variant="rectangular" accept=".pdf" icon="memory" title="Neural Payload (CV)"
                    subtitle="Upload encrypted resume packet" activeText="PAYLOAD_SECURED" :file="cvFile"
                    @file-selected="handleCvUpload" />
            </div>
        </form>

        <template #footer="{ close }">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-amber-400 text-lg"
                    :class="{ 'animate-pulse': isSubmitting }">
                    {{ isSubmitting ? 'sync' : 'warning' }}
                </span>
                <div class="flex flex-col">
                    <span class="text-[10px] font-mono text-slate-400">STATUS_CHECK</span>
                    <span class="text-xs font-bold font-mono tracking-wider"
                        :class="isSubmitting ? 'text-[#0df2f2]' : 'text-amber-400'">
                        {{ systemStatus }}
                    </span>
                </div>
            </div>
            <div class="flex items-right gap-3">
                <button @click="close" type="button"
                class="flex-1 md:flex-none px-6 py-3 rounded text-slate-300 font-mono text-[11px] border border-transparent hover:bg-red-500/10 hover:text-red-400 hover:border-red-500/30 transition-all uppercase tracking-widest">
                Abort_Sequence
            </button>
            <button @click="registerOperative" :disabled="isSubmitting" type="submit"
                class="flex-1 md:flex-none relative group/btn overflow-hidden px-8 py-3 bg-[#0df2f2]/10 text-[#0df2f2] font-bold text-[11px] font-mono rounded border border-[#0df2f2] hover:bg-[#0df2f2] hover:text-[#0a192f] transition-all uppercase tracking-widest shadow-[0_0_15px_rgba(13,242,242,0.2)] hover:shadow-[0_0_30px_rgba(13,242,242,0.6)] disabled:opacity-50 disabled:cursor-not-allowed">
                <span class="relative z-10 flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-base">fingerprint</span>
                    {{ isSubmitting ? 'INITIALIZING...' : 'Register_Operative' }}
                </span>
            </button>
            </div>
        </template>
    </CyberModal>
</template>

<style scoped>
@reference "../../../../css/app.css";

/* Base Inputs */
.input-cyber {
    @apply w-full bg-[#0a192f]/50 border-0 border-b-2 border-[#0df2f2]/30 text-white placeholder-slate-500 px-4 py-3 transition-all duration-300 outline-none;
}

.input-cyber:focus {
    @apply border-[#0df2f2] bg-[#112240]/80 shadow-[0_4px_15px_-3px_rgba(13,242,242,0.1)];
}

/* Decoraciones y Backgrounds */
.cyber-grid {
    background-image:
        linear-gradient(rgba(13, 242, 242, 0.05) 1px, transparent 1px),
        linear-gradient(90deg, rgba(13, 242, 242, 0.05) 1px, transparent 1px);
}

.scanline-overlay {
    background: linear-gradient(to bottom, rgba(255, 255, 255, 0), rgba(255, 255, 255, 0) 50%, rgba(0, 0, 0, 0.2) 50%, rgba(0, 0, 0, 0.2));
    background-size: 100% 4px;
}

/* Esquinas Cyborg */
.cyber-corner {
    @apply absolute w-5 h-5 border-2 border-[#0df2f2] transition-all duration-300 pointer-events-none;
}

.cyber-corner-tl {
    top: 0;
    left: 0;
    border-right: none;
    border-bottom: none;
}

.cyber-corner-tr {
    top: 0;
    right: 0;
    border-left: none;
    border-bottom: none;
}

.cyber-corner-bl {
    bottom: 0;
    left: 0;
    border-right: none;
    border-top: none;
}

.cyber-corner-br {
    bottom: 0;
    right: 0;
    border-left: none;
    border-top: none;
}

.glass-panel:hover .cyber-corner {
    width: 30px;
    height: 30px;
    box-shadow: 0 0 10px rgba(13, 242, 242, 0.5);
}

/* Animaciones */
.cursor-blink::after {
    content: '_';
    animation: blink 1s step-end infinite;
}

@keyframes blink {
    50% {
        opacity: 0;
    }
}

/* --- VUE TRANSITION: HOLO-MATERIALIZE --- */
.holo-enter-active .glass-panel {
    animation: holoMaterialize 0.35s cubic-bezier(0.1, 0.9, 0.2, 1) forwards;
}

.holo-leave-active .glass-panel {
    transition: all 0.15s ease-in;
    opacity: 0;
    transform: scale(0.95) translateY(10px);
}

.holo-enter-active .absolute,
.holo-leave-active .absolute {
    transition: opacity 0.2s ease-out;
}

.holo-enter-from .absolute,
.holo-leave-to .absolute {
    opacity: 0;
}

@keyframes holoMaterialize {
    0% {
        opacity: 0;
        transform: scale(0.9) translateY(20px);
        filter: blur(10px) brightness(3);
    }

    60% {
        opacity: 1;
        filter: blur(2px) brightness(1.5);
        transform: scale(1.02);
    }

    100% {
        opacity: 1;
        transform: scale(1) translateY(0);
        filter: blur(0) brightness(1);
    }
}
</style>