let mix = require('laravel-mix')

require('./nova.mix')

mix
  .setPublicPath('dist')
  .js('resources/js/tool.js', 'js')
  .vue({ version: 3 })
  .css('resources/css/tool.css', 'css', [
      require("./node_modules/tailwindcss"),
  ])
  .nova('ttc/thingables')
