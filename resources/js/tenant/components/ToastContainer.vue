<script setup lang="ts">
import { useUIStore } from '@/tenant/store/ui'; // Ajusta la ruta a tu store
const ui = useUIStore();

const icons = {
    success: 'check_circle',
    error: 'warning',
    warning: 'priority_high',
    info: 'info'
};

const colors = {
    success: 'border-l-primary text-primary shadow-[0_0_15px_rgba(13,242,242,0.2)]',     
    error: 'border-l-red-500 text-red-500 shadow-[0_0_15px_rgba(239,68,68,0.2)]',       
    warning: 'border-l-amber-500 text-amber-500 shadow-[0_0_15px_rgba(245,158,11,0.2)]', 
    info: 'border-l-blue-400 text-blue-400 shadow-[0_0_15px_rgba(96,165,250,0.2)]'       
};
</script>

<template>
    <Teleport to="body">
        
        <div class="fixed top-6 right-6 z-[200] flex flex-col gap-3 pointer-events-none w-80">
            
            <TransitionGroup name="toast">
                <div v-for="toast in ui.toasts" :key="toast.id"
                     class="pointer-events-auto bg-navy-deep/95 backdrop-blur-xl border border-white/10 rounded-r-lg border-l-4 p-4 flex gap-3 items-start select-none shadow-2xl"
                     :class="colors[toast.type]">
                    
                    <span class="material-symbols-outlined text-xl mt-0.5 animate-pulse">
                        {{ icons[toast.type] }}
                    </span>

                    <div class="flex-1 overflow-hidden">
                        <h4 v-if="toast.title" class="font-bold text-xs uppercase tracking-wider mb-1 text-white opacity-90">
                            {{ toast.title }}
                        </h4>
                        <p class="text-xs font-mono text-slate-300 leading-relaxed break-words">
                            {{ toast.message }}
                        </p>
                    </div>

                    <button @click="ui.removeToast(toast.id)" class="text-slate-500 hover:text-white transition-colors">
                        <span class="material-symbols-outlined text-xs">close</span>
                    </button>
                </div>
            </TransitionGroup>

        </div>
    </Teleport>
</template>

<style scoped>
/* Animación de entrada estilo "Deslizamiento Cyberpunk" */
.toast-enter-active,
.toast-leave-active {
  transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); /* Ease-out suave */
}

.toast-enter-from {
  opacity: 0;
  transform: translateX(50px) scale(0.9);
}

.toast-leave-to {
  opacity: 0;
  transform: translateX(50px);
}
</style>