<script setup>
defineProps({
    team: {
        type: Array,
        required: true
    }
});
</script>

<template>
    <div class="md:col-span-12 glass-card rounded-xl p-6">
        <div class="flex items-center justify-between mb-8 border-b border-white/10 pb-4">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-primary">groups</span>
                <h3 class="text-white font-bold text-lg uppercase tracking-tight">Recommended Squad / Resource Allocation</h3>
            </div>
            <div class="text-[10px] font-mono text-slate-500 uppercase tracking-widest">Team ID: UNIT_402_ALPHA</div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div v-for="(member, index) in team" :key="index" 
                 class="p-4 rounded-xl flex flex-col gap-4 relative overflow-hidden transition-all duration-300"
                 :class="index === 0 ? 'bg-primary/5 border-2 border-primary/50 glow-teal' : 'bg-slate-800/20 border border-white/10 hover:border-white/20'">
                
                <div v-if="index === 0" class="absolute top-2 right-2">
                    <span class="bg-primary text-background-dark text-[8px] font-black px-2 py-0.5 rounded uppercase">Top Match</span>
                </div>

                <div class="flex items-center gap-3">
                    <div class="size-12 rounded bg-slate-800 p-0.5 overflow-hidden border"
                         :class="index === 0 ? 'border-primary/30' : 'border-white/10'">
                         <div class="w-full h-full bg-slate-700 flex items-center justify-center text-xs font-bold text-slate-400">
                            {{ member.name.charAt(0) }}
                         </div>
                    </div>
                    <div>
                        <p class="text-white font-bold text-sm">{{ member.name }}</p>
                        <p class="text-[10px] font-mono uppercase" :class="index === 0 ? 'text-primary' : 'text-slate-400'">{{ member.role }}</p>
                    </div>
                </div>

                <div class="space-y-1">
                    <div class="flex justify-between text-[10px] font-mono">
                        <span class="text-slate-400 uppercase">Match Score</span>
                        <span :class="index === 0 ? 'text-primary' : 'text-slate-300'">{{ member.match_score }}% Fit</span>
                    </div>
                    <div class="h-1 bg-slate-800 rounded-full overflow-hidden">
                        <div class="h-full rounded-full" 
                             :class="index === 0 ? 'bg-primary' : 'bg-slate-400'"
                             :style="{ width: member.match_score + '%' }"></div>
                    </div>
                </div>

                <p class="text-[10px] text-slate-500 font-mono italic truncate">Matches: {{ member.skills_matched.join(', ') }}</p>
            </div>
        </div>
    </div>
</template>