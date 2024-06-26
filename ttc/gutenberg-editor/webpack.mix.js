let mix = require('laravel-mix');

// const CopyPlugin = require('copy-webpack-plugin');
// const path = require('path');

// module.exports = {
//     plugins: [
//         new CopyPlugin({
//             patterns: [
//                 {
//                     from: path.resolve(__dirname, 'node_modules/@automattic/isolated-block-editor/build-browser/isolated-block-editor.js'),
//                     to: path.resolve(__dirname, '../../public_html/vendor/editor/resources/js/editor.js')
//                 },
//             ],
//         }),
//     ],
// };

// mix.webpackConfig({
//     resolve: {
//         alias: {
//             // Add your alias
//             'WPComponents': path.resolve(__dirname, 'node_modules/@wordpress/components/build-style/style.css')
//         }
//     }
// });

mix
    .setPublicPath('../../public_html/vendor/editor')
    .js('./resources/js/tool.js', 'js')
    .css('./resources/css/tool.css', 'css')
;
