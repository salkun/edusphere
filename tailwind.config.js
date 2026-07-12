import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                indigo: {
                    50: 'rgb(var(--indigo-50) / <alpha-value>)',
                    100: 'rgb(var(--indigo-100) / <alpha-value>)',
                    200: 'rgb(var(--indigo-200) / <alpha-value>)',
                    300: 'rgb(var(--indigo-300) / <alpha-value>)',
                    400: 'rgb(var(--indigo-400) / <alpha-value>)',
                    500: 'rgb(var(--indigo-500) / <alpha-value>)',
                    600: 'rgb(var(--indigo-600) / <alpha-value>)',
                    700: 'rgb(var(--indigo-700) / <alpha-value>)',
                    800: 'rgb(var(--indigo-800) / <alpha-value>)',
                    900: 'rgb(var(--indigo-900) / <alpha-value>)',
                    950: 'rgb(var(--indigo-950) / <alpha-value>)',
                }
            }
        },
    },

    plugins: [forms],
};
