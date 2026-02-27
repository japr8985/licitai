<script setup lang="ts">
</script>
<template>
    <div class="h-full glass-card rounded-xl p-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-red-500">crisis_alert</span>
                <h3 class="text-white font-bold text-lg uppercase tracking-tight">Financial Risk Radar
                </h3>
            </div>
            <div
                class="text-[10px] bg-red-500/10 text-red-500 border border-red-500/30 px-2 py-1 rounded font-bold uppercase">
                3 Critical Flags</div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div
                class="bg-red-500/5 border border-red-500/20 rounded-lg p-4 glow-red flex flex-col justify-between group hover:border-red-500/40 transition-all">
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-red-400 text-[10px] font-bold uppercase tracking-wider">Monetary
                            Penalty</span>
                        <span class="material-symbols-outlined text-red-500 text-sm">warning</span>
                    </div>
                    <p class="text-white font-mono text-sm font-bold">Penalty of $5,000 USD per 15 min
                        delay in uptime SLA.</p>
                </div>
                <div class="mt-4 pt-4 border-t border-red-500/10 flex items-center justify-between">
                    <span class="text-slate-500 text-[10px]">Clause 14.2.1</span>
                    <span class="text-red-400 text-[10px] font-bold">HIGH RISK</span>
                </div>
            </div>
            <div
                class="bg-slate-800/20 border border-white/5 rounded-lg p-4 flex flex-col justify-between hover:border-white/20 transition-all">
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Uncapped
                            Liability</span>
                        <span class="material-symbols-outlined text-amber-500 text-sm">priority_high</span>
                    </div>
                    <p class="text-white font-mono text-sm font-bold">Indemnity clause lacks aggregate
                        liability cap for data breaches.</p>
                </div>
                <div class="mt-4 pt-4 border-t border-white/5 flex items-center justify-between">
                    <span class="text-slate-500 text-[10px]">Section 8: Legal</span>
                    <span class="text-amber-400 text-[10px] font-bold">MEDIUM RISK</span>
                </div>
            </div>
            <div
                class="bg-slate-800/20 border border-white/5 rounded-lg p-4 flex flex-col justify-between md:col-span-2">
                <div class="flex items-center gap-4">
                    <div class="bg-primary/10 p-2 rounded">
                        <span class="material-symbols-outlined text-primary text-xl">payments</span>
                    </div>
                    <div>
                        <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">
                            Performance Bond</p>
                        <p class="text-white font-mono text-sm font-bold">15% Performance Bond required
                            within 5 days of signing.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>