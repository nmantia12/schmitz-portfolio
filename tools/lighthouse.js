#!/usr/bin/env node

require('dotenv').config()
const WP_HOME = process.env.WP_HOME

require('child_process').execSync(`
    lighthouse ${WP_HOME} --view
`, { stdio: [0, 1, 2] })
