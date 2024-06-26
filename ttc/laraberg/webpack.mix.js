let mix = require('laravel-mix')

require('./nova.mix')

mix
  .setPublicPath('dist')
  .js('resources/js/field.js', 'js')
  .vue({ version: 3 })
  .sass('resources/css/field.scss', 'css')
  .nova('ttc/laraberg')

mix.override((webpackConfig) => {
  webpackConfig.resolve.modules = [
    "node_modules",
    __dirname + "../../../vendor/spatie/laravel-medialibrary-pro/resources/js",
  ];
})
