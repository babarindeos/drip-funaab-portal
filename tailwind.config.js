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
                seyi: ['Inter', 'sans-serif'],
                serif: ['Lato', 'serif'],
            },
            width: {
                '3/5': '60%', // 3/5 equals 60%
                '2/5': '40%',
                '1/2': '50%',
                '1/4': '25%',
                '3/4': '65%'
              },
            spacing: {
                '12': '3rem'
            },
        },
    },

    plugins: [forms],
};
