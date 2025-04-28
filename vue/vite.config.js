import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'path'

// Путь для сборки прямо в install/components/.../templates/.default/dist/
const COMPONENT_DIST = resolve(__dirname, '../install/components/mycompany/vuecounter/templates/.default/dist/');

export default defineConfig({
  base: '/local/components/mycompany/vuecounter/templates/.default/dist/',
  plugins: [vue()],
  build: {
    outDir: COMPONENT_DIST,
    assetsDir: 'assets',
    emptyOutDir: true,
    rollupOptions: {
      input: '/src/main.js',
      output: {
        entryFileNames: `assets/[name].js`,
        chunkFileNames: `assets/[name].js`,
        assetFileNames: `assets/[name].[ext]`
      }
    }
  }
});
