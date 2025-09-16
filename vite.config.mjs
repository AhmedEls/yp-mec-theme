import { defineConfig } from 'vite'
import tailwindcss from '@tailwindcss/vite'
import { resolve } from 'path';
import fs from "fs";
import path from "path";

export default defineConfig(({ command, mode }) => {
  const isDev = command === 'serve';
  const useCDN = process.env.USE_CDN === 'true' || mode === 'production';

  console.log(`🚀 Building in ${mode} mode, CDN: ${useCDN ? 'enabled' : 'disabled'}`);

  return {
    base: 'https://cdn.jsdelivr.net/gh/AhmedEls/yp-mec-theme@alpha', // Keep base path simple
    publicDir: false, // Disable Vite’s public folder behavior
    plugins: [
      tailwindcss(),
      ...(useCDN ? [{
        name: 'cdn-manifest',
        writeBundle(options, bundle) {
          const manifestPath = path.join(options.dir, 'manifest.json');
          const cdnBase = 'https://cdn.jsdelivr.net/gh/AhmedEls/yp-mec-theme@alpha/dist/';

          // Read the generated manifest
          if (fs.existsSync(manifestPath)) {
            const manifest = JSON.parse(fs.readFileSync(manifestPath, 'utf-8'));

            // Update all file paths to use CDN
            Object.keys(manifest).forEach(key => {
              if (manifest[key].file) {
                // Convert relative path to CDN URL
                manifest[key].file = cdnBase + manifest[key].file;
              }
            });

            // Write the updated manifest
            fs.writeFileSync(manifestPath, JSON.stringify(manifest, null, 2));
            console.log('✅ Manifest updated with CDN URLs');
          }
        }
      }] : [])
    ],
    build: {
      outDir: 'dist',
      emptyOutDir: true,
      minify: useCDN ? 'esbuild' : false,
      sourcemap: true, // Enable source maps for easier debugging
      cssCodeSplit: true, // Split CSS into separate files
      chunkSizeWarningLimit: 500, // Increase chunk size limit to avoid warnings
      target: 'esnext', // Use modern JavaScript features
      reportCompressedSize: useCDN, // Disable size reporting for production builds
      assetsInlineLimit: 0, // Disable inlining of assets to avoid issues with large files
      cssMinify: useCDN, // Enable CSS minification
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
  }
})