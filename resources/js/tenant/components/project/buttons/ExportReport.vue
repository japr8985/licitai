<template>
    <button
        class="flex items-center gap-2 px-4 py-2 rounded-lg bg-primary text-slate-900 hover:brightness-110 transition-all text-sm font-bold glow-teal"
        @click="handle">
        <span class="material-symbols-outlined text-sm">ios_share</span>
        Export Report
    </button>
</template>
<script setup lang="ts">
import api from '@tenant/api/axios';
import { Project } from '@tenant/types/api';
import { ref } from 'vue';

const props = defineProps<{ project: Project }>();
const isExporting = ref(false);

const handle = async () => {
    isExporting.value = true;
    try {
        const response = await api.get(`/projects/${props.project.id}/export-report`, {
            responseType: 'blob', // ¡CRÍTICO!
        });

        // Misma lógica de descarga que el otro botón
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `LicitAI_Report_${props.project.id}.pdf`);
        document.body.appendChild(link);
        link.click();
        link.remove();
        window.URL.revokeObjectURL(url);

    } catch (error) {
        console.error("Export failed", error);
        alert("Could not generate report.");
    } finally {
        isExporting.value = false;
    }
}
</script>