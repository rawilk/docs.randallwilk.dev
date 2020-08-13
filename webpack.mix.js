const mix = require('laravel-mix');
require('laravel-mix-tailwind');

mix.js('resources/js/app.js', 'js')
    .sass('resources/sass/app.scss', 'css')
    .tailwind('./tailwind.config.js')
    .disableNotifications();

if (mix.inProduction()) {
    mix.version();
} else {
    mix.sourceMaps();
}
