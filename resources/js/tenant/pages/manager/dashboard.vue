<script setup lang="ts">
import { ref } from 'vue';
import { useUIStore } from '@tenant/store/ui';

const ui = useUIStore();

// Estado de expansión (Simulamos que el segundo proyecto está abierto para mostrar el diseño)
const expandedProjectId = ref<string | null>('TC-9104-Y'); 

const toggleDetails = (id: string) => {
    expandedProjectId.value = expandedProjectId.value === id ? null : id;
};

// Datos simulados (Adaptados para alimentar la nueva tabla)
const projects = [
    {
        id: 'TC-8821-X',
        title: 'Smart Grid Expansion v2',
        entity: 'Dept. of Infrastructure',
        value: '$4.2M',
        status: 'high', // high | analysis | low
        verdict: 'High Viability'
    },
    {
        id: 'TC-9104-Y',
        title: 'Neural Hub Licensing',
        entity: 'National Tech Registry',
        value: '$12.5M',
        status: 'analysis',
        verdict: 'In Analysis'
    },
    {
        id: 'TC-4402-Z',
        title: 'Quantum Link Infrastructure',
        entity: 'Telecom Defense Agency',
        value: '$32.1M',
        status: 'low',
        verdict: 'Low Viability'
    },
    {
        id: 'TC-1100-W',
        title: 'Urban Air Mobility Mesh',
        entity: 'City Planning Office',
        value: '$1.8M',
        status: 'high',
        verdict: 'High Viability'
    }
];
</script>

