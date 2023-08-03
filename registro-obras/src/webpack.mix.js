const mix = require('laravel-mix');

const TargetsPlugin = require('targets-webpack-plugin');

mix.webpackConfig({
    plugins: [
        new TargetsPlugin({
          browsers: ['last 2 versions', 'chrome >= 41', 'IE 11'],
    }),
]});

mix.js('resources/js/app.js', 'public/js')
mix.sass('resources/sass/app.scss', 'public/css');

mix.js('resources/js/login.js', 'public/js');
mix.js('resources/js/storage.js', 'public/js');
mix.js('resources/js/sadaic.js', 'public/js');
mix.js('resources/js/work.register.js', 'public/js');
mix.js('resources/js/jingle.register.js', 'public/js');
mix.js('resources/js/file.uploader.js', 'public/js');
mix.js('resources/js/city.selector.js', 'public/js');
mix.js('resources/js/agency.input.js', 'public/js');
