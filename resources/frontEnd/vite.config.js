import { fileURLToPath, URL } from "node:url";
import { defineConfig, loadEnv } from "vite";
import vue from "@vitejs/plugin-vue";

export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), "");

  return {
    // Domain-agnostic by default. Override for a sub-path deployment.
    base: env.VITE_BASE_PATH || "/",
    plugins: [vue()],
    resolve: {
      alias: {
        "@": fileURLToPath(new URL("./src", import.meta.url)),
      },
    },
    build: {
      outDir: "../../public/build",
      emptyOutDir: true,
    },
    server: {
      host: true,
      port: 4174,
    },
    preview: {
      host: true,
      port: 4174,
    },
  };
});
