import { defineStore } from 'pinia';
export interface Toast {
  id: string;
  type: 'success' | 'error' | 'warning' | 'info';
  title?: string;
  message: string;
  timeout?: number;
}
export const useUIStore = defineStore('ui', {
  state: () => ({
    sidebarCollapsed: false,
    isIngestModalOpen: false,

    // Global Loading (System Status)
    isSystemLoading: false,
    systemStatusMessage: 'SYSTEM SYNCED', // Mensaje por defecto

    // Notifications
    toasts: [] as Toast[],
  }),
  actions: {
    toggleSidebar() {
      this.sidebarCollapsed = !this.sidebarCollapsed;
    },
    openIngest() {
      this.isIngestModalOpen = true;
    },
    // --- LOADING SYSTEM ---
    startLoading(message: string = 'PROCESSING DATA STREAM...') {
      this.isSystemLoading = true;
      this.systemStatusMessage = message;
    },
    stopLoading() {
      this.isSystemLoading = false;
      this.systemStatusMessage = 'SYSTEM SYNCED';
    },
    // --- TOAST SYSTEM ---
    notify(type: Toast['type'], message: string, title?: string) {
      const id = Date.now().toString(36) + Math.random().toString(36).substr(2);

      const toast: Toast = { id, type, message, title };

      this.toasts.push(toast);

      // Auto-remove
      setTimeout(() => {
        this.removeToast(id);
      }, 5000);
    },

    removeToast(id: string) {
      this.toasts = this.toasts.filter(t => t.id !== id);
    }
  }
});