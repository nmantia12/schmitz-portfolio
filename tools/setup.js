#!/usr/bin/env node

const stdout = require('child_process').execSync(`
	composer install -o
	yarn install
`, { stdio: [0, 1, 2] })