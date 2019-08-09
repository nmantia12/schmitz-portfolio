const { execSync } = require('child_process')
const path = require('path')

/**
 * Deploy a WordPress Site using rsync and git
 * @param {Object} destination the environment you're deploying to
 * @param {String} branch the name of the branch you want to deploy
 */
function deploy(
	{
		connect: CONNECT,
		root_directory: DEPLOY_PATH,
		asset_path: ASSET_PATH,
		url
	},
	branch
) {
	console.log(`Pushing to ${CONNECT}\n`)
	// in the future, this can become a loop in an array.
	// we'll want to check whether the path is an array or a string
	// and act accordingly
	let SOURCE_ASSET_PATH = (DESTINATION_ASSET_PATH = ASSET_PATH)

	if (ASSET_PATH.includes(':'))
		[SOURCE_ASSET_PATH, DESTINATION_ASSET_PATH] = ASSET_PATH.split(':')

	console.log(`\nUsing the ${branch} branch\n`)
	execSync(
		`
        ssh ${CONNECT} "cd ${DEPLOY_PATH} && git fetch && git fetch --tags && git reset --hard origin/${branch} && composer install -o --no-dev && mkdir -p ${DEPLOY_PATH}/css && mkdir -p ${DEPLOY_PATH}/js"
    `,
		{ stdio: [0, 1, 2] }
	)

	console.log(
		`\nSyncing ${SOURCE_ASSET_PATH} to ${CONNECT}:${path.join(
			DEPLOY_PATH,
			DESTINATION_ASSET_PATH
		)}\n`
	)
	execSync(
		`
        rsync -azv --delete ${SOURCE_ASSET_PATH}/ ${CONNECT}:${path.join(
			DEPLOY_PATH,
			DESTINATION_ASSET_PATH
		)}/
    `,
		{ stdio: [0, 1, 2] }
	)

	console.log(`\n\nCheck the results of your deploy at ${url}`)
}

module.exports = {
	deploy
}
