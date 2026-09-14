<script setup lang="ts">
import { ref } from 'vue';

const props = withDefaults(defineProps<{
    isProcessing?: boolean;
    placeholder?: string;
}>(), {
    isProcessing: false,
    placeholder: 'ENTER_OPERATIONAL_COMMAND_OR_PARAMETER...'
});

const emit = defineEmits(['send-message']);
const commandInput = ref('');


// Variables reactivas (asegúrate de tenerlas)
const chatSessionId = ref<string | null>(null);

const executeCommand = async (command: string) => {
    // A) Agregamos lo que el usuario escribió al feed visual
    chatHistory.value.push({
        id: Date.now(),
        role: 'user',
        type: 'text',
        content: command
    });

    // B) Encendemos "Procesando..."
    isAiThinking.value = true;

    try {
        // C) Enviar el POST a nuestro ProxyChatController
        const response = await api.post('/chat', {
            message: command,
            session_id: chatSessionId.value // Enviamos null la 1ra vez, luego el UUID
        });

        // D) Guardamos el session_id que Laravel generó para futuras peticiones
        if (response.data.session_id) {
            chatSessionId.value = response.data.session_id;
        }

        // ¡OJO AQUÍ! 
        // Por ahora, como nuestra arquitectura será asíncrona, n8n nos enviará
        // la respuesta DE VUELTA a Laravel y Laravel nos avisará por Websockets (Echo).
        // Así que aquí NO metemos la respuesta de la IA todavía. 
        // Solo apagaremos isAiThinking cuando llegue el Websocket.
        // Pero para debug, lo apagamos temporalmente tras unos segundos.
        
        // isAiThinking.value = false; // Quita esto cuando tengamos websockets

    } catch (error) {
        console.error("Fallo la conexión con el Neural Engine:", error);
        isAiThinking.value = false;
        // Opcional: Mostrar mensaje de error en el chat
        chatHistory.value.push({
            id: Date.now() + 1,
            role: 'agent',
            type: 'text',
            content: '[ERROR] Neural link severed. Please try again.'
        });
    }
};
</script>

<template>
    <div class="w-full flex flex-col">
        <div class="flex items-stretch w-full shadow-[0_0_15px_rgba(13,242,242,0.05)] border border-[#0df2f2]/20">
            <div class="relative flex-1 bg-[#020a17]">
                <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                    <span class="material-symbols-outlined text-[#0df2f2]/40 text-sm">chevron_right</span>
                </div>
                <input 
                    v-model="commandInput"
                    @keyup.enter="executeCommand"
                    :disabled="isProcessing"
                    class="w-full h-14 bg-transparent border-0 focus:ring-0 text-[#0df2f2] font-mono text-[12px] md:text-sm pl-10 pr-4 placeholder:text-slate-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all outline-none" 
                    :placeholder="placeholder" 
                    type="text"
                />
            </div>
            
            <button 
                @click="executeCommand"
                :disabled="isProcessing || !commandInput.trim()"
                class="w-28 md:w-32 bg-[#0df2f2]/10 text-[#0df2f2] font-mono text-[10px] md:text-xs font-bold tracking-widest hover:bg-[#0df2f2] hover:text-[#0a192f] transition-all duration-300 flex items-center justify-center gap-2 group disabled:opacity-50 disabled:hover:bg-[#0df2f2]/10 disabled:hover:text-[#0df2f2] disabled:cursor-not-allowed border-l border-[#0df2f2]/20">
                EXECUTE
                <span class="material-symbols-outlined text-sm transition-transform" :class="{'group-hover:translate-x-1': !isProcessing && commandInput.trim()}">terminal</span>
            </button>
        </div>
        
        <div class="mt-2 flex justify-between px-2 w-full">
            <span class="font-mono text-[9px] text-slate-600 uppercase tracking-widest">Input_Latency: {{ isProcessing ? 'CALCULATING...' : '12ms' }}</span>
            <span class="font-mono text-[9px] text-green-500/70 font-bold uppercase">● SECURE_CHANNEL</span>
        </div>
    </div>
</template>