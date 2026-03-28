<script setup lang="ts">
import { ref, computed, useSlots, watch } from 'vue';

const model = defineModel<number | null>();

const props = withDefaults(defineProps<{
    id?: string;
    label?: string;       
    placeholder?: string;
    error?: string[];       
    prefix?: string;      
    reqText?: string;     
    hint?: string;
    required?: boolean;
    locale?: string;
}>(), {
    reqText: 'NUMERIC_ONLY',
    required: false,
    locale: 'en-US',
    error: () => []
});

const slots = useSlots();
const hasError = computed(() => !!props.error && props.error.length > 0);

// --- MOTOR DE FORMATEO ESTRICTO ---
const isFocused = ref(false);
const internalString = ref('');

// Sincronizar desde Laravel hacia Vue
watch(model, (newVal) => {
    if (!isFocused.value) {
        internalString.value = newVal == null ? '' : newVal.toString();
    }
}, { immediate: true });

// El valor que se muestra en pantalla (Crudo en focus, Formateado en blur)
const displayValue = computed(() => {
    if (isFocused.value) return internalString.value;
    if (model.value == null || isNaN(model.value)) return '';
    
    return new Intl.NumberFormat(props.locale, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(model.value);
});

// CAPA 1: Evitar teclas inválidas antes de que se registren
const handleKeydown = (e: KeyboardEvent) => {
    // Permitir teclas de control (Backspace, Flechas, Tab, Ctrl+V, etc.)
    if (e.key.length > 1 || e.ctrlKey || e.metaKey) return;
    
    // Solo permitimos números, el punto y el guion
    if (!/[0-9.-]/.test(e.key)) {
        e.preventDefault();
        return;
    }

    // Evitar múltiples puntos
    if (e.key === '.' && internalString.value.includes('.')) {
        e.preventDefault();
    }

    // Evitar el guion (negativo) si no es el primer caracter
    const target = e.target as HTMLInputElement;
    if (e.key === '-' && target.selectionStart !== 0) {
        e.preventDefault();
    }
};

// CAPA 2: Limpieza agresiva (Para proteger contra Copiar y Pegar textos sucios)
const handleInput = (e: Event) => {
    const target = e.target as HTMLInputElement;
    let val = target.value;
    
    // 1. Borrar todo lo que no sea número, punto o guion
    let cleanVal = val.replace(/[^0-9.-]/g, '');
    
    // 2. Proteger contra múltiples guiones (ej. pegó "--5")
    const hasMinus = cleanVal.startsWith('-');
    cleanVal = cleanVal.replace(/-/g, '');
    if (hasMinus) cleanVal = '-' + cleanVal;
    
    // 3. Proteger contra múltiples puntos (ej. pegó "1.2.3")
    const parts = cleanVal.split('.');
    if (parts.length > 2) {
        cleanVal = parts[0] + '.' + parts.slice(1).join('');
    }
    
    // 4. Forzar al DOM a mostrar el valor limpio (Esto quita las letras de la pantalla si pegó basura)
    if (val !== cleanVal) {
        target.value = cleanVal;
    }
    
    internalString.value = cleanVal;
    
    // 5. Emitir a Laravel
    if (cleanVal === '' || cleanVal === '-' || cleanVal === '.') {
        model.value = null;
    } else {
        model.value = parseFloat(cleanVal);
    }
};
</script>

<template>
    <div class="space-y-3 group/input">
        
        <div class="flex justify-between font-mono text-[10px] tracking-wider uppercase transition-colors duration-300">
            <slot name="label" :has-error="hasError">
                <span v-if="label" :class="hasError ? 'text-red-500 font-bold' : 'text-cyber-teal'">
                    {{ label }}
                </span>
            </slot>
            <slot name="status" :has-error="hasError">
                <span :class="hasError ? 'text-red-500/60' : 'text-slate-500'">
                    {{ hasError ? 'CRITICAL' : reqText }}
                </span>
            </slot>
        </div>

        <div class="relative flex items-center">
            
            <slot name="prefix" :has-error="hasError">
                <span v-if="prefix" 
                      class="absolute left-3 font-mono text-lg pointer-events-none transition-colors duration-300"
                      :class="hasError ? 'text-red-500' : 'text-cyber-teal'">
                    {{ prefix }}
                </span>
            </slot>

            <input 
                :id="id"
                type="text"
                :value="displayValue"
                @keydown="handleKeydown" 
                @input="handleInput"
                @focus="isFocused = true"
                @blur="isFocused = false"
                :placeholder="placeholder"
                class="w-full bg-[#020a17]/80 border-0 border-b font-mono py-3 placeholder:text-slate-700 focus:ring-0 transition-all duration-300 outline-none cyber-hide-arrows"
                :class="[
                    (prefix || slots.prefix) ? 'pl-8' : 'pl-3',
                    slots.suffix ? 'pr-8' : 'pr-3',
                    hasError 
                        ? 'border-red-900/50 text-red-400 focus:border-red-500 shadow-[0_4px_12px_-4px_rgba(239,68,68,0.3)]' 
                        : 'border-[#0df2f2]/30 text-white focus:border-cyber-teal focus:shadow-[0_4px_12px_-4px_rgba(13,242,242,0.3)]'
                ]"
                :required="required"
            />

            <div v-if="slots.suffix" class="absolute right-3 flex items-center">
                <slot name="suffix" :has-error="hasError"></slot>
            </div>
            
            <div class="absolute bottom-0 left-0 h-[1px] w-full origin-left transition-transform duration-500 ease-out"
                 :class="hasError 
                    ? 'bg-red-500 scale-x-100 shadow-[0_0_8px_rgba(239,68,68,0.4)]' 
                    : 'bg-cyber-teal scale-x-0 group-focus-within/input:scale-x-100 shadow-[0_0_10px_#0df2f2]'">
            </div>
        </div>

        <div class="min-h-[20px] overflow-hidden mt-1 relative">
            <Transition name="cyber-slide">
                <div v-if="hasError" class="absolute top-0 left-0 w-full">
                    <slot name="error" :error="error">
                        <div class="flex items-center gap-2 text-red-500 font-mono text-[10px] tracking-tight">
                            <span class="material-symbols-outlined text-[14px] leading-none shrink-0" style="font-variation-settings: 'FILL' 1;">warning</span>
                            <span class="truncate">INVALID_ENTRY_DETECTED: {{ error.join(' ') }}</span>
                        </div>
                    </slot>
                </div>
                <div v-else-if="hint || slots.hint" class="absolute top-0 left-0 w-full">
                    <slot name="hint">
                        <div class="font-mono text-[9px] text-slate-500">
                            {{ hint }}
                        </div>
                    </slot>
                </div>
            </Transition>
        </div>
        
    </div>
</template>

<style scoped>
.cyber-slide-enter-active,
.cyber-slide-leave-active { transition: all 0.3s cubic-bezier(0.1, 0.9, 0.2, 1); }
.cyber-slide-enter-from { opacity: 0; transform: translateY(-4px); }
.cyber-slide-leave-to { opacity: 0; transform: translateY(4px); }

.cyber-hide-arrows::-webkit-outer-spin-button,
.cyber-hide-arrows::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
.cyber-hide-arrows[type=number] {
  appearance: textfield;
  -moz-appearance: textfield;
}
</style>