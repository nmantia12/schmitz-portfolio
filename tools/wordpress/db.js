const { execSync } = require('child_process')

/**
 * Pull the database
 * @param {string} DB_NAME name of the database being pulled from 
 * @param {object} source object containing the connection string, root directory, and url of the source
 * @param {object} destination object containing the connection string, root directory, and url of the destination
 */
function pullDb(
    DB_NAME,
    source,
    destination
) {

    console.log(`Importing database from ${source.url} into ${destination.url}`)
    moveDb(
        DB_NAME,
        source,
        destination
    )
}


/**
 * Push the database
 * @param {string} DB_NAME name of the database being pulled from 
 * @param {object} source object containing the connection string, root directory, and url of the source
 * @param {object} destination object containing the connection string, root directory, and url of the destination
 */
function pushDb(
   DB_NAME,
   source,
   destination 
) {
    console.log(`Exporting database from ${source.url} into ${destination.url}`)
    moveDb(
        DB_NAME,
        source,
        destination
    )
}

function moveDb(
    DB_NAME,
    source,
    destination
) {
    const {
        connect: SOURCE_CONNECT,
        root_directory: SOURCE_ROOT_DIRECTORY,
        url: SOURCE_URL
    } = source

    const {
        connect: DESTINATION_CONNECT,
        root_directory: DESTINATION_ROOT_DIRECTORY,
        url: DESTINATION_URL
    } = destination


    try {

        // get database from source
        if (isSSHConnection(SOURCE_CONNECT)) {
            sshGetDb(
                DB_NAME,
                source
            )
        } else {
            // otherwise we're in Docker world
            dockerGetDb(
                DB_NAME,
                source
            )
        }

        // replace database on destination
        if (isSSHConnection(DESTINATION_CONNECT)) {
            // copy database to destination
            execSync(`
                scp ./${DB_NAME}.sql.gz ${DESTINATION_CONNECT}:${DESTINATION_ROOT_DIRECTORY}/${DB_NAME}.sql.gz
            `, { stdio: [0, 1, 2] })

            sshReplaceDb(
                DB_NAME,
                source,
                destination
            )
        } else {
            // otherwise we're in Docker world
            dockerReplaceDb(
                DB_NAME,
                source,
                destination
            )
        }

        execSync(`
            rm ${DB_NAME}.sql.gz
        `)

    } catch (error) {
        console.error(error)
    }
}

/**
 * Dummy function to switch b/w ssh and Docker connections
 * @param {string} connect connection string
 */
function isSSHConnection(connect) {
    return connect.includes('@')
}

function sshGetDb(
    DB_NAME,
    {
        connect: SOURCE_CONNECT,
        root_directory: SOURCE_ROOT_DIRECTORY,
    },
) {
    console.log(`Connecting via SSH to ${SOURCE_CONNECT} to retrieve database...`)
    return execSync(`
        ssh ${SOURCE_CONNECT} "cd ${SOURCE_ROOT_DIRECTORY}/htdocs && wp db export --skip-lock-tables --allow-root -| gzip > ${SOURCE_ROOT_DIRECTORY}/${DB_NAME}.sql.gz"
        scp ${SOURCE_CONNECT}:${SOURCE_ROOT_DIRECTORY}/${DB_NAME}.sql.gz .
        ssh ${SOURCE_CONNECT} "rm -v ${SOURCE_ROOT_DIRECTORY}/${DB_NAME}.sql.gz"
    `, { stdio: [0, 1, 2] })
}

function sshReplaceDb(
    DB_NAME,
    source,
    destination
) {
    const {
        url: SOURCE_URL
    } = source
    const {
        connect: DESTINATION_CONNECT,
        root_directory: DESTINATION_ROOT_DIRECTORY,
        url: DESTINATION_URL
    } = destination

    console.log(`Connecting via SSH to ${DESTINATION_CONNECT} to replace database...`)
    return execSync(`
        ssh ${DESTINATION_CONNECT} "cd ${DESTINATION_ROOT_DIRECTORY}/htdocs && gunzip -c ${DESTINATION_ROOT_DIRECTORY}/${DB_NAME}.sql.gz | wp db import - && wp search-replace --url=${SOURCE_URL} ${SOURCE_URL} ${DESTINATION_URL}"
    `, { stdio: [0, 1, 2] })
}

function dockerReplaceDb(
    DB_NAME,
    {
        url: SOURCE_URL
    },
    destination
) {
    const {
        connect: DESTINATION_CONNECT,
        root_directory: DESTINATION_ROOT_DIRECTORY,
        url: DESTINATION_URL
    } = destination

    let wordpress_service = destination.wordpress || 'wordpress'

    let wordpress_container = execSync(`docker-compose ps | grep ${wordpress_service} | awk '{ print $1 }'`).toString().trim()

    console.log(`Replacing database via Docker...`)
    // copy file into volume space
    execSync(`
        cp ${DB_NAME}.sql.gz ./htdocs/wp/${DB_NAME}.sql.gz 
    `)

    // unzip the db dump
    execSync(`
        gunzip -kf ./htdocs/wp/${DB_NAME}.sql.gz
    `)


    
    // use wp-cli to import db
    execSync(`
        docker run --rm --volumes-from ${wordpress_container} --network container:${wordpress_container} wordpress:cli --path=${DESTINATION_ROOT_DIRECTORY}/htdocs/wp db import ${DESTINATION_ROOT_DIRECTORY}/htdocs/wp/${DB_NAME}.sql
    `, { stdio: [0, 1, 2] })

    // use wp-cli to search and replace source url with destination url
    execSync(`
        docker run --rm --volumes-from ${wordpress_container} --network container:${wordpress_container} wordpress:cli --path=${DESTINATION_ROOT_DIRECTORY}/htdocs/wp search-replace --url=${SOURCE_URL} ${SOURCE_URL} ${DESTINATION_URL}
    `, { stdio: [0, 1, 2] })

    // Remove decompressed file
    execSync(`
        rm ./htdocs/wp/${DB_NAME}.sql
        rm ./htdocs/wp/${DB_NAME}.sql.gz
    `)
}

function dockerGetDb(
    DB_NAME,
    source
) {
    const {
        root_directory: SOURCE_ROOT_DIRECTORY,
    } = source

    let wordpress_service = source.wordpress || 'wordpress'

    let wordpress_container = execSync(`docker-compose ps | grep ${wordpress_service} | awk '{ print $1 }'`).toString().trim()

    console.log(`Connecting via Docker to retrieve database...`)    
    // use wp-cli to export db
    execSync(`
        docker run --rm --volumes-from ${wordpress_container} --network container:${wordpress_container} wordpress:cli --path=${SOURCE_ROOT_DIRECTORY}/htdocs/wp db export ${SOURCE_ROOT_DIRECTORY}/htdocs/${DB_NAME}.sql
    `, { stdio: [0, 1, 2] })

    // move the db up
    execSync(`
        mv ./htdocs/${DB_NAME}.sql ./${DB_NAME}.sql
    `, { stdio: [0, 1, 2] })

    // gunzip db dump
    execSync(`
        gzip ./${DB_NAME}.sql
    `, { stdio: [0, 1, 2] })
}

module.exports = {
    pullDb,
    pushDb,
}
