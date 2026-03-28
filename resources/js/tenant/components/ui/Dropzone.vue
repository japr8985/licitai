<script setup lang="ts">
import { ref } from 'vue';

const props = withDefaults(defineProps<{
    variant?: 'rectangular' | 'circular';
    accept?: string;
    icon?: string;
    title: string;
    subtitle?: string;
    limitText?: string;
    activeText?: string;
    file: File | null;
    previewUrl?: string | null;
    label?: string;
    pulsing?: boolean
}>(), {
    variant: 'rectangular',
    icon: 'upload_file',
    activeText: 'PAYLOAD_SECURED'
});

const emit = defineEmits(['file-selected']);
const fileInput = ref<HTMLInputElement | null>(null);

const triggerFileInput = () => fileInput.value?.click();

const handleFileUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const selectedFile = target.files?.[0];
    if (selectedFile) emit('file-selected', selectedFile);
    target.value = '';
};
const isDragging = ref(false);
const onDragEnter = (e: DragEvent) => {
    isDragging.value = true;
};

const onDragLeave = (e: DragEvent) => {
    isDragging.value = false;
};
const onDrop = (e: DragEvent) => {
    isDragging.value = false; // Apagamos el efecto visual
    
    // Extraemos el archivo de los datos transferidos por el Sistema Operativo
    const droppedFile = e.dataTransfer?.files?.[0];
    
    if (droppedFile) {
        // Opcional: Podrías validar el tipo de archivo aquí basándote en props.accept
        emit('file-selected', droppedFile);
    }
};
</script>

<template>
    
    <div class="space-y-2 group">
        <label v-if="label"class="flex justify-between text-[11px] font-mono text-cyber-teal tracking-widest uppercase">
            <span>{{ label }}</span>
        </label>
        <div class="neural-dropzone group/dropzone cursor-pointer w-full h-full min-h-[140px] flex" 
            :class="{ 'animate-pulse-glow': pulsing }" 
            @click="triggerFileInput"
            @dragenter.prevent="onDragEnter"
            @dragover.prevent="onDragEnter"
            @dragleave.prevent="onDragLeave"
            @drop.prevent="onDrop">
        
        <input type="file" ref="fileInput" class="hidden" @change="handleFileUpload" :accept="accept" />
        
        <div class="absolute inset-0 bg-[#0df2f2]/10 translate-y-full transition-transform duration-500 ease-in-out group-hover/dropzone:translate-y-0 z-0 pointer-events-none"></div>
        
        <div class="relative z-10 w-full h-full flex transition-all overflow-hidden"
             :class="variant === 'rectangular' ? 'flex-col items-center justify-center p-6' : 'items-center justify-center gap-4 p-5'">
            
            <template v-if="variant === 'rectangular'">
                
                <template v-if="file">
                    <div class="w-16 h-16 mb-4 rounded-full border border-[#0df2f2] flex items-center justify-center bg-[#0df2f2]/20 shadow-[0_0_20px_rgba(13,242,242,0.3)] group-hover/dropzone:scale-110 transition-transform duration-300">
                        <span class="material-symbols-outlined text-3xl text-[#0df2f2]">task</span>
                    </div>
                    <div class="text-sm font-bold text-white mb-1 tracking-wider text-center px-4 break-words line-clamp-2">{{ file.name }}</div>
                    <div class="text-[10px] font-mono text-[#0df2f2] uppercase mt-2">{{ activeText }}</div>
                </template>
                
                <template v-else>
                    <div class="w-16 h-16 mb-4 rounded-full border border-[#0df2f2]/30 flex items-center justify-center bg-[#0a192f]/50 group-hover/dropzone:scale-110 transition-transform duration-300 group-hover/dropzone:border-[#0df2f2] group-hover/dropzone:shadow-[0_0_15px_rgba(13,242,242,0.4)] relative overflow-hidden">
                        <span class="material-symbols-outlined text-3xl text-[#0df2f2] animate-glitch relative z-10">{{ icon }}</span>
                    </div>
                    <span class="text-white font-bold text-sm tracking-wide z-10">{{ title }}</span>
                    <span v-if="subtitle" class="text-[#0df2f2]/60 text-[10px] font-mono mt-1 text-center max-w-[200px] uppercase z-10">{{ subtitle }}</span>
                    <div v-if="limitText" class="text-[9px] font-mono text-[#0df2f2] bg-[#0df2f2]/10 px-3 py-1.5 border border-[#0df2f2]/30 rounded mt-auto z-10">
                        {{ limitText }}
                    </div>
                </template>

                <div class="absolute top-2 left-2 w-2 h-2 border-t border-l border-[#0df2f2]/50 pointer-events-none"></div>
                <div class="absolute top-2 right-2 w-2 h-2 border-t border-r border-[#0df2f2]/50 pointer-events-none"></div>
                <div class="absolute bottom-2 left-2 w-2 h-2 border-b border-l border-[#0df2f2]/50 pointer-events-none"></div>
                <div class="absolute bottom-2 right-2 w-2 h-2 border-b border-r border-[#0df2f2]/50 pointer-events-none"></div>
            </template>

            <template v-else>
                <div class="h-16 w-16 shrink-0 rounded-full border-2 border-[#0df2f2]/30 flex items-center justify-center bg-[#0a192f] shadow-[0_0_15px_rgba(13,242,242,0.1)] overflow-hidden group-hover/dropzone:border-[#0df2f2] group-hover/dropzone:shadow-[0_0_20px_rgba(13,242,242,0.3)] group-hover/dropzone:scale-110 transition-all duration-300 z-10">
                    <img v-if="previewUrl" :src="previewUrl" class="w-full h-full object-cover" />
                    <span v-else class="material-symbols-outlined text-[#0df2f2] text-2xl animate-glitch">{{ icon }}</span>
                </div>
                <div class="flex flex-col min-w-0 z-10 text-left">
                    <span class="text-white font-bold text-sm tracking-wide truncate">{{ title }}</span>
                    <span class="text-[#0df2f2]/60 text-[10px] font-mono mt-1 truncate">{{ file ? file.name : subtitle }}</span>
                    <span v-if="file" class="text-[#10b981] text-[9px] font-mono mt-1 uppercase">{{ activeText }}</span>
                </div>
            </template>
        </div>
    </div>
    </div>
    
    
</template>