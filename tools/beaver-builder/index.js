#!/usr/bin/env node

/*
Recreation of make command
## Beaver Builder
BB_PLUGIN_DIRECTORY = htdocs/app/plugins/pc-bb-modules
BB_CUSTOM_MODULE_DIRECTORY = modules
BB_COMPONENT_TEMPLATE_DIRECTORY = _TEMPLATE_FOR_MODULES
BB_COMPONENT_TEMPLATE_MODULE_CLASS_NAME = PC_EXAMPLE_Module
BB_COMPONENT_TEMPLATE_FILE_NAME = example.php
BB_COMPONENT_MODULE_LOADER_DIRECTORY = classes
BB_COMPONENT_MODULE_LOADER_FILE_NAME = class-pc-bb-modules-loader.php
BB_COMPONENT_MODULE_LOADER_FUNCTION_NAME = load_modules

#---COMPONENT--------------------------------------
bbcm:
	@${eval}CLASS_NAME=PC_${shell}echo "${NAME}" | tr '-' '_' | tr '[:lower:]' '[:upper:]')_Module)
	cp -r ${local_directory}/${BB_PLUGIN_DIRECTORY}/${BB_CUSTOM_MODULE_DIRECTORY}/${BB_COMPONENT_TEMPLATE_DIRECTORY} ${local_directory}/${BB_PLUGIN_DIRECTORY}/${BB_CUSTOM_MODULE_DIRECTORY}/${NAME}
	mv ${local_directory}/${BB_PLUGIN_DIRECTORY}/${BB_CUSTOM_MODULE_DIRECTORY}/${NAME}/${BB_COMPONENT_TEMPLATE_FILE_NAME} ${local_directory}/${BB_PLUGIN_DIRECTORY}/${BB_CUSTOM_MODULE_DIRECTORY}/${NAME}/${NAME}.php
	sed -i '' 's/${BB_COMPONENT_TEMPLATE_MODULE_CLASS_NAME}/${CLASS_NAME}/g; s/${BB_COMPONENT_TEMPLATE_DIRECTORY}/${NAME}/g' ${local_directory}/${BB_PLUGIN_DIRECTORY}/${BB_CUSTOM_MODULE_DIRECTORY}/${NAME}/${NAME}.php
	echo ".fl-module-${NAME} {}" >> ${STYLESHEET_DIRECTORY}/${STYLESHEET_MODULE_DIRECTORY}/_${NAME}.scss
	echo "@import '${STYLESHEET_MODULE_DIRECTORY}/${NAME}';" >> ${STYLESHEET_DIRECTORY}/${STYLESHEET_MAIN_FILE}
    # sed -i '' '/function ${BB_COMPONENT_MODULE_LOADER_FUNCTION_NAME}/ a\ require_once PC_MODULES_DIR . "${BB_CUSTOM_MODULE_DIRECTORY}/${NAME}/${NAME}.php"' ${BB_PLUGIN_DIRECTORY}/${BB_COMPONENT_MODULE_LOADER_DIRECTORY}/${BB_COMPONENT_MODULE_LOADER_FILE_NAME}
*/
const BB_PLUGIN_DIRECTORY = 'htdocs/app/plugins/pc-bb-modules'
const BB_CUSTOM_MODULE_DIRECTORY = 'modules'
const BB_COMPONENT_TEMPLATE_DIRECTORY = '_TEMPLATE_FOR_MODULES'
const BB_COMPONENT_TEMPLATE_MODULE_CLASS_NAME = 'PC_EXAMPLE_Module'
const BB_COMPONENT_TEMPLATE_FILE_NAME = 'example.php'
const BB_COMPONENT_MODULE_LOADER_DIRECTORY = 'classes'
const BB_COMPONENT_MODULE_LOADER_FILE_NAME = 'class-pc-bb-modules-loader.php'
const BB_COMPONENT_MODULE_LOADER_FUNCTION_NAME = 'load_modules'
const STYLESHEET_DIRECTORY = 'build/stylesheets'
const STYLESHEET_MAIN_FILE = 'main.scss'
const STYLESHEET_MODULE_DIRECTORY = 'modules'

const program = require('commander')

const util = require('util')
const exec = require('child_process').execSync

let local_directory = require('child_process').execSync('pwd')

program
    .command('add <NAME>')
    .description('add a beaver builder component')
    .action(function (NAME, cmd) {
        local_directory = local_directory.toString().trim()
        const CLASS_NAME = `PC_${NAME.replace(/-/gi, '_').toUpperCase()}_Module`
        const copy_template_folder = `cp -R ${local_directory}/${BB_PLUGIN_DIRECTORY}/${BB_CUSTOM_MODULE_DIRECTORY}/${BB_COMPONENT_TEMPLATE_DIRECTORY} ${local_directory}/${BB_PLUGIN_DIRECTORY}/${BB_CUSTOM_MODULE_DIRECTORY}/${NAME}`
        const rename_module_file = `mv ${local_directory}/${BB_PLUGIN_DIRECTORY}/${BB_CUSTOM_MODULE_DIRECTORY}/${NAME}/${BB_COMPONENT_TEMPLATE_FILE_NAME} ${local_directory}/${BB_PLUGIN_DIRECTORY}/${BB_CUSTOM_MODULE_DIRECTORY}/${NAME}/${NAME}.php`
        const add_template_names = `sed -i '' 's/${BB_COMPONENT_TEMPLATE_MODULE_CLASS_NAME}/${CLASS_NAME}/g; s/${BB_COMPONENT_TEMPLATE_DIRECTORY}/${NAME}/g' ${local_directory}/${BB_PLUGIN_DIRECTORY}/${BB_CUSTOM_MODULE_DIRECTORY}/${NAME}/${NAME}.php`
        const add_scss_partial = `echo ".fl-module-${NAME} {}" >> ${STYLESHEET_DIRECTORY}/${STYLESHEET_MODULE_DIRECTORY}/_${NAME}.scss`
        const add_scss_partial_to_main = `echo "@import '${STYLESHEET_MODULE_DIRECTORY}/${NAME}';" >> ${STYLESHEET_DIRECTORY}/${STYLESHEET_MAIN_FILE}`

        try {
            exec(copy_template_folder)
            exec(rename_module_file)
            exec(add_template_names)
            exec(add_scss_partial)
            exec(add_scss_partial_to_main)
        } catch(e) {
            console.log('Fail!!')
        }
    })

program.parse(process.argv)
