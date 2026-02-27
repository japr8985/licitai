<script setup lang="ts">
import { ProjectStatus } from '@tenant/types/api';
import { computed } from 'vue';

const props = defineProps<{
    status: ProjectStatus | string // string por si acaso viene algo raro de la DB
}>();

// Configuración Centralizada de Estilos
const statusConfig: Record<string, { bg: string, text: string, ring: string, icon: string, animate?: boolean }> = {
    // 1. PENDING (Gris/Neutro)
    pending: {
        bg: 'bg-slate-500/10',
        text: 'text-slate-400',
        ring: 'ring-slate-500/30',
        icon: 'hourglass_empty'
    },
    // 2. PROCESSING (Ámbar/Trabajando)
    processing: {
        bg: 'bg-amber-500/10',
        text: 'text-amber-400',
        ring: 'ring-amber-500/30',
        icon: 'sync',
        animate: true // Pulse effect
    },
    // 3. ANALYZED (Cyan/Primary - El color de la marca)
    analyzed: {
        bg: 'bg-primary/10', // Tu cyan #0df2f2
        text: 'text-primary',
        ring: 'ring-primary/30',
        icon: 'psychology'
    },
    // 4. APPROVED (Verde/Éxito)
    approved: {
        bg: 'bg-emerald-500/10',
        text: 'text-emerald-400',
        ring: 'ring-emerald-500/30',
        icon: 'check_circle'
    },
    // 5. REJECTED (Rojo/Parada)
    rejected: {
        bg: 'bg-red-500/10',
        text: 'text-red-400',
        ring: 'ring-red-500/30',
        icon: 'cancel'
    },
    // 6. FAILED (Rosa/Error de Sistema)
    failed: {
        bg: 'bg-rose-500/10',
        text: 'text-rose-500',
        ring: 'ring-rose-500/30',
        icon: 'error'
    }
};

// Computed para obtener la config o un default seguro
const config = computed(() => {
    const key = props.status?.toLowerCase() || 'pending';
    return statusConfig[key] || statusConfig['pending'];
});
</script>

<template>
    <span 
        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold ring-1 ring-inset uppercase tracking-wider transition-all duration-300"
        :class="[config.bg, config.text, config.ring, { 'glow-effect': status === 'analyzed' }]"
    >
        <span 
            v-if="config.animate"
            class="material-symbols-outlined text-[10px] mr-1.5 animate-spin"
        >
            {{ config.icon }}
        </span>
        <span 
            v-else
            class="material-symbols-outlined text-[10px] mr-1.5"
        >
            {{ config.icon }}
        </span>

        {{ status }}
    </span>
</template>

<style scoped>
.glow-effect {
    box-shadow: 0 0 10px rgba(13, 242, 242, 0.2);
}
</style>