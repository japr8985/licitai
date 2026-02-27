<template>
    <div class="h-full glass-card rounded-xl p-6 relative overflow-hidden">
        <div class="absolute top-0 right-0 p-4 font-mono text-[10px] text-primary/20 pointer-events-none">
            PLAN_REV: 0.9.4a
        </div>
        <div class="flex items-center gap-3 mb-10 border-b border-white/10 pb-4">
            <span class="material-symbols-outlined text-amber-500">route</span>
            <h3 class="text-white font-bold text-lg uppercase tracking-tight">AI Suggested Roadmap /
                Sprints</h3>
        </div>
        <div class="relative pl-8 space-y-10">
            <div class="absolute left-[11px] top-2 bottom-2 w-px timeline-line"></div>
            <div class="relative flex flex-col md:flex-row md:items-start justify-between gap-4">
                <div
                    class="absolute -left-10 mt-1 size-6 bg-primary rounded border border-background-dark flex items-center justify-center z-10 shadow-[0_0_10px_rgba(13,242,242,0.5)]">
                    <span class="material-symbols-outlined text-slate-900 text-xs font-black">check</span>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-1">
                        <span class="text-primary font-mono text-[10px] uppercase font-bold tracking-widest">Phase
                            1</span>
                        <h4 class="text-white font-bold text-base uppercase">Inception &amp; Discovery
                        </h4>
                    </div>
                    <p class="text-slate-400 text-xs font-mono">Infrastructure audit, stakeholder
                        mapping, and final migration architecture sign-off.</p>
                </div>
                <div class="flex items-center gap-4 min-w-[200px] justify-end">
                    <div class="px-3 py-1 bg-slate-900 border border-white/10 rounded flex items-center gap-2">
                        <span class="text-[10px] text-slate-500 font-bold uppercase">Sprint 1-2</span>
                        <div class="h-3 w-px bg-white/10"></div>
                        <span class="text-[10px] text-primary font-bold">120 HOURS</span>
                    </div>
                    <div class="w-24 h-1 bg-slate-800 rounded-full overflow-hidden">
                        <div class="h-full bg-primary w-full"></div>
                    </div>
                </div>
            </div>
            <div class="relative flex flex-col md:flex-row md:items-start justify-between gap-4">
                <div
                    class="absolute -left-10 mt-1 size-6 bg-slate-900 border border-white/30 rounded flex items-center justify-center z-10">
                    <span class="material-symbols-outlined text-white/50 text-xs font-black">hourglass_top</span>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-1">
                        <span class="text-slate-400 font-mono text-[10px] uppercase font-bold tracking-widest">Phase
                            2</span>
                        <h4 class="text-white font-bold text-base uppercase">Core Infrastructure Setup
                        </h4>
                    </div>
                    <p class="text-slate-400 text-xs font-mono">Deployment of landing zones, hybrid
                        connectivity, and baseline security controls.</p>
                </div>
                <div class="flex items-center gap-4 min-w-[200px] justify-end">
                    <div class="px-3 py-1 bg-slate-900 border border-white/10 rounded flex items-center gap-2">
                        <span class="text-[10px] text-slate-500 font-bold uppercase">Sprint 3-5</span>
                        <div class="h-3 w-px bg-white/10"></div>
                        <span class="text-[10px] text-primary font-bold">280 HOURS</span>
                    </div>
                    <div class="w-24 h-1 bg-slate-800 rounded-full overflow-hidden">
                        <div class="h-full bg-primary/30 w-[15%]"></div>
                    </div>
                </div>
            </div>
            <div class="relative flex flex-col md:flex-row md:items-start justify-between gap-4">
                <div
                    class="absolute -left-10 mt-1 size-6 bg-slate-900 border border-white/10 rounded flex items-center justify-center z-10">
                    <span class="material-symbols-outlined text-white/20 text-xs font-black">lock_open</span>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-1">
                        <span class="text-slate-600 font-mono text-[10px] uppercase font-bold tracking-widest">Phase
                            3</span>
                        <h4 class="text-slate-500 font-bold text-base uppercase">Application Migration
                        </h4>
                    </div>
                    <p class="text-slate-600 text-xs font-mono italic">Workload prioritization and live
                        traffic transition to hybrid cluster.</p>
                </div>
                <div class="flex items-center gap-4 min-w-[200px] justify-end opacity-40">
                    <div class="px-3 py-1 bg-slate-900 border border-white/10 rounded flex items-center gap-2">
                        <span class="text-[10px] text-slate-600 font-bold uppercase">Sprint 6-12</span>
                        <div class="h-3 w-px bg-white/10"></div>
                        <span class="text-[10px] text-slate-600 font-bold">650 HOURS</span>
                    </div>
                    <div class="w-24 h-1 bg-slate-800 rounded-full overflow-hidden">
                        <div class="h-full bg-primary/10 w-0"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup lang="ts"></script>
<style>
.timeline-line {
    /* Crea una línea de 1px de ancho */
    width: 1px;
    /* El truco: un gradiente que es 50% color y 50% transparente */
    background: linear-gradient(to bottom, #0df2f2 50%, transparent 50%);
    /* Define el tamaño del patrón (1px ancho, 12px alto para que se repita) */
    background-size: 1px 12px;
    /* Opcional: opacidad para que no compita con el texto */
    opacity: 0.7;
}
</style>