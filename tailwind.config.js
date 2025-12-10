import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
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
                primary: {
                    50: '#e9e8ff',
                    100: '#c7c5ff',
                    200: '#a39fff',
                    300: '#7f78ff',
                    400: '#6058ff',
                    500: '#3f36f7',
                    600: '#3b30e0',
                    700: '#2f27b3',
                    800: '#231d80',
                    900: '#17144d',
                },
                gray: {
                    750: '#24303f',
                    950: '#121418',
                },
            },
        },
    },

    plugins: [forms],
};
