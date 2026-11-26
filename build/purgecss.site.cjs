/*
 * Bootstrap allégé pour le site public (le portail MY EVC garde la feuille complète).
 * Régénérer après toute modification des gabarits du site :
 *   npx --yes purgecss@8.0.0 --config build/purgecss.site.cjs
 */
module.exports = {
  css: ['public/assets/vendor/bootswatch-cerulean/bootstrap.min.css'],
  content: [
    'resources/views/layouts/site.blade.php',
    'resources/views/site/**/*.blade.php',
    'public/assets/site/js/*.js',
    'public/assets/vendor/bootstrap/bootstrap.bundle.min.js',
  ],
  output: 'public/assets/site/css/bootstrap.site.css',
  variables: true,
  keyframes: true,
  fontFace: true,
  safelist: {
    standard: ['show', 'showing', 'hiding', 'fade', 'collapsing', 'collapsed', 'active', 'disabled', 'was-validated', 'is-invalid', 'is-valid', 'modal-open', 'modal-backdrop', 'offcanvas-backdrop', 'dropdown-menu-end', 'carousel-item-start', 'carousel-item-end', 'carousel-item-next', 'carousel-item-prev', 'invalid-feedback', 'valid-feedback', 'visually-hidden'],
    greedy: [/^data-bs-/, /^bs-/, /popper/, /^\[hidden\]/],
  },
};
