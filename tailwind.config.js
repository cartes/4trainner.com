import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: "class",
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./node_modules/flowbite/**/*.js",
    ],

    safelist: [
        "bg-red-500",
        "hover:bg-red-700",
        "bg-blue-500",
        "hover:bg-blue-700",
        "text-white",
        "font-bold",
        "rounded",
        "py-2",
        "px-5",
        "ml-2",
    ],

    theme: {
        extend: {
            colors: {
                primary: "#fa5700ff", // Saturated Orange
                "primary-dark": "#314554", // Navy from screenshot
                "background-light": "#F8FAFC",
                "background-dark": "#0F172A",
                aqua: {
                    50: "#f0f9fa",
                    100: "#dcf0f2",
                    200: "#bee1e6",
                    300: "#92cbd4",
                    400: "#5fa9b8",
                    500: "#438d9c",
                },
            },
            fontFamily: {
                display: ["Outfit", "sans-serif"],
                sans: ["Plus Jakarta Sans", "sans-serif"],
            },
            borderRadius: {
                DEFAULT: "1rem",
                "2xl": "1.5rem",
            },
        },
    },

    plugins: [require("@tailwindcss/forms"), require("flowbite/plugin")],
};
