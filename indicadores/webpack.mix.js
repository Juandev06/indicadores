const mix = require('laravel-mix');

mix.js("resources/js/app.js", "public/js/app.js")
    .js("resources/js/dashboard.js", "public/js/dashboard.js")
    .css("resources/css/app.css", "public/css/app.css")
    .version();
