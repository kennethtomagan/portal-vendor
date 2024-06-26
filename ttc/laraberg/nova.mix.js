const mix = require('laravel-mix')
const webpack = require('webpack')
const path = require('path')

class NovaExtension {
    name() {
        return 'nova-extension'
    }

    register(name) {
        this.name = name
    }

    webpackPlugins() {
        return new webpack.ProvidePlugin({
            _: 'lodash',
            Errors: 'form-backend-validation',
        })
    }


    webpackConfig(webpackConfig) {
        webpackConfig.externals = {
            vue: 'Vue',
        }

        webpackConfig.resolve.alias = {
            ...(webpackConfig.resolve.alias || {}),
            'laravel-nova': path.join(
                __dirname,
                '../../vendor/laravel/nova/resources/js/mixins/packages.js'
            ),
            '@': path.join(
                __dirname,
                '../../vendor/laravel/nova/resources/js'
            ),
            '@spatie': path.join(
                __dirname,
                '../../vendor/spatie/laravel-medialibrary-pro/resources/js'
            ), //this is needed for all of the native nova fields to compile successfully
        }

        webpackConfig.output = {
            uniqueName: this.name,
        }
    }
}

mix.extend('nova', new NovaExtension())
