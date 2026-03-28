<script setup lang="ts">
import { computed } from 'vue';

export interface RadioOption {
    label: string;
    value: string | number | boolean;
}

const model = defineModel<string | number | boolean | null>();

const props = withDefaults(defineProps<{
    name: string;                 // Atributo name obligatorio para agrupar los radios
    options: RadioOption[];       // Array de opciones { label, value }
    title?: string;               // Título superior (ej. CLEARANCE_LEVEL)
    icon?: string;                // Icono opcional
    layout?: 'row' | 'col';       // Dirección de los radios (horizontal o vertical)
    error?: string;               // Mensaje de error
}>(), {
    layout: 'row'
});

const hasError = computed(() => !!props.error && props.error.length > 0);
</script>

<template>
    <div class="p-4 border rounded transition-colors duration-300"
         :class="hasError ? 'border-red-500/30 bg-red-900/10' : 'border-[#0df2f2]/10 bg-[#0df2f2]/5'">
        
        <h3 v-if="title" class="font-mono text-[11px] mb-4 flex items-center gap-2 uppercase tracking-widest transition-colors duration-300"
            :class="hasError ? 'text-red-500 font-bold' : 'text-[#0df2f2]'">
            <span v-if="icon" class="material-symbols-outlined text-[14px] leading-none shrink-0" style="font-variation-settings: 'FILL' 1;">
                {{ icon }}
            </span>
            {{ title }}
        </h3>

        <div class="flex" :class="layout === 'col' ? 'flex-col gap-3' : 'gap-6 flex-wrap'">
            
            <label v-for="option in options" :key="String(option.value)" 
                   class="flex items-center gap-2.5 cursor-pointer group">
                
                <input 
                    type="radio"
                    :name="name"
                    :value="option.value"
                    v-model="model"
                    class="bg-transparent transition-all duration-200 focus:ring-offset-[#020a17]"
                    :class="hasError 
                        ? 'border-red-500/50 text-red-500 focus:ring-red-500' 
                        : 'border-[#0df2f2]/50 text-[#0df2f2] focus:ring-[#0df2f2] group-hover:border-[#0df2f2] shadow-[0_0_5px_rgba(13,242,242,0)] group-hover:shadow-[0_0_8px_rgba(13,242,242,0.3)]'"
                />
                
                <span class="text-[11px] font-mono transition-colors uppercase tracking-wide"
                      :class="model === option.value 
                        ? (hasError ? 'text-red-400 font-bold' : 'text-white font-bold drop-shadow-[0_0_5px_rgba(13,242,242,0.5)]') 
                        : 'text-slate-400 group-hover:text-slate-300'">
                    {{ option.label }}
                </span>
            </label>

        </div>

        <div class="relative mt-2 min-h-[16px]" v-if="hasError">
            <Transition name="cyber-slide" appear>
                <div class="absolute top-0 left-0 w-full flex items-start gap-1.5 text-red-500 font-mono text-[10px] tracking-tight leading-tight mt-1">
                    <span class="material-symbols-outlined text-[13px] leading-none shrink-0 translate-y-[1px]" style="font-variation-settings: 'FILL' 1;">warning</span>
                    <div class="break-words">CRITICAL_REQUIREMENT: {{ error }}</div>
                </div>
            </Transition>
        </div>
        
    </div>
</template>

<style scoped>
/* Reset base para asegurar que el input se renderice bien si Tailwind Forms falla */
input[type="radio"] {
    appearance: none;
    -webkit-appearance: none;
    border-radius: 50%;
    width: 1rem;
    height: 1rem;
    border-width: 1px;
    display: inline-grid;
    place-content: center;
}

input[type="radio"]::before {
    content: "";
    width: 0.5rem;
    height: 0.5rem;
    border-radius: 50%;
    transform: scale(0);
    transition: 120ms transform ease-in-out;
    box-shadow: inset 1em 1em var(--tw-text-opacity, currentColor);
    /* Forzamos el color cyan o rojo heredado del text-color de Tailwind */
    background-color: currentColor; 
}

input[type="radio"]:checked::before {
    transform: scale(1);
}

/* Transición del error */
.cyber-slide-enter-active,
.cyber-slide-leave-active { transition: all 0.3s cubic-bezier(0.1, 0.9, 0.2, 1); }
.cyber-slide-enter-from { opacity: 0; transform: translateY(-4px); }
.cyber-slide-leave-to { opacity: 0; transform: translateY(4px); }
</style>