<script setup lang="ts">
import axios from 'axios';
import api from '@tenant/api/axios';
import { UserRole, ApiResponse, LoginResponse } from '@tenant/types/api';
import { ref } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '@tenant/store/auth';

const email = ref('');
const password = ref('');
const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

const handleLogin = async () => {
    try {
        const tenantId = route.params.tenant;
        
        await axios.get(`/app/${tenantId}/sanctum/csrf-cookie`);

        const res = await api.post<ApiResponse<LoginResponse>>('/login', {
            email: email.value,
            password: password.value
        });
        const { user } = res.data.content;

        authStore.setAuth(res.data.content)
        switch (user.role) {
            case UserRole.MANAGER:
                router.push({ name: 'manager.dashboard' });
                break;
            case UserRole.PROJECT_MANAGER:
                router.push({ name: 'pm.dashboard' });
                break;
            case UserRole.DEVELOPER:
                router.push({ name: 'dev.tasks' });
                break;
        }
    } catch (errorMsg) {
        console.log(errorMsg)
        // El interceptor ya nos pasó el mensaje de error directamente
        // errorMessage.value = errorMsg as string;
    }
};
</script>

<template>
    <div>
        <div class="mb-8 text-center md:text-left">
            <h2 class="text-2xl font-semibold text-white mb-2">Identify &amp; Authenticate</h2>
            <p class="text-sm text-slate-400">Please enter your secure credentials to proceed.</p>
        </div>
        <form class="space-y-6" @submit.prevent="handleLogin">
            <div class="space-y-1">
                <label class="block text-xs font-mono uppercase tracking-widest text-slate-400" for="email">Work
                    Identifier</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span
                            class="material-icons text-slate-500 text-lg group-focus-within:text-primary transition-colors">badge</span>
                    </div>
                    <input
                        class="block w-full pl-10 pr-3 py-3 bg-navy-deep/80 border border-slate-700 rounded-lg text-slate-200 placeholder-slate-600 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary input-tech transition-all duration-300 sm:text-sm font-mono"
                        id="email" placeholder="name@globalcorp.com" type="email" v-model="email"/>
                </div>
            </div>
            <div class="space-y-1">
                <div class="flex justify-between items-center">
                    <label class="block text-xs font-mono uppercase tracking-widest text-slate-400"
                        for="password">Security Key</label>
                </div>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span
                            class="material-icons text-slate-500 text-lg group-focus-within:text-primary transition-colors">vpn_key</span>
                    </div>
                    <input
                        class="block w-full pl-10 pr-3 py-3 bg-navy-deep/80 border border-slate-700 rounded-lg text-slate-200 placeholder-slate-600 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary input-tech transition-all duration-300 sm:text-sm font-mono"
                        id="password" placeholder="••••••••••••" type="password" v-model="password"/>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input class="h-4 w-4 rounded border-slate-700 text-primary focus:ring-primary bg-navy-deep/80"
                        id="remember-me" name="remember-me" type="checkbox" />
                    <label class="ml-2 block text-xs text-slate-400" for="remember-me">Remember
                        device</label>
                </div>
                <div class="text-xs">
                    <a class="font-medium text-primary hover:text-primary/80 transition-colors" href="#">Forgot key?</a>
                </div>
            </div>
            <div class="pt-2">
                <button
                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent rounded-lg text-sm font-semibold text-navy-deep bg-primary hover:bg-[#3bfbfb] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-primary transition-all duration-300 shadow-[0_0_20px_rgba(13,242,242,0.3)] hover:shadow-[0_0_30px_rgba(13,242,242,0.5)]"
                    type="submit">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <span
                            class="material-icons h-5 w-5 text-navy-deep/50 group-hover:text-navy-deep transition-colors">lock_open</span>
                    </span>
                    ESTABLISH CONNECTION
                </button>
            </div>
        </form>
        <div class="mt-8">
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-slate-700/50"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span
                        class="px-2 bg-navy-deep/20 backdrop-blur-md text-slate-500 text-xs uppercase tracking-wide">Or
                        verify via</span>
                </div>
            </div>
            <div class="mt-6">
                <button
                    class="w-full inline-flex justify-center items-center px-4 py-3 border border-slate-700 shadow-sm text-sm font-medium rounded-lg text-slate-300 bg-navy-light/50 hover:bg-navy-light hover:border-slate-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-primary transition-all duration-200"
                    type="button">
                    <span class="material-icons text-base mr-2">corporate_fare</span>
                    <span class="font-mono">SSO / SAML Login</span>
                </button>
            </div>
        </div>
    </div>
</template>