#!/usr/bin/env node

const BLOCKS_PLUGIN_DIRECTORY = 'htdocs/app/plugins/pc-custom-blocks'
const BLOCKS_BUILD_DIRECTORY = 'build/javascripts/blocks'
const BLOCKS_EXAMPLE_JS = '_example.js'
const BLOCKS_EXAMPLE_TEMPLATE = 'example-component'
const BLOCKS_EXAMPLE_CLASS = 'example_component'
const BLOCKS_REGISTER_PHP = 'pc-custom-blocks'
const BLOCKS_EXAMPLE_PHP = '_example-register.txt'
const STYLESHEET_DIRECTORY = 'build/stylesheets'
const STYLESHEET_BLOCKS_DIRECTORY = 'blocks'
const STYLESHEET_MAIN_FILE = 'blocks.scss'

const program = require('commander')

const util = require('util')
const exec = require('child_process').execSync

let local_directory = require('child_process').execSync('pwd')

program
	.command('add <NAME>')
	.description('add new gutenberg block')
	.action(function(NAME, cmd) {
		local_directory = local_directory.toString().trim()
		const BLOCK_SLUG = `${NAME.replace(/-/gi, '_').toLowerCase()}`
		const JS_BLOCK_SLUG = `${NAME.replace(/_/gi, '-').toLowerCase()}`
		console.log(`${BLOCKS_PLUGIN_DIRECTORY}/${BLOCKS_EXAMPLE_PHP}`)
		const copy_template_file = `cp ${local_directory}/${BLOCKS_BUILD_DIRECTORY}/${BLOCKS_EXAMPLE_JS} ${local_directory}/${BLOCKS_BUILD_DIRECTORY}/_${NAME}.js`
		const add_template_names = `sed -i '' 's/${BLOCKS_EXAMPLE_TEMPLATE}/${BLOCK_SLUG}/g; s/${BLOCKS_EXAMPLE_TEMPLATE}/${NAME}/g' ${local_directory}/${BLOCKS_BUILD_DIRECTORY}/_${NAME}.js`
		const add_register_block = `cat ${local_directory}/${BLOCKS_PLUGIN_DIRECTORY}/${BLOCKS_EXAMPLE_PHP} >> ${local_directory}/${BLOCKS_PLUGIN_DIRECTORY}/${BLOCKS_REGISTER_PHP}.php`
		const change_block_names = `sed -i '' 's/${BLOCKS_EXAMPLE_CLASS}/${BLOCK_SLUG}/g; s/${BLOCKS_EXAMPLE_TEMPLATE}/${JS_BLOCK_SLUG}/g' ${local_directory}/${BLOCKS_PLUGIN_DIRECTORY}/${BLOCKS_REGISTER_PHP}.php`
		const add_scss_partial = `echo ".wp-block-${NAME}-main {}" >> ${STYLESHEET_DIRECTORY}/${STYLESHEET_BLOCKS_DIRECTORY}/_${NAME}.scss`
		const add_scss_partial_to_main = `echo "@import '${STYLESHEET_BLOCKS_DIRECTORY}/_${NAME}';" >> ${STYLESHEET_DIRECTORY}/${STYLESHEET_MAIN_FILE}`
		console.log(add_register_block)
		try {
			exec(copy_template_file)
			exec(add_template_names)
			exec(add_register_block)
			exec(change_block_names)
			exec(add_scss_partial)
			exec(add_scss_partial_to_main)
		} catch (e) {
			console.log('Fail!! You did NOT make a custom Gutenberg block.')
		}
	})

program.parse(process.argv)
