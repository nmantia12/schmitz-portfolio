#!/usr/bin/env node
const { execSync } = require('child_process')
const program = require('commander')
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
    .command('ssh <environment>')
    .action(function (environment) {
        environment = environment.toString().toUpperCase()
        const { 
            connect: DESTINATION_CONNECT,
            root_directory: DESTINATION_ROOT_DIRECTORY,  
        } = environments[environment]
        execSync(`
            ssh -t ${DESTINATION_CONNECT} 'cd ${DESTINATION_ROOT_DIRECTORY}; bash -l'
        `, {stdio: [0, 1, 2] })
    })

program.parse(process.argv)
