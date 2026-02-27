<template>
    <button
        class="border border-amber-500/30 text-amber-500 hover:bg-amber-500/10 transition-all text-sm font-bold px-4 py-2 rounded-lg flex items-center gap-2 hover-spin-effect">
        <span class="material-symbols-outlined text-sm">autorenew</span>
        Re-run Analysis
    </button>
</template>
<script setup lang="ts">
const props = defineProps<{ id: string }>();

const handle = () => {
    console.log('ReRunAnalysis')
}
</script>