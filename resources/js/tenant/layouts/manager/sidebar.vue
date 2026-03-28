<script setup lang="ts">
import { computed } from 'vue';
import { useUIStore } from '@tenant/store/ui';
import { useRoute } from 'vue-router';

const ui = useUIStore();
const route = useRoute();

const menuItems = [
  { name: 'Dashboard', route: 'manager.dashboard', icon: 'grid_view' },
  { name: 'Projects', route: 'manager.projects.index', icon: 'hub' },
  { name: 'Employees', route: 'manager.employees.index', icon: 'group' },
//   { name: 'Workbench', route: 'projects', icon: 'analytics' }, // Asumo ruta 'projects'
//   { name: 'Neural Chat', route: 'chat', icon: 'forum' },
//   { name: 'Settings', route: 'settings', icon: 'settings' },
];

const isCollapsed = computed(() => ui.sidebarCollapsed);

// 🧠 LOGICA INFALIBLE PARA EL ESTADO ACTIVO
const checkIsActive = (itemRoute: string) => {
  if (!route.name) return false;
  
  const currentRouteName = route.name.toString();
  
  // Para el Dashboard: Exigimos coincidencia EXACTA
  if (itemRoute === 'manager.dashboard') {
    return currentRouteName === 'manager.dashboard';
  }
  
  // Para Proyectos: Coincidencia PARCIAL 
  // (Así se mantiene iluminado en el index, en el show, edit, etc.)
  if (itemRoute === 'manager.projects.index') {
    return currentRouteName.startsWith('manager.projects');
  }

  return currentRouteName === itemRoute;
};
</script>

<template>
  <aside 
    :class="[
      'glass-panel border-r border-white/5 transition-all duration-300 h-full flex flex-col z-40', 
      isCollapsed ? 'w-20' : 'w-64'
    ]"
  >
    <nav class="flex-1 p-4 space-y-2 overflow-y-auto no-scrollbar">
      
      <button 
        @click="ui.openIngest()"
        :class="[
          'w-full flex items-center mb-6 transition-all duration-200 group',
          isCollapsed ? 'justify-center p-2 rounded-lg hover:bg-cyber-teal/10' : 'px-4 py-3 bg-cyber-teal/10 hover:bg-cyber-teal/20 border border-cyber-teal/30 rounded-lg'
        ]"
      >
        <span class="material-symbols-outlined text-cyber-teal group-hover:scale-110 transition-transform">add_circle</span>
        <span v-if="!isCollapsed" class="ml-3 text-xs font-bold font-mono text-cyber-teal uppercase tracking-wider">New Project</span>
      </button>

      <router-link 
        v-for="item in menuItems" 
        :key="item.name"
        :to="{ name: item.route }" 
        class="block"
      >
          <div :class="[
              'flex items-center rounded-lg transition-all duration-200 group relative',
              isCollapsed ? 'justify-center p-3' : 'px-4 py-3',
              checkIsActive(item.route)
                ? 'bg-cyber-teal/10 border-l-2 border-cyber-teal text-white' 
                : 'text-slate-tech hover:bg-white/5 hover:text-cyber-teal'
            ]">
              <span class="material-symbols-outlined">{{ item.icon }}</span>
              <span v-if="!isCollapsed" class="ml-3 text-xs font-medium">{{ item.name }}</span>
          </div>
      </router-link>

    </nav>

    <div class="p-4 border-t border-white/5 shrink-0 bg-[#0a192f]/50 backdrop-blur-md">
      <button 
        @click="ui.toggleSidebar()" 
        class="w-full flex items-center justify-center p-2 rounded-lg text-slate-tech hover:bg-white/5 hover:text-white transition-colors"
      >
        <span class="material-symbols-outlined transition-transform duration-300" :class="{ 'rotate-180': isCollapsed }">
          keyboard_double_arrow_left
        </span>
      </button>
    </div>
  </aside>
</template>

<style scoped>
@reference "../../../../css/app.css";

/* Estilos específicos del componente */
.glass-panel {
  @apply bg-[#0a192f]/80 backdrop-blur-xl;
}

/* Custom scrollbar minimalista para el nav */
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(136, 146, 176, 0.2);
  border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: rgba(13, 242, 242, 0.4);
}
</style>