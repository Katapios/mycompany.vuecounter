import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
  plugins: [vue()],
  build: {
    outDir: path.resolve(__dirname, '../js/vuecounter'),
    emptyOutDir: true,
    rollupOptions: {
      input: path.resolve(__dirname, 'main.js'),
      output: {
        entryFileNames: 'app.js',  // чтобы главный файл был красивым
      }
    }
  },
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './'),
    }
  }
});
