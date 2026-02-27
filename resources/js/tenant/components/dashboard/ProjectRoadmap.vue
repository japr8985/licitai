<script setup>
defineProps({
    roadmap: {
        type: Array,
        required: true
    }
});
</script>

<template>
    <div class="md:col-span-12 glass-card rounded-xl p-6 relative overflow-hidden mt-6">
        <div class="absolute top-0 right-0 p-4 font-mono text-[10px] text-primary/20 pointer-events-none">
            PLAN_REV: 0.9.4a
        </div>
        <div class="flex items-center gap-3 mb-10 border-b border-white/10 pb-4">
            <span class="material-symbols-outlined text-amber-500">route</span>
            <h3 class="text-white font-bold text-lg uppercase tracking-tight">AI Suggested Roadmap / Sprints</h3>
        </div>

        <div class="relative pl-8 space-y-10">
            <div class="absolute left-[11px] top-2 bottom-2 w-px timeline-line"></div>

            <div v-for="(phase, index) in roadmap" :key="index" class="relative flex flex-col md:flex-row md:items-start justify-between gap-4">
                
                <div class="absolute -left-10 mt-1 size-6 rounded border flex items-center justify-center z-10 shadow-[0_0_10px_rgba(0,0,0,0.5)]"
                     :class="index === 0 ? 'bg-primary border-background-dark' : 'bg-slate-900 border-white/30'">
                    <span class="material-symbols-outlined text-xs font-black"
                          :class="index === 0 ? 'text-background-dark' : 'text-white/50'">
                          {{ index === 0 ? 'check' : (index === roadmap.length -1 ? 'lock_open' : 'hourglass_top') }}
                    </span>
                </div>

                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-1">
                        <span class="font-mono text-[10px] uppercase font-bold tracking-widest"
                              :class="index === 0 ? 'text-primary' : 'text-slate-400'">Phase {{ index + 1 }}</span>
                        <h4 class="font-bold text-base uppercase" :class="index === roadmap.length -1 ? 'text-slate-500' : 'text-white'">{{ phase.title }}</h4>
                    </div>
                    <p class="text-xs font-mono" :class="index === roadmap.length -1 ? 'text-slate-600' : 'text-slate-400'">{{ phase.description }}</p>
                </div>

                <div class="flex items-center gap-4 min-w-[200px] justify-end" :class="{'opacity-40': index === roadmap.length -1}">
                    <div class="px-3 py-1 bg-slate-900 border border-white/10 rounded flex items-center gap-2">
                        <span class="text-[10px] text-slate-500 font-bold uppercase">{{ phase.sprints }}</span>
                        <div class="h-3 w-px bg-white/10"></div>
                        <span class="text-[10px] font-bold" :class="index === 0 ? 'text-primary' : 'text-slate-400'">{{ phase.hours }} HOURS</span>
                    </div>
                    <div class="w-24 h-1 bg-slate-800 rounded-full overflow-hidden">
                        <div class="h-full" 
                             :class="index === 0 ? 'bg-primary' : (index === 1 ? 'bg-primary/30' : 'bg-primary/10')" 
                             :style="{ width: index === 0 ? '100%' : (index === 1 ? '15%' : '0%') }"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.timeline-line {
    background: linear-gradient(to bottom, #0df2f2 50%, transparent 50%);
    background-size: 1px 12px;
}
</style>