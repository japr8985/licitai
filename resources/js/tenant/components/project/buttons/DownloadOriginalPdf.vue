<template>
    <button
        class="flex items-center gap-2 px-4 py-2 rounded-lg border border-white/10 bg-white/5 hover:bg-white/10 transition-all text-sm font-bold text-slate-300 disabled:opacity-50 disabled:cursor-not-allowed group"
        @click="handleDownload"
        :disabled="isDownloading"
    >
        <span 
            class="material-symbols-outlined text-sm" 
            :class="{'animate-spin': isDownloading}">
            {{ isDownloading ? 'progress_activity' : 'picture_as_pdf' }}
        </span>
        
        <span>{{ isDownloading ? 'Downloading...' : 'Original PDF' }}</span>
    </button>
</template>
<script setup lang="ts">
import api from '@tenant/api/axios';
import { ref } from 'vue';

const props = defineProps<{ id: string, filename?: string}>();
const isDownloading = ref(false);

const handleDownload = async () => {
    isDownloading.value = true;
    try {
        const res = await api.get(`/projects/${props.id}/download`, {
            responseType: 'blob'
        });
        const url =  window.URL.createObjectURL(new Blob([res.data]));
        const link = document.createElement('a');
        link.href = url;
        // Intentamos sacar el nombre real del archivo desde los headers o usamos uno por defecto
        const contentDisposition = res.headers['content-disposition'];
        let fileName = props.filename || `project-${props.id}.pdf`;

        if (contentDisposition) {
            const fileNameMatch = contentDisposition.match(/filename="?([^"]+)"?/);
            if (fileNameMatch && fileNameMatch.length === 2)
                fileName = fileNameMatch[1];
        }

        link.setAttribute('download', fileName);
        document.body.appendChild(link);
        link.click();
        link.remove();
        window.URL.revokeObjectURL(url);
    } catch (error) {
        console.error("Download failed:", error);
        alert("Error downloading file. It might have been deleted.");
    } finally {
        isDownloading.value = false;
    }
}
</script>