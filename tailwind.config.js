import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.ts',
        './resources/**/*.vue',
    ],
    darkMode: 'class', // Importante para que el look dark de Stitch funcione
    theme: {
        extend: {
            colors: {
                // Colores inyectados desde el diseño de Stitch
                "primary": "#0df2f2",
                "primary-dark": "#0bbcb",
                "background-light": "#f5f8f8",
                "background-dark": "#102222",
                "navy-deep": "#0a192f",
                "navy-light": "#112240",
                "slate-tech": "#8892b0",
            },
            fontFamily: {
                "display": ["Inter", ...defaultTheme.fontFamily.sans],
                "mono": ["JetBrains Mono", ...defaultTheme.fontFamily.mono],
            },
            backgroundImage: {
                // El patrón de rejilla futurista
                'grid-pattern': "linear-gradient(to right, #1e293b 1px, transparent 1px), linear-gradient(to bottom, #1e293b 1px, transparent 1px)",
            }
        },
    },
    plugins: [forms],
};