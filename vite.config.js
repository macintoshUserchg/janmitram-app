import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
    ],
    // Use relative base path so dynamic imports and CSS preloads
    // resolve correctly when served from any subdirectory (e.g. /janmitram-app/).
    base: "",
});
