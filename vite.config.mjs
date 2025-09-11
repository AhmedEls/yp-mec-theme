import { defineConfig } from 'vite'
import tailwindcss from '@tailwindcss/vite'
import { resolve } from 'path';

export default defineConfig({
  base: '/', // Keep base path simple
  publicDir: false, // Disable Vite’s public folder behavior
  plugins: [
    tailwindcss(),
  ],
  build: {
    outDir: 'dist',
    emptyOutDir: true,
    minify: 'esbuild',
    sourcemap: true, // Enable source maps for easier debugging
    cssCodeSplit: true, // Split CSS into separate files
    chunkSizeWarningLimit: 500, // Increase chunk size limit to avoid warnings
    target: 'esnext', // Use modern JavaScript features
    reportCompressedSize: false, // Disable size reporting for production builds
    assetsInlineLimit: 0, // Disable inlining of assets to avoid issues with large files
    cssMinify: true, // Enable CSS minification
    manifest: 'manifest.json', // puts manifest.json directly into /dist/
    rollupOptions: {
      input: {
        app: resolve(__dirname, 'assets/js/app.js'),
        styles: resolve(__dirname, 'assets/scss/app.scss'),
        tailwindcss: resolve(__dirname, 'assets/css/tailwind.css'),
        // Blocks
      },
      output: {
        entryFileNames: 'js/[name].[hash].js',
        assetFileNames: 'css/[name].[hash][extname]',
      },
    },
  },
})