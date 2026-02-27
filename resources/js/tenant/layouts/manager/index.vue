<script setup lang="ts">
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import { Tenant, User } from '@tenant/types/api';
import { useAuthStore } from '@tenant/store/auth';
import { useUIStore } from '@tenant/store/ui';
import ManagerSidebar from './sidebar.vue';
import ProjectIngestModal from '@tenant/components/ProjectIngestModal.vue';
import ToastContainer from '@tenant/components/ToastContainer.vue';

const route = useRoute();
const auth = useAuthStore();
const ui = useUIStore();

const user = computed((): User | null => auth.user);
const tenant = computed((): Tenant | null => auth.tenant);
</script>

<template>
  <div class="h-screen w-full bg-navy-deep text-slate-100 tech-grid-bg font-display flex flex-col overflow-hidden">
    <ToastContainer />
    <header class="h-14 border-b border-white/10 glass-panel flex items-center justify-between px-6 shrink-0 z-50 relative">
      <div class="flex items-center gap-6">
        <div class="flex items-center gap-2">
          <div class="w-7 h-7 bg-cyber-teal/20 border border-cyber-teal/40 flex items-center justify-center rounded">
            <span class="material-symbols-outlined text-cyber-teal text-lg">hub</span>
          </div>
          <span class="text-sm font-bold tracking-widest text-white uppercase">
            Licit<span class="text-cyber-teal">AI</span>
          </span>
        </div>
        <div class="h-4 w-[1px] bg-white/10"></div>
        <div class="flex items-center gap-2 text-[10px] font-mono text-emerald-accent uppercase">
          <span class="w-1.5 h-1.5 rounded-full bg-emerald-accent animate-pulse"></span>
          NODE_STATUS: OPERATIONAL
        </div>
      </div>
      
      <div class="flex items-center hidden md:flex">
        <div class="flex items-center gap-2 text-[10px] font-mono tracking-widest">
          <span class="material-symbols-outlined text-sm">home</span>
          <span class="text-white/20">/</span>
          <span class="text-cyber-teal uppercase font-bold">{{ tenant?.company_name }}</span>
          <span class="text-white/20">/</span>
          <span class="text-white uppercase">{{ $route.meta.title || 'DASHBOARD' }}</span>
        </div>
      </div>

      <div class="flex items-center gap-4">
        <div class="px-3 py-1 rounded bg-navy-deep/60 border border-white/5 text-[10px] font-mono text-slate-tech hidden sm:block">
          {{ tenant?.company_name }}
        </div>
        <div class="flex items-center gap-3 pl-4 border-l border-white/10">
          <div class="text-right hidden md:block">
            <div class="text-[10px] font-mono text-slate-tech uppercase">Usuario</div>
            <div class="text-[11px] font-mono text-cyber-teal">{{ user?.name }}</div>
          </div>
          <div class="w-8 h-8 rounded-full bg-gradient-to-br from-cyber-teal to-blue-600 p-[1px] cursor-pointer hover:shadow-lg hover:shadow-cyber-teal/20 transition-all">
            <div class="w-full h-full rounded-full bg-navy-deep flex items-center justify-center">
              <span class="material-symbols-outlined text-sm">person</span>
            </div>
          </div>
        </div>
      </div>
    </header>
    
    <div class="flex flex-1 overflow-hidden relative">
      <ManagerSidebar />

      <main class="flex-grow p-6 overflow-y-auto custom-scrollbar relative">
        <slot />
        
        <div class="fixed bottom-4 right-6 pointer-events-none z-50">
           <div class="pointer-events-auto px-4 py-2 glass-panel rounded-lg flex items-center gap-3 border border-white/5 shadow-xl bg-navy-deep/90 backdrop-blur-md">
              <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-accent opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-accent"></span>
              </span>
              <span class="text-[10px] font-mono text-slate-tech uppercase tracking-wider">System Synced</span>
           </div>
        </div>
      </main>
    </div>

    <ProjectIngestModal 
      :is-open="ui.isIngestModalOpen" 
      @close="ui.isIngestModalOpen = false" 
    />

  </div>
</template>

<style scoped>
@reference "../../../../css/app.css";

.tech-grid-bg {
  background-image: radial-gradient(circle at 2px 2px, rgba(13, 242, 242, 0.03) 1px, transparent 0);
  background-size: 32px 32px;
}
.glass-panel {
    @apply bg-navy-light/60 backdrop-blur-md;
}
/* Scrollbar global para el main content */
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: rgba(10, 25, 47, 0.5);
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(136, 146, 176, 0.2);
  border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: rgba(13, 242, 242, 0.4);
}
</style>