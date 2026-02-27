<template>
    <div class="h-full glass-card rounded-xl p-6 font-mono">
        <h3
            class="text-white font-bold text-sm uppercase tracking-widest mb-8 border-b border-white/10 pb-4 flex items-center justify-between">
            Metric Analysis
            <span class="text-primary text-[10px]">[DEBUG_MODE: ON]</span>
        </h3>
        <div class="space-y-8">
            <div>
                <div class="flex justify-between text-[11px] mb-2">
                    <span class="text-slate-400 uppercase">Financial Health</span>
                    <span class="text-primary">40/40 <span class="text-slate-600">[OPTIMAL]</span></span>
                </div>
                <div class="h-1.5 w-full bg-slate-800 rounded-full overflow-hidden">
                    <div class="h-full bg-primary rounded-full glow-teal w-[100%] shadow-[0_0_8px_#0df2f2]">
                    </div>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-[11px] mb-2">
                    <span class="text-slate-400 uppercase">Scope Clarity</span>
                    <span class="text-primary">25/30 <span class="text-slate-600">[VAGUE_TERMS]</span></span>
                </div>
                <div class="h-1.5 w-full bg-slate-800 rounded-full overflow-hidden">
                    <div class="h-full bg-primary rounded-full glow-teal w-[83%]"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-[11px] mb-2">
                    <span class="text-slate-400 uppercase">Legal Compliance</span>
                    <span class="text-primary">18/20 <span class="text-slate-600">[MINOR_GAP]</span></span>
                </div>
                <div class="h-1.5 w-full bg-slate-800 rounded-full overflow-hidden">
                    <div class="h-full bg-primary rounded-full glow-teal w-[90%]"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-[11px] mb-2">
                    <span class="text-slate-400 uppercase">Timeline Feasibility</span>
                    <span class="text-amber-500">06/10 <span class="text-slate-600">[AGGRESSIVE]</span></span>
                </div>
                <div class="h-1.5 w-full bg-slate-800 rounded-full overflow-hidden">
                    <div class="h-full bg-amber-500 rounded-full w-[60%]"></div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">

</script>