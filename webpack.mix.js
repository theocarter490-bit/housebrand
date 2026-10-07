const {EnvironmentPlugin, IgnorePlugin} = require('webpack');
const mix = require('laravel-mix');
const glob = require('glob');
const path = require('path');

/*
 |--------------------------------------------------------------------------
 | Configure mix
 |--------------------------------------------------------------------------
 */

mix.options({
    resourceRoot: process.env.ASSET_URL || undefined,
    processCssUrls: false,
    postCss: [require('autoprefixer')]
});


/*
 |--------------------------------------------------------------------------
 | Configure Webpack
 |--------------------------------------------------------------------------
 */

mix.webpackConfig({
    output: {
        publicPath: process.env.ASSET_URL || undefined,
        libraryTarget: 'umd'
    },
    resolve: {
        alias: {
            Vue: 'vue/dist/vue.js'
        }
    },
    plugins: [
        new IgnorePlugin({
            checkResource(resource, context) {
                return [
                    path.join(__dirname, 'resources/assets/vendor/libs/@form-validation')
                ].some(pathToIgnore => resource.startsWith(pathToIgnore));
            }
        }),
        new EnvironmentPlugin({
            BASE_URL: process.env.ASSET_URL ? `${process.env.ASSET_URL}/` : '/'
        })
    ],
    module: {
        rules: [
            {
                test: /\.js$/,
                include: [
                    path.join(__dirname, 'node_modules/bootstrap/'),
                    path.join(__dirname, 'node_modules/popper.js/'),
                    path.join(__dirname, 'node_modules/shepherd.js/')
                ],
                loader: 'babel-loader',
                options: {
                    presets: [['@babel/preset-env', {targets: 'last 2 versions, ie >= 10'}]],
                    plugins: [
                        '@babel/plugin-transform-destructuring',
                        '@babel/plugin-proposal-object-rest-spread',
                        '@babel/plugin-transform-template-literals'
                    ],
                    babelrc: false
                }
            },
        ]
    },
    externals: {
        jQuery: 'jQuery',
        jquery: 'jQuery',
        moment: 'moment',
        jsdom: 'jsdom',
        velocity: 'Velocity',
        hammer: 'Hammer',
        pace: '"pace-progress"',
        chartist: 'Chartist',
        'popper.js': 'Popper',
        './blueimp-helper': 'jQuery',
        './blueimp-gallery': 'blueimpGallery',
        './blueimp-gallery-video': 'blueimpGallery'
    }
});

/*
 |--------------------------------------------------------------------------
 | Vendor assets
 |--------------------------------------------------------------------------
 */

function mixAssetsDir(query, cb) {
    (glob.sync('resources/assets/' + query) || []).forEach(f => {
        f = f.replace(/[\\\/]+/g, '/');
        cb(f, f.replace('resources/assets/', 'public/assets/'));
    });
}

// Core CSS files
mixAssetsDir('vendor/css/**/*.css', (src, dest) => mix.copy(src, dest));

// Core JavaScripts
mixAssetsDir('vendor/js/**/*.js', (src, dest) => mix.js(src, dest));

// Libs
mixAssetsDir('vendor/libs/**/*.js', (src, dest) => mix.js(src, dest));
mixAssetsDir('vendor/libs/**/*.css', (src, dest) => mix.copy(src, dest));
mixAssetsDir('vendor/libs/**/*.{png,jpg,jpeg,gif}', (src, dest) => mix.copy(src, dest));
// Copy task for form validation plugin as premium plugin don't have npm package
mixAssetsDir('vendor/libs/@form-validation/umd', (src, dest) => mix.copyDirectory(src, dest));

// Fonts
mixAssetsDir('vendor/fonts/*/*', (src, dest) => mix.copy(src, dest));
mixAssetsDir('vendor/fonts/*.css', (src, dest) => mix.copy(src, dest));
mixAssetsDir('audio/*.mp3', (src, dest) => mix.copy(src, dest));


/*
 |--------------------------------------------------------------------------
 | Application assets
 |--------------------------------------------------------------------------
 */

mixAssetsDir('js/**/*.js', (src, dest) => mix.scripts(src, dest));
mixAssetsDir('css/**/*.css', (src, dest) => mix.copy(src, dest));

mix.js('resources/js/bootstrap.js', 'public/js/');
mix.js('resources/js/timer-modal/index.js', 'public/js/timer-modal/');
mix.js('resources/js/chat/chat.js', 'public/js/chat/');
mix.js('resources/js/quick-shop-setting/*.js', 'public/js/quick-shop-setting/').vue({version: 2})
    .version();

mix.copy('node_modules/flag-icons/flags/1x1/*', 'public/assets/vendor/fonts/flags/1x1');
mix.copy('node_modules/flag-icons/flags/4x3/*', 'public/assets/vendor/fonts/flags/4x3');
mix.copy('node_modules/@fortawesome/fontawesome-free/webfonts/*', 'public/assets/vendor/fonts/fontawesome');
mix.copy('node_modules/katex/dist/fonts/*', 'public/assets/vendor/libs/quill/fonts');

mix.version();

/*
 |--------------------------------------------------------------------------
 | Browsersync Reloading
 |--------------------------------------------------------------------------
 */

// mix.browserSync('http://127.0.0.1:8000/');
