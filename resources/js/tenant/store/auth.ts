import { LoginResponse, User, Tenant } from '@tenant/types/api';
import { defineStore } from 'pinia';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(sessionStorage.getItem('user') || 'null'),
    tenant: JSON.parse(sessionStorage.getItem('tenant') || 'null'),
    token: sessionStorage.getItem('token') || null,
  }),
  getters: {
    isAuthenticated: (state) => !!state.token,
    companyName: (state) => state.tenant?.company_name || 'LicitAI Node',
  },
  actions: {
    setAuth(payload: LoginResponse) {
      this.user = payload.user
      this.tenant = payload.tenant;
      this.token = payload.token;

      sessionStorage.setItem('user', JSON.stringify(payload.user));
      sessionStorage.setItem('tenant', JSON.stringify(payload.tenant));
      sessionStorage.setItem('token', payload.token);
    },
    logout() {
      this.user = null;
      this.tenant = null;
      this.token = null;
      sessionStorage.clear();
    },
    updateTenantInfo(newInfo: Tenant) {
      this.tenant = { ...this.tenant, ...newInfo };
      sessionStorage.setItem('tenant', JSON.stringify(this.tenant));
    }
  }
});