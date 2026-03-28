<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{summary: String}>()
const text = computed(() => {
    return props.summary?.split('\n');
})
</script>

<template>
    <div class="h-full glass-card rounded-xl overflow-hidden flex flex-col border-primary/20">
        <div class="bg-slate-900/80 px-4 py-2 flex items-center justify-between border-b border-white/10">
            <div class="flex items-center gap-2">
                <div class="flex gap-1.5">
                    <div class="size-2 rounded-full bg-red-500/40"></div>
                    <div class="size-2 rounded-full bg-amber-500/40"></div>
                    <div class="size-2 rounded-full bg-primary/40"></div>
                </div>
                <span class="text-[10px] font-mono text-slate-500 ml-4">AI_EXECUTIVE_BRIEF.LOG</span>
            </div>
            <span class="material-symbols-outlined text-slate-500 text-sm">terminal</span>
        </div>
        <div
            class="p-6 font-mono text-sm leading-relaxed text-primary/80 h-[320px] overflow-y-auto scanline custom-scrollbar">
            <p class="mb-4 text-white font-bold">&gt; INITIALIZING PROJECT HEURISTICS...</p>

            <p class="mb-4" v-for="(t, i) in text" :key="i">
                &gt; {{ t }}
            </p>
            <p class="animate-pulse">_</p>
            <!-- <p class="mb-4">&gt; Analysis complete for Minister of Modernization hybrid cloud tender.
                The financial proposition is strong with a profit margin estimated at 24% despite
                stringent SLA requirements.</p>
            <p class="mb-4 text-amber-400">&gt; CRITICAL NOTICE: The timeline for Phase 1 (Cloud
                Migration) is established at 90 days. Our neural engine suggests an 84% probability of
                overrun based on historical department bottlenecks.</p>
            <p class="mb-4">&gt; COMPETITIVE INTELLIGENCE: 3 potential competitors detected in previous
                cycles for similar specs. Price point should remain under $1.15M for maximum scoring
                weight.</p>
            <p class="mb-4">&gt; RECOMMENDATION: Proceed with bid if penalty clauses can be capped at
                10% of total contract value. Legal team must review Section 12 on 'Force Majeure'.</p>
            <p class="animate-pulse">_</p> -->
        </div>
    </div>
</template>

<style scoped>
.scanline {
    background: linear-gradient(to bottom, transparent 50%, rgba(13, 242, 242, 0.1) 50%);
    background-size: 100% 4px;
}
.custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.1);
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(13, 242, 242, 0.3);
        border-radius: 10px;
    }
</style>