import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [vue()],
  base: '/bitrix/js/mycompany.vuecounter/',
  build: {
    outDir: '../install/js/mycompany.vuecounter',
    emptyOutDir: true,
    rollupOptions: {
      input: './src/main.js',
      output: {
        entryFileNames: 'app.js',
        assetFileNames: '[name].[ext]'
      }
    }
  }
})
