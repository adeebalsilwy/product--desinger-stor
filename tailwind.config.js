import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
                bespoke: ["bespoke", ...defaultTheme.fontFamily.sans],
                main: ["main", ...defaultTheme.fontFamily.sans],
                secondary: ["secondary", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Ahlam's Girls Brand Colors - Professional Elegant Palette
                brand: {
                    primary: '#1a1a2e',      // Deep navy blue - elegance, sophistication
                    secondary: '#16213e',    // Rich navy - depth, professionalism
                    accent: '#e94560',       // Rose pink - feminine, energetic accent
                    bg: '#0f3460',           // Midnight blue - background depth
                    text: '#ffffff',         // Pure white - clarity, purity
                    gold: '#d4af37',         // Luxury gold - premium feel
                    rose: '#f8b4c8',         // Soft rose - gentle feminine touch
                    lavender: '#b19cd9',     // Soft lavender - creativity, imagination
                },
                green: {
                    50: "#ECFDF5",
                    100: "#D1FAE5",
                    200: "#A7F3D0",
                    300: "#6EE7B7",
                    400: "#34D399",
                    500: "#10B981", // This is the color you want
                    600: "#059669",
                    700: "#047857",
                    800: "#065F46",
                    900: "#064E3B",
                    950: "#022C22",
                },
            },
        },
    },

    plugins: [forms],
};
