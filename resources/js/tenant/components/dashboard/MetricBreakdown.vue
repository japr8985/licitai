<script setup>
const props = defineProps({
    breakdown: {
        type: Object,
        required: true
        // Espera: { financial_score: 35, technical_scope_score: 25, ... }
    }
});

// Configuración de métricas (Labels y Máximos)
const metrics = [
    { key: 'financial_score', label: 'Financial Health', max: 40, color: 'bg-primary', text: 'text-primary' },
    { key: 'technical_scope_score', label: 'Scope Clarity', max: 30, color: 'bg-blue-400', text: 'text-blue-400' },
    { key: 'legal_compliance_score', label: 'Legal Compliance', max: 20, color: 'bg-indigo-400', text: 'text-indigo-400' },
    { key: 'timeline_score', label: 'Timeline Feasibility', max: 10, color: 'bg-amber-500', text: 'text-amber-500' }
];

// Helper para calcular porcentaje CSS
const getPercentage = (value, max) => {
    if (!value) return 0;
    return Math.min(100, Math.max(0, (value / max) * 100));
};
</script>

<template>
    <div class="h-full glass-card rounded-xl p-6 font-mono flex flex-col justify-center">
        <h3 class="text-white font-bold text-sm uppercase tracking-widest mb-6 border-b border-white/10 pb-4 flex items-center justify-between">
            Metric Analysis
            <span class="text-primary/50 text-[10px]">[HEURISTIC_V2]</span>
        </h3>

        <div class="space-y-6">
            <div v-for="metric in metrics" :key="metric.key">
                <div class="flex justify-between text-[11px] mb-2 uppercase tracking-wide">
                    <span class="text-slate-400">{{ metric.label }}</span>
                    <span :class="metric.text" class="font-bold">
                        {{ breakdown[metric.key] || 0 }} <span class="text-slate-600">/ {{ metric.max }}</span>
                    </span>
                </div>

                <div class="h-2 w-full bg-slate-800 rounded-full overflow-hidden relative">
                    <div 
                        class="h-full rounded-full relative transition-all duration-1000 ease-out"
                        :class="`${metric.color} shadow-[0_0_10px_currentColor]`"
                        :style="{ width: `${getPercentage(breakdown[metric.key], metric.max)}%` }"
                    ></div>
                </div>
            </div>
        </div>
    </div>
</template>