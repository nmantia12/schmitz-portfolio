/**
 * 
 * @param {string} UPLOADS_PATH the path to the WordPress uploads
 * @param {object} source object containing the connection string, root directory, and url of the source
 * @param {object} destination object containing the connection string, root directory, and url of the destination 
 */
function pullUploads(
    UPLOADS_PATH,
    {
        connect: SOURCE_CONNECT,
        root_directory: SOURCE_ROOT_DIRECTORY,
    },
    {
        root_directory: DESTINATION_ROOT_DIRECTORY,
    }
) {

    try {
        require('child_process').execSync(`
            rsync -avz ${SOURCE_CONNECT}:${SOURCE_ROOT_DIRECTORY}/${UPLOADS_PATH} ${DESTINATION_ROOT_DIRECTORY}/${require('path').dirname(UPLOADS_PATH)} 
        `, { stdio: [0, 1, 2] })
    } catch (error) {
        console.error(error)
    }
}

/**
 * 
 * @param {string} UPLOADS_PATH the path to the WordPress uploads
 * @param {object} source object containing root directory of the source
 * @param {object} destination object containing the connection string, root directory, and url of the destination 
 */
function pushUploads(
    UPLOADS_PATH,
    {
        root_directory: SOURCE_ROOT_DIRECTORY,
    },
    {
        connect: DESTINATION_CONNECT,
        root_directory: DESTINATION_ROOT_DIRECTORY,
    },
) {

    try {
        require('child_process').execSync(`
            rsync -avz ${SOURCE_ROOT_DIRECTORY}/${UPLOADS_PATH}  ${DESTINATION_CONNECT}:${DESTINATION_ROOT_DIRECTORY}/${require('path').dirname(UPLOADS_PATH)}
        `, { stdio: [0, 1, 2] })
    } catch (error) {
        console.error(error)
    }
}

module.exports = {
    pullUploads,
    pushUploads,
}
