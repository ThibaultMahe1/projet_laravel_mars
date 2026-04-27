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
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                mars: {
                    50: '#fff3ec',
                    100: '#ffe4d3',
                    200: '#ffc5a5',
                    300: '#ff9f6d',
                    400: '#ff6f31',
                    500: '#fd4d0c',
                    600: '#ed3403',
                    700: '#c52304',
                    800: '#9c1c0b',
                    900: '#7e1a0d',
                    950: '#440a04',
                },
            },
        },
    },

    plugins: [forms],
};
