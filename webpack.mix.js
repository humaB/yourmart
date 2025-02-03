const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

 mix.js('resources/js/app.js', 'public/assets/js')
 mix.js('resources/js/productApp.js', 'public/assets/js')
 mix.js('resources/js/inventoryApp.js', 'public/assets/js')
 mix.js('resources/js/accountApp.js', 'public/assets/js')
 .vue();

    //Version only customerApp.js
mix.version([
    'public/assets/js/app.js',
    'public/assets/js/productApp.js',
    'public/assets/js/inventoryApp.js',
    'public/assets/js/accountApp.js',
]);

