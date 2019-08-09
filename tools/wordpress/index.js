#!/usr/bin/env node

const program = require('commander')
const { pullDb, pushDb } = require('./db')
const { pullUploads, pushUploads } = require('./uploads')
const { deploy } = require('./deploy')

require('dotenv').config()
const WP_ENV = process.env.WP_ENV || 'local'
const WP_HOME = process.env.WP_HOME
const DB_PREFIX = process.env.DB_PREFIX

let local_directory = require('child_process').execSync('pwd').toString().trim()

/**
 * The following could easily come from a JSON file
 */
let environments = {}

const ENVIRONMENTS_JSON_PATH = `${local_directory}/.environments.json`
// Check for environments.json
try {
    require('fs').statSync(ENVIRONMENTS_JSON_PATH)

    environments = {
        ...environments,
        ...require(ENVIRONMENTS_JSON_PATH)
    }

    environments.LOCAL.db_name = process.env.DB_NAME

} catch (error) {
    // If the file simply doesn't exist, no sweat
    if (error.code === 'ENOENT')
        console.info('Failed to load .environments.json file.')
    else
        console.warn(error)
}


program
    .command('pull [type] <environment>')
    .action(function (type, environment) {
        const destination = environments.LOCAL
        environment = environment.toString().toUpperCase()

        let source = environments[environment]

        if (!type || type === 'db') 
            pullDb(
                source.db_name, 
                source, 
                destination
            )

        if (!type || type == 'uploads')
            pullUploads(
                'htdocs/app/uploads',
                source,
                {
                    root_directory: local_directory
                }
            )
    })

program
    .command('push [type] <environment> [branch]')
    .action(function (type, environment, branch) {
        const source = environments.LOCAL
        environment = environment.toString().toUpperCase()
        
        let destination = environments[environment]

        if (!branch)
            branch = destination['branch'] || 'master'

        if (!type || type === 'code')
            deploy(
                destination,
                branch
            )

        if (type === 'db')
            pushDb(
                source.db_name,
                source,
                destination
            )

        if (type == 'uploads')
            pushUploads(
                'htdocs/app/uploads',
                {
                    root_directory: local_directory
                },
                destination,
            )
    })

program.parse(process.argv)
