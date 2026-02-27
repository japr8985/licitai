import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/central/app.ts',
                'resources/js/tenant/app.ts'
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            '@central': path.resolve(__dirname, './resources/js/central'),
            '@tenant': path.resolve(__dirname, './resources/js/tenant'),
            'vue': 'vue/dist/vue.esm-bundler.js',
        },
    },
});