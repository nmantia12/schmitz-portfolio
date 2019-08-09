const webpack = require('webpack');
const glob = require('glob');
const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin')
const UglifyJsPlugin = require('uglifyjs-webpack-plugin')
const OptimizeCSSAssetsPlugin = require('optimize-css-assets-webpack-plugin')
const BrowserSyncPlugin = require('browser-sync-webpack-plugin')

const THEME_NAME = 'wp_base_install'
const publicPath = `/app/themes/${THEME_NAME}/assets/js/`
const OUTPUT_DIRECTORY = `htdocs${publicPath}`;

// grab environment name from .env
const ENV = require('dotenv').config()
const WP_ENV = process.env.WP_ENV || 'local'
const WP_HOME = process.env.WP_HOME || 'http://local.wp_base_install.com'

const PROXY_URL = WP_HOME

const blocksArray = glob.sync('./build/javascripts/blocks/*.js');
const blocksObject = blocksArray.reduce((acc, item) => {
	// console.log(item)
	let name = item.replace('./build/javascripts/blocks/_', '').replace(".js", '');
	acc[name] = [item];
	return acc;
}, {});

const mainJs = { main: ['./build/javascripts/index.js', './build/stylesheets/main.scss'] }
const blockCSS = { blocks: ['./build/stylesheets/blocks.scss'] }
blocksObject['main'] = mainJs['main']
blocksObject['blocks'] = blockCSS['blocks']

// dynamically choose mode if none provided by command
const mode = ['staging', 'prod'].indexOf(WP_ENV) < 0 ? 'development' : 'production'

// const entry = {
// 	main: ['./build/javascripts/index.js'],
// 	blocks: ['./build/javascripts/blocks.js']
// }
const entry = blocksObject

const output = {
	path: path.join(__dirname, OUTPUT_DIRECTORY),
	publicPath,
	filename: '[name].js'
}

const plugins = [
	new MiniCssExtractPlugin({
		filename: '../css/[name].css'
	}),
	// new BrowserSyncPlugin({
	//     proxy: WP_HOME
	// })
	// new BrowserSyncPlugin({
	// 	host: 'localhost',
	// 	port: 9005,
	// 	server: { baseDir: [OUTPUT_DIRECTORY] }
	// })
	new BrowserSyncPlugin({
		host: 'localhost',
		port: 9005,
		proxy: PROXY_URL,
		files: ['**/**/**/**/*.php'],
		reloadDelay: 0
	})
]

const optimization = {
	splitChunks: {
		// chunks: 'all',
		// maxInitialRequests: Infinity,
		// minSize: 0,
		cacheGroups: {
			commons: {
				name: 'commons',
				chunks: 'initial',
				minChunks: 2
			}
		}
	}
}

let config = {
	mode,
	entry,
	plugins,
	output,
	optimization,
	resolve: {
		alias: {
			styles: path.resolve(__dirname, 'build/stylesheets/', 'build/javascripts/'),
			scripts: path.resolve(__dirname, 'build/javascripts/'),
			'@': path.resolve(__dirname, 'build/')
		}
	},
	module: {
		rules: [
			{
				test: /\.js$/,
				loader: 'babel-loader',
				exclude: /node_modules/
			},
			{
				test: /\.css$/,
				use: [
					MiniCssExtractPlugin.loader,
					{
						loader: 'css-loader',
						options: {
							url: false
						}
					}
				]
			},
			{
				test: /\.eot(\?v=\d+\.\d+\.\d+)?$/,
				loader: 'file-loader'
			},
			{
				test: /\.(woff|woff2)$/,
				loader: 'url-loader?prefix=font/&limit=5000'
			},
			{
				test: /\.ttf(\?v=\d+\.\d+\.\d+)?$/,
				loader: 'url-loader?limit=1000&mimetype=application/octet-stream'
			},
			{
				test: /\.svg(\?v=\d+\.\d+\.\d+)?$/,
				loader: 'url-loader?limit=1000&mimetype=image/svg+xml'
			},
			{
				test: /\.(scss)$/,
				use: [
					MiniCssExtractPlugin.loader,
					{
						loader: 'css-loader', // translates CSS into CommonJS modules
						options: {
							url: false
						}
					},
					{
						loader: 'sass-loader' // compiles Sass to CSS
					}
				]
			}
		]
	}
}

if (mode === 'production') {
    config.optimization = {
        minimizer: [
            new UglifyJsPlugin({
                cache: true,
                parallel: true,
                sourceMap: true // set to true if you want JS source maps
            }),
            new OptimizeCSSAssetsPlugin({})
        ]
    }

    config.plugins.push(new OptimizeCSSAssetsPlugin())
    config.plugins.push(new UglifyJsPlugin())
}

module.exports = function(env, argv) {
    if (env && env.production) config.mode = 'production'
    if (argv && argv['mode']) config.mode = argv['mode']
    console.log(`Running in ${config.mode} mode`)
    return config
}
