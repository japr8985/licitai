<script setup lang="ts">
import { RedFlag } from '@tenant/types/api';
import { computed } from 'vue';

const props = defineProps<{
    redflags: RedFlag[]
}>();
const quantity = computed(() => props.redflags?.length || 0);
const criticalQuantity = computed(() => props.redflags.filter(f => f.severity === 'HIGH').length);

const getSeverityClasses = (severity: string) => {
    switch (severity) {
        case 'HIGH':
            return {
                card: 'bg-red-500/5 border border-red-500/20 glow-red hover:border-red-500/40',
                titleText: 'text-red-400',
                icon: 'warning',
                iconColor: 'text-red-500',
                badgeText: 'text-red-400',
                borderTop: 'border-red-500/10'
            };
        case 'MODERATE':
            return {
                card: 'bg-amber-500/5 border border-amber-500/20 hover:border-amber-500/40',
                titleText: 'text-amber-400',
                icon: 'priority_high',
                iconColor: 'text-amber-500',
                badgeText: 'text-amber-400',
                borderTop: 'border-amber-500/10'
            };
        case 'LOW':
        default:
            return {
                card: 'bg-primary/5 border border-primary/20 hover:border-primary/40',
                titleText: 'text-primary',
                icon: 'info',
                iconColor: 'text-primary',
                badgeText: 'text-primary',
                borderTop: 'border-primary/10'
            };
    }
};
</script>
<template>
    <div class="h-full glass-card rounded-xl p-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-red-500">crisis_alert</span>
                <h3 class="text-white font-bold text-lg uppercase tracking-tight">
                    Financial Risk Radar
                </h3>
            </div>
            <div v-if="quantity > 0"
                class="text-[10px] bg-red-500/10 text-red-500 border border-red-500/30 px-2 py-1 rounded font-bold uppercase">
                {{ criticalQuantity }} Critical / {{ quantity }} Total
            </div>
            <div v-else
                class="text-[10px] bg-emerald-500/10 text-emerald-500 border border-emerald-500/30 px-2 py-1 rounded font-bold uppercase">
                ALL CLEAR
            </div>
        </div>

        <div v-if="quantity === 0" class="flex flex-col items-center justify-center py-10 opacity-50">
            <span class="material-symbols-outlined text-4xl text-emerald-500 mb-2">shield</span>
            <p class="text-xs font-mono text-emerald-500 uppercase tracking-widest">No significant risks detected</p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-for="(flag, index) in redflags" :key="index"
                class="rounded-lg p-4 flex flex-col justify-between transition-all group"
                :class="[getSeverityClasses(flag.severity).card, flag.severity === 'LOW' ? 'md:col-span-2' : '']">
                
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider"
                              :class="getSeverityClasses(flag.severity).titleText">
                            {{ flag.title }}
                        </span>
                        <span class="material-symbols-outlined text-sm"
                              :class="getSeverityClasses(flag.severity).iconColor">
                            {{ getSeverityClasses(flag.severity).icon }}
                        </span>
                    </div>
                    <p class="text-white font-mono text-sm font-bold">
                        {{ flag.description }}
                    </p>
                </div>

                <div class="mt-4 pt-4 border-t flex items-center justify-between"
                     :class="getSeverityClasses(flag.severity).borderTop">
                    <span class="text-slate-500 text-[10px]">{{ flag.reference }}</span>
                    <span class="text-[10px] font-bold"
                          :class="getSeverityClasses(flag.severity).badgeText">
                        {{ flag.severity }} RISK
                    </span>
                </div>
                
            </div>
        </div>
    </div>
</template>
<style scoped>
@reference "../../../../css/app.css";
.glass-card {
    @apply bg-slate-900/40 backdrop-blur-md border border-white/5;
}
.glow-red {
    box-shadow: 0 0 15px rgba(239, 68, 68, 0.05);
}
</style>