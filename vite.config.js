import { defineConfig } from 'vite';

// ──────────────────────────────────────────────────────────────
// Vite Config – Vijaya Dental
// Configured for GitHub Pages deployment under:
//   https://dixit2726.github.io/vijayadental/
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
      input: {
        main: 'index.html',
        about: 'about.html',
        treatments: 'treatments.html',
        reviews: 'reviews.html',
        gallery: 'gallery.html',
        location: 'location.html',
        appointment: 'appointment.html',
        invisalign: 'invisalign.html',
        braces: 'braces.html',
        smile_designing: 'smile-designing.html',
        dental_implants: 'dental-implants.html',
        root_canal: 'root-canal.html',
        crowns_bridges: 'crowns-bridges.html',
        flap_surgery: 'flap-surgery.html',
        teeth_whitening: 'teeth-whitening.html',
        impacted_teeth: 'impacted-teeth.html',
        full_mouth_rehabilitation: 'full-mouth-rehabilitation.html',
      },
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
