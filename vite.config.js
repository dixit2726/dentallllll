import { defineConfig } from 'vite';

// ──────────────────────────────────────────────────────────────
// Vite Config – Surya Dental
// Configured for GitHub Pages deployment under:
//   https://dixit2726.github.io/suryaaadental/
// ──────────────────────────────────────────────────────────────

export default defineConfig({
  // ▶ Matches the actual GitHub repo name exactly
  base: '/dentallllll/',

  build: {
    outDir: 'dist',
    assetsDir: 'assets',
    emptyOutDir: true,

    // asset inlining threshold - inline small assets
    assetsInlineLimit: 4096,

    // Source maps for debugging (disable for production)
    sourcemap: false,

    rollupOptions: {
      output: {
        // Clean chunk naming
        chunkFileNames: 'assets/js/[name]-[hash].js',
        entryFileNames: 'assets/js/[name]-[hash].js',
        assetFileNames: 'assets/[ext]/[name]-[hash].[ext]',
      },
    },
  },

  // CSS config
  css: {
    devSourcemap: true,
  },

  // Preview server settings
  preview: {
    port: 4173,
    strictPort: true,
  },

  // Dev server
  server: {
    port: 5173,
    open: true,
  },
});