<template>
    <div class="max-w-[1600px] mx-auto space-y-6 animate-in fade-in duration-500">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="glass-panel p-6 rounded-lg relative overflow-hidden group hover:border-cyber-teal/30 transition-colors">
                <div class="relative z-10">
                    <p class="text-[10px] font-mono font-bold text-slate-tech uppercase tracking-widest mb-1">Total Pipeline Budget</p>
                    <h2 class="text-3xl font-mono font-bold text-white mb-4">$42,890,200.00</h2>
                    <div class="flex items-center gap-2">
                        <div class="h-1 w-20 bg-cyber-teal rounded"></div>
                        <span class="text-[10px] text-cyber-teal font-bold">+12.4% VS PREV QUARTER</span>
                    </div>
                </div>
                <span class="material-symbols-outlined absolute right-[-10px] bottom-[-10px] text-[150px] text-white/5 group-hover:text-cyber-teal/5 transition-colors pointer-events-none">payments</span>
            </div>

            <div class="glass-panel p-6 rounded-lg flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-mono font-bold text-slate-tech uppercase tracking-widest mb-1">Mean AI Viability Score</p>
                    <h2 class="text-3xl font-mono font-bold text-emerald-accent mb-1">84.2%</h2>
                    <p class="text-[10px] text-slate-400 font-medium">OPTIMAL STRATEGIC WINDOW</p>
                </div>
                <div class="relative w-16 h-16">
                    <svg class="w-full h-full transform -rotate-90">
                        <circle class="text-slate-800" cx="32" cy="32" fill="transparent" r="28" stroke="currentColor" stroke-width="4"></circle>
                        <circle class="text-emerald-accent" cx="32" cy="32" fill="transparent" r="28" stroke="currentColor" stroke-dasharray="175.9" stroke-dashoffset="27.8" stroke-width="4"></circle>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="material-symbols-outlined text-emerald-accent text-sm">auto_awesome</span>
                    </div>
                </div>
            </div>

            <div class="glass-panel p-6 rounded-lg">
                <p class="text-[10px] font-mono font-bold text-slate-tech uppercase tracking-widest mb-1">Neural Processing Throughput</p>
                <h2 class="text-3xl font-mono font-bold text-white mb-4">1,240 <span class="text-sm text-slate-500">PPS</span></h2>
                <div class="flex items-end gap-1 h-10">
                    <div class="w-full bg-slate-800 h-2 rounded-t"></div>
                    <div class="w-full bg-slate-800 h-4 rounded-t"></div>
                    <div class="w-full bg-cyber-teal/50 h-8 rounded-t"></div>
                    <div class="w-full bg-slate-800 h-6 rounded-t"></div>
                    <div class="w-full bg-slate-800 h-3 rounded-t"></div>
                    <div class="w-full bg-slate-800 h-5 rounded-t"></div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
            
            <div class="xl:col-span-3 space-y-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-cyber-teal">analytics</span>
                        <h3 class="text-lg font-bold text-white uppercase tracking-wider">Tender Workbench</h3>
                    </div>
                    <button class="text-[10px] font-mono border border-white/10 px-3 py-1 rounded hover:bg-white/5 transition-colors uppercase tracking-widest text-slate-tech">
                        Matrix_Config
                    </button>
                </div>

                <div class="glass-panel rounded-lg overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-white/5 border-b border-white/10">
                            <tr>
                                <th class="px-6 py-3 text-[10px] font-mono font-bold text-slate-500 uppercase">Project / Entity</th>
                                <th class="px-6 py-3 text-[10px] font-mono font-bold text-slate-500 uppercase text-right">Value</th>
                                <th class="px-6 py-3 text-[10px] font-mono font-bold text-slate-500 uppercase">AI Verdict</th>
                                <th class="px-6 py-3 text-[10px] font-mono font-bold text-slate-500 uppercase text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
    <template v-for="project in projects" :key="project.id">
        
        <tr 
            @click="toggleDetails(project.id)"
            :class="[
                'transition-all group cursor-pointer border-l-4',
                // LÓGICA DE CLASES ACTUALIZADA:
                expandedProjectId === project.id 
                    ? 'bg-cyber-teal/[0.03] border-l-cyber-teal border-y border-cyber-teal/20' 
                    : project.status === 'low'
                        // Aquí aplicamos EXACTAMENTE tus clases de rojo nativo
                        ? 'bg-red-500/5 hover:bg-red-500/10 border-l-transparent'
                        : 'border-l-transparent hover:bg-white/5'
            ]"
        >
            <td class="px-6 py-5">
                <div :class="['text-[10px] font-mono mb-1', project.status === 'low' ? 'text-red-400' : 'text-cyber-teal']">
                    #{{ project.id }}
                </div>
                <div class="text-sm font-bold text-white">{{ project.title }}</div>
                <div class="text-[10px] text-slate-500 uppercase">{{ project.entity }}</div>
            </td>

            <td class="px-6 py-5 text-right font-mono font-bold text-white text-lg">
                {{ project.value }}
            </td>

            <td class="px-6 py-5">
                <div class="flex items-center">
                    <span :class="[
                        'inline-block px-3 py-1 rounded-full text-[9px] font-bold tracking-tighter uppercase border',
                        project.status === 'high' ? 'bg-emerald-accent/10 border-emerald-accent/20 text-emerald-accent status-glow-emerald' :
                        project.status === 'analysis' ? 'bg-cyber-teal/10 border-cyber-teal/30 text-cyber-teal status-glow-teal' :
                        // Estilo exacto del snippet para LOW
                        'bg-red-500/10 border-red-500/20 text-red-500'
                    ]">
                        {{ project.verdict }}
                    </span>
                    
                    <span v-if="project.status === 'low'" class="ml-2 text-red-500 font-bold animate-pulse">
                        !
                    </span>
                </div>
            </td>

            <td class="px-6 py-5 text-right">
                <span v-if="project.status === 'low'" class="inline-block">
                     <span class="material-symbols-outlined text-red-500/80">warning</span>
                </span>
                
                <span v-else :class="['material-symbols-outlined transition-transform duration-300', expandedProjectId === project.id ? 'text-cyber-teal rotate-90' : 'text-slate-600 group-hover:text-cyber-teal']">
                    chevron_right
                </span>
            </td>
        </tr>

        <tr v-if="expandedProjectId === project.id" class="bg-cyber-teal/[0.03] border-l-4 border-l-cyber-teal animate-in slide-in-from-top-2 duration-200">
            <td class="px-6 pb-6 pt-2" colspan="4">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[10px] font-mono text-slate-500 uppercase tracking-widest">Comparative Analysis Metrics</span>
                    <span class="text-[9px] font-mono text-slate-600">REF_ID: COMP_MATRIX_{{ project.id }}</span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-navy-deep/40 border border-white/10 p-4 rounded-md">
                        <p class="text-[9px] text-slate-500 uppercase mb-3">Competitor Saturation</p>
                        <div class="flex items-end justify-between">
                            <span class="text-xl font-bold text-white">Low</span>
                            <span class="text-[10px] text-emerald-accent font-mono">2 Active Bidders</span>
                        </div>
                        <div class="w-full bg-slate-800 h-1 mt-2 rounded">
                            <div class="bg-emerald-accent h-1 w-1/4 rounded"></div>
                        </div>
                    </div>
                    <div class="bg-navy-deep/40 border border-white/10 p-4 rounded-md">
                        <p class="text-[9px] text-slate-500 uppercase mb-3">Technical Alignment</p>
                        <div class="flex items-end justify-between">
                            <span class="text-xl font-bold text-white">92%</span>
                            <span class="text-[10px] text-cyber-teal font-mono">+12% over avg</span>
                        </div>
                        <div class="w-full bg-slate-800 h-1 mt-2 rounded">
                            <div class="bg-cyber-teal h-1 w-[92%] rounded"></div>
                        </div>
                    </div>
                    <div class="bg-navy-deep/40 border border-white/10 p-4 rounded-md">
                        <p class="text-[9px] text-slate-500 uppercase mb-3">Projected Margin</p>
                        <div class="flex items-end justify-between">
                            <span class="text-xl font-bold text-white">18.4%</span>
                            <span class="text-[10px] text-slate-500 font-mono">Target: 15%</span>
                        </div>
                        <div class="w-full bg-slate-800 h-1 mt-2 rounded">
                            <div class="bg-emerald-accent h-1 w-2/3 rounded"></div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4 mt-6">
                    <button class="bg-cyber-teal/20 text-cyber-teal text-[10px] font-bold px-4 py-2 rounded uppercase hover:bg-cyber-teal/30 transition-colors border border-cyber-teal/30">Download Comparative PDF</button>
                    <button class="bg-white/5 text-slate-400 text-[10px] font-bold px-4 py-2 rounded uppercase hover:bg-white/10 border border-white/10 transition-colors">Compare with Global Benchmark</button>
                </div>
            </td>
        </tr>
    </template>
</tbody>
                    </table>
                    
                    <div class="px-6 py-3 bg-white/5 flex items-center justify-between border-t border-white/10">
                        <div class="text-[10px] font-mono text-slate-500">SHOWING 4 OF 128 QUANTUM ENTRIES</div>
                        <div class="flex gap-4">
                            <button class="text-[10px] font-mono text-cyber-teal uppercase tracking-widest hover:underline">Previous_Block</button>
                            <button class="text-[10px] font-mono text-cyber-teal uppercase tracking-widest hover:underline">Next_Block</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                
                <div class="glass-panel p-6 rounded-lg border-l-4 border-cyber-teal">
                    <div class="flex items-center gap-2 mb-6">
                        <span class="material-symbols-outlined text-cyber-teal text-base">compare_arrows</span>
                        <h4 class="text-[10px] font-bold text-white uppercase tracking-widest">Benchmarking Data</h4>
                    </div>
                    <div class="space-y-6">
                        <div>
                            <div class="flex justify-between text-[10px] font-mono mb-2">
                                <span class="text-slate-400">ENTITY SUCCESS RATE</span>
                                <span class="text-white">64%</span>
                            </div>
                            <div class="w-full bg-slate-800 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-emerald-accent h-full w-[64%]"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-[10px] font-mono mb-2">
                                <span class="text-slate-400">AVG COMPETITOR BID</span>
                                <span class="text-white">$11.8M</span>
                            </div>
                            <div class="w-full bg-slate-800 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-cyber-teal h-full w-[45%]"></div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-8 pt-6 border-t border-white/10">
                        <p class="text-[9px] font-mono text-emerald-accent uppercase mb-2">AI Tactical Advice</p>
                        <p class="text-[11px] italic text-slate-400 leading-relaxed font-sans">
                            "Selected tender #TC-9104-Y aligns with historical 'Telecom' clusters where your hit-rate is 22% higher than the industry baseline."
                        </p>
                    </div>
                </div>

                <div 
                    @click="ui.openIngest()"
                    class="glass-panel p-6 rounded-lg border-2 border-dashed border-slate-700 hover:border-cyber-teal/40 transition-all group cursor-pointer"
                >
                    <div class="flex items-center gap-2 mb-6">
                        <span class="material-symbols-outlined text-slate-500 text-base group-hover:text-cyber-teal transition-colors">memory</span>
                        <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest group-hover:text-cyber-teal transition-colors">Neural Input</h4>
                    </div>
                    <div class="text-center py-4">
                        <div class="w-12 h-12 bg-cyber-teal/10 rounded flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-cyber-teal text-2xl">upload_file</span>
                        </div>
                        <h5 class="text-white font-bold text-sm mb-1">Inject PDF Data</h5>
                        <p class="text-[9px] font-mono text-slate-500 mb-6">PARSING RATIO: 1.4GB/SEC</p>
                        <button class="w-full py-2 border border-cyber-teal/30 text-cyber-teal text-[10px] font-bold uppercase tracking-widest rounded hover:bg-cyber-teal/10 transition-all">Select_Source_Nodes</button>
                    </div>
                </div>

                <div class="glass-panel p-4 rounded-lg bg-navy-deep/80">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-white/5 rounded">
                            <span class="material-symbols-outlined text-slate-400 text-sm">description</span>
                        </div>
                        <div class="flex-1">
                            <div class="text-[10px] font-mono text-white mb-1">tender_specs_v4.pdf</div>
                            <div class="w-full bg-slate-800 h-1 rounded-full overflow-hidden">
                                <div class="bg-cyber-teal h-full w-[78%]"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<style scoped>
@reference "../../../../css/app.css";

/* Glassmorphism utility */
.glass-panel {
    @apply bg-navy-light/40 backdrop-blur-xl border border-white/10;
}

/* Efectos de Glow Específicos */
.status-glow-emerald {
    box-shadow: 0 0 10px rgba(16, 185, 129, 0.15);
}
.status-glow-teal {
    box-shadow: 0 0 10px rgba(13, 242, 242, 0.15);
}
.status-glow-crimson {
    box-shadow: 0 0 10px rgba(239, 68, 68, 0.15);
}
</style>