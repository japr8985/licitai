<script setup lang="ts">
import { useSlots } from 'vue';

// Control bidireccional del estado del modal (Vue 3.4+)
const isOpen = defineModel<boolean>('open', { default: false });

const props = withDefaults(defineProps<{
    title?: string;
    subtitle?: string;
    statusText?: string;
    statusColor?: 'cyan' | 'red' | 'emerald'; 
    maxWidth?: 'sm' | 'md' | 'lg' | 'xl' | '2xl' | '4xl' | '6xl' | 'full';
    persistent?: boolean; // Evita que se cierre al hacer click afuera
}>(), {
    statusColor: 'cyan',
    maxWidth: '4xl',
    persistent: false
});

const slots = useSlots();

const closeModal = () => {
    if (!props.persistent) {
        isOpen.value = false;
    }
};

const forceClose = () => {
    isOpen.value = false;
};

// Diccionario de anchos para Tailwind
const maxWidthClasses: Record<string, string> = {
    'sm': 'max-w-sm',
    'md': 'max-w-md',
    'lg': 'max-w-lg',
    'xl': 'max-w-xl',
    '2xl': 'max-w-2xl',
    '4xl': 'max-w-4xl',
    '6xl': 'max-w-6xl',
    'full': 'max-w-[95vw]'
};
</script>

<template>
    <slot name="trigger" :open="() => isOpen = true"></slot>

    <Teleport to="body">
        <Transition name="cyber-holo">
            <div v-if="isOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 md:p-8 overflow-hidden font-display perspective-[1000px]">
                
                <div class="absolute inset-0 bg-[#0a192f]/80 backdrop-blur-sm z-0" @click="closeModal"></div>
                <div class="absolute inset-0 tech-grid-bg opacity-20 pointer-events-none z-0"></div>
                <div class="absolute inset-0 scanline-overlay opacity-10 pointer-events-none z-0"></div>

                <div class="relative z-10 w-full glass-panel flex flex-col max-h-[95vh] rounded-xl transform-gpu shadow-2xl" 
                     :class="maxWidthClasses[maxWidth]">
                    
                    <div class="absolute top-0 left-0 w-8 h-8 border-t-2 border-l-2 border-[#0df2f2] rounded-tl-xl pointer-events-none"></div>
                    <div class="absolute top-0 right-0 w-8 h-8 border-t-2 border-r-2 border-[#0df2f2] rounded-tr-xl pointer-events-none"></div>
                    <div class="absolute bottom-0 left-0 w-8 h-8 border-b-2 border-l-2 border-[#0df2f2] rounded-bl-xl pointer-events-none"></div>
                    <div class="absolute bottom-0 right-0 w-8 h-8 border-b-2 border-r-2 border-[#0df2f2] rounded-br-xl pointer-events-none"></div>

                    <div class="relative flex flex-col w-full h-full overflow-hidden p-6 md:p-8 lg:p-10">
                        
                        <header v-if="title || slots.header" class="mb-8 border-b border-[#0df2f2]/20 pb-6 flex flex-col md:flex-row md:items-end justify-between gap-4 shrink-0">
                            <slot name="header">
                                <div>
                                    <div v-if="subtitle" class="flex items-center gap-2 mb-2">
                                        <span class="material-symbols-outlined text-[#0df2f2] text-xl">terminal</span>
                                        <span class="text-[#0df2f2]/70 font-mono text-xs tracking-widest uppercase">{{ subtitle }}</span>
                                    </div>
                                    <h1 class="text-white text-3xl md:text-4xl font-bold tracking-tight mb-1 uppercase">{{ title }}</h1>
                                    <p class="text-[#0df2f2]/60 font-mono text-sm tracking-wide cursor-blink">Awaiting Input...</p>
                                </div>
                                <div v-if="statusText" class="flex items-center gap-3 bg-[#1d3456]/50 px-4 py-2 rounded border border-white/5 shadow-inner">
                                    <div class="h-2 w-2 rounded-full animate-pulse" 
                                         :class="{
                                            'bg-[#0df2f2] shadow-[0_0_8px_rgba(13,242,242,0.8)]': statusColor === 'cyan',
                                            'bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.8)]': statusColor === 'red',
                                            'bg-[#10b981] shadow-[0_0_8px_rgba(16,185,129,0.8)]': statusColor === 'emerald'
                                         }">
                                    </div>
                                    <span class="text-xs font-mono text-slate-300 uppercase tracking-wider">{{ statusText }}</span>
                                </div>
                            </slot>
                        </header>

                        <div class="flex-1 overflow-y-auto custom-scrollbar relative z-10 pr-2">
                            <slot></slot>
                        </div>

                        <footer v-if="slots.footer" class="mt-8 pt-6 border-t border-[#0df2f2]/20 flex flex-col md:flex-row items-center justify-between gap-6 shrink-0 z-10">
                            <slot name="footer" :close="forceClose"></slot>
                        </footer>

                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
/* Transición Holográfica de Entrada/Salida */
.cyber-holo-enter-active .glass-panel { 
    animation: holoMaterialize 0.4s cubic-bezier(0.1, 0.9, 0.2, 1) forwards; 
}
.cyber-holo-leave-active .glass-panel { 
    transition: all 0.2s ease-in; 
    opacity: 0; 
    transform: scale(0.95) translateY(10px); 
}

/* Fades para los overlays de fondo */
.cyber-holo-enter-active > div:not(.glass-panel), 
.cyber-holo-leave-active > div:not(.glass-panel) { 
    transition: opacity 0.3s ease-out; 
}
.cyber-holo-enter-from > div:not(.glass-panel), 
.cyber-holo-leave-to > div:not(.glass-panel) { 
    opacity: 0; 
}

@keyframes holoMaterialize {
    0% { opacity: 0; transform: scale(0.9) translateY(20px); filter: blur(10px) brightness(3); }
    50% { filter: blur(2px) brightness(1.5); transform: scale(1.02); }
    100% { opacity: 1; transform: scale(1) translateY(0); filter: blur(0) brightness(1); }
}

/* Ajustes finos del scroll interno del modal */
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(13, 242, 242, 0.2); border-radius: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(13, 242, 242, 0.6); }

/* Animación del cursor en el texto Awaiting Input */
.cursor-blink::after {
    content: '_';
    animation: blink 1s step-end infinite;
}
@keyframes blink {
    50% { opacity: 0; }
}
</style>