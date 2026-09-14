<script setup lang="ts">
import { ref, nextTick, watch, onMounted } from 'vue';

// 1. Tipado de los mensajes para estructurar la data que viene de Laravel/n8n
export interface ChatMessage {
    id: string | number;
    role: 'user' | 'agent';
    type: 'text' | 'structured'; // 'text' es un mensaje normal, 'structured' es el panel de diagnóstico
    content: any; // String para texto, Object para datos estructurados
}

const props = withDefaults(defineProps<{
    messages: ChatMessage[];
    isProcessing?: boolean; // Para mostrar la animación de "Pensando..."
}>(), {
    messages: () => [],
    isProcessing: false
});

const emit = defineEmits(['send-message']);


const feedContainer = ref<HTMLElement | null>(null);

// 2. Motor de Auto-Scroll
const scrollToBottom = async () => {
    await nextTick();
    if (feedContainer.value) {
        feedContainer.value.scrollTop = feedContainer.value.scrollHeight;
    }
};

// Vigila si llegan mensajes nuevos o cambia el estado de procesamiento para bajar el scroll
watch([() => props.messages.length, () => props.isProcessing], () => {
    scrollToBottom();
});

onMounted(() => {
    scrollToBottom();
});

</script>

<template>
    <div class="flex flex-col h-full w-full bg-[#0a192f] border border-[#0df2f2]/20 shadow-inner overflow-hidden relative">
        
        <div class="bg-[#020a17] p-3 border-b border-[#0df2f2]/30 flex justify-between items-center shrink-0 z-10">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-[#0df2f2] text-xl">terminal</span>
                <span class="font-mono text-[10px] md:text-xs tracking-[0.2em] md:tracking-[0.3em] text-[#0df2f2]">OPERATIONAL_DIALOGUE</span>
            </div>
            <div class="flex items-center gap-4">
                <span class="font-mono text-[9px] text-slate-500 uppercase tracking-widest hidden sm:block">Secure_Node: Licit_01</span>
                <div class="w-2 h-2 bg-[#0df2f2] animate-pulse shadow-[0_0_8px_#0df2f2]"></div>
            </div>
        </div>

        <div ref="feedContainer" class="flex-1 overflow-y-auto p-4 md:p-6 space-y-8 custom-scrollbar scroll-smooth">
            <template v-for="msg in messages" :key="msg.id">
            </template>

            <div v-if="isProcessing" class="flex items-center gap-3 opacity-80 pt-4 animate-[fadeIn_0.3s_ease-out]">
                </div>
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(13, 242, 242, 0.2); border-radius: 4px;}
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(13, 242, 242, 0.8); }

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(5px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>