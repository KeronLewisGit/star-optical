import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Poppins', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    DEFAULT: '#184088',
                    dark: '#0f2c61',
                    soft: '#e8eef8',
                    ink: '#0b1526',
                },
                gold: {
                    DEFAULT: '#e09850',
                    dark: '#c47f35',
                    soft: '#fbf1e5',
                },
                wa: '#25d366',
            },
        },
    },

    plugins: [forms],
};
