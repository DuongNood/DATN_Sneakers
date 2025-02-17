import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";
import tailwindcss from "@tailwindcss/vite";

// https://vite.dev/config/
export default defineConfig({
  plugins: [react(), tailwindcss()],
  // dùng cổng 3000 cho thân thiện với người dùng
  server: {
    port: 3000,
  },
});
