import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

function withOpacity(variableName) {
    return ({ opacityValue }) => {
        if (opacityValue !== undefined) {
            return `rgba(var(${variableName}), ${opacityValue})`;
        }
        return `rgb(var(${variableName}))`;
    };
}

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
                sans: ['Outfit', 'Inter', ...defaultTheme.fontFamily.sans],
                mono: ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                supabase: {
                    'dark': withOpacity('--sb-dark'),
                    'surface': withOpacity('--sb-surface'),
                    'border': withOpacity('--sb-border'),
                    'input': withOpacity('--sb-input'),
                    'muted': withOpacity('--sb-muted'),
                    'accent': withOpacity('--sb-accent'),
                    'accent-hover': withOpacity('--sb-accent-hover'),
                }
            }
        },
    },

    plugins: [forms],
};
