<script setup lang="ts">
import { ref, reactive, computed } from 'vue';
import api from '@tenant/api/axios';
import { ChunkUploader } from '@tenant/services/ChunkUploader'; 
import { useUIStore } from '@tenant/store/ui';
import { ValidationErrors } from '@tenant/types/api';
import Dropzone from '../ui/Dropzone.vue';
import CyberModal from '../ui/CyberModal.vue';
import InputField from '../ui/InputField.vue';

const emit = defineEmits(['specialistInjected']);

const isOpen = ref(false);

const form = reactive({
    name: '',
    email: '',
    job_title: '',
    department: ''
});
const ui = useUIStore();

const avatarFile = ref<File | null>(null);
const avatarPreview = ref<string | null>(null);
const cvFile = ref<File | null>(null);

const isSubmitting = ref(false);
const currentUploadProcess = ref('');
const uploadProgress = ref(0);
const currentUploader = ref<ChunkUploader | null>(null);

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

        currentUploadProcess.value = 'INITIALIZING_OPERATIVE';
        uploadProgress.value = 100;
        currentUploader.value = null;

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
        const response = await api.post('/employees/finalize', finalPayload);

        emit('specialistInjected', response.data.content);
        handleClose();

    } catch (error: any) {
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
