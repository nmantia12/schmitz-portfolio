# WP Base Install - WikiPedia

### Contents

1. What is the WP Base Install project?
2. Technology Stack
3. Installation
4. Working With Code
5. Custom PC Functionality
6. Technical Details
7. Passwords
8. Known Issues / Room for Improvement
9. Resources / Documentation

## What is the WP Base Install project?

The hope of this repository is to have standard starting point when beginning a Wordpress project at Paradowski Creative. We will strive to follow the best practices to enable semantic, performant, and accessible websites.

## Technical Stack

This project's technology stack is a Linux, (E)nginx, MySQL, PHP (LEMP) stack built on a Docker virtual machine. We use PHP's composer to install Wordpress, setup the site architecture, and install necessary 3rd party plugins. We use Javascript's npm/yarn and webpack to compile assets while developing and to help deploy the project files. To create custom block for the Gutenberg editor, React.js will be used to create the editor experience.

## Installation

### Project Dependencies

- [Docker](https://www.docker.com/) Virtual Machine
- [Node](https://nodejs.org/) Javascript Package Manager
- [Yarn](https://yarnpkg.com) Faster Javascript Package Manager
- [Composer](https://getcomposer.org/) PHP Package Manager
- [Sequel Pro](https://sequelpro.com/) Native Mac App Database Manager

**If you have the above installed, follow these steps to install the site locally:**

1. Clone this repo into `www/domains` directory and `cd` into the new directory.
1. In your code editor do a search and replace for `rabo_microsite` and replace with a short name for the project, ie: `prosoco.com`, `12footbeard.com`.
1. Copy the `.env.example` file and rename to `.env`, fill in database credentials.
1. Copy the `.environments.json.example` file and rename to `.environments.json`, nothing needs to change in this file. This file is used for tooling tasks.
1. Copy the `docker-compose.yml.example` file and rename to `docker-compose.yml`, nothing needs to change in this file. This file describes the virtual machine Docker will create.
1. Copy the `nginx.conf.example` file and rename to `nginx.conf`, nothing needs to change in this file. This file describes the nginx server setup.
1. Run `npm run setup` to install PHP and Javascript dependencies. Learn more about dependencies here.
1. Run `docker-compose up -d` which turns on Docker's virtual machine in detached mode.
1. When this is done, run `npm run watch` and it will watch for file changes
1. Start coding!

## Working With Code

### Creating new Gutenberg Blocks

1. With `npm run watch` already running use the command `npm run block:add <NAME>`
1. This will create the appropriate JS and CSS files in the build directory and it updates our custom plugin PHP file to look for those files.

### WorkFlow from LOCAL to QA

1. Create a new branch off the `develop` branch.
1. When you are done with this feature, submit a "Pull Request" into develop.
1. When the Pull Request is approved, merge into `develop`.
1. Run `npm run deploy-qa` to deploy code to the QA environment

### WorkFlow from QA to STAGING

1. Create a new branch off the `master` branch.
1. When you are done with this feature, submit a "Pull Request" into master.
1. When the Pull Request is approved, merge into `master`.
1. Run `npm run deploy-staging` to deploy code to the Staging environment.

### Clean up

1. Delete your local and remote support ticket branch when done. Avoid leaving stale branches.
1. Local branch removal: run `git branch -d supportBanchName`
1. Remote branch removal: run `git push origin --delete supportBanchName`

---

## Technical Details

| Technology                                     | Description                                                                                                                                                                                                                             |
| ---------------------------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| [Wordpress](http://wordpress.org)              | WordPress is a free and open-source content management system (CMS) based on PHP, MySQL, and Gutenberg.                                                                                                                                 |
| [Docker](https://docker.com/)                  | Docker is used to run software packages called "containers". Containers are isolated from each other and bundle their own tools, libraries and configuration files; they can communicate with each other through well-defined channels. |
| [Node](https://nodejs.org/)                    | Node.js is an open-source, cross-platform JavaScript runtime environment for developing a diverse variety of server tools and applications.                                                                                             |
| [Composer](https://getcomposer.org)            | Composer is an application-level package manager for the PHP programming language that provides a standard format for managing dependencies of PHP software and required libraries.                                                     |
| [Sequel Pro](https://sequelpro.com/)           | Sequel Pro is a fast, easy-to-use Mac database management application for working with MySQL databases.                                                                                                                                 |
| [Gutenberg](https://wordpress.org/gutenberg//) | A React based block content editor that replaces TinyMCE.                                                                                                                                                                               |

### Passwords

All associated passwords can be found using the search term 'rabo_microsite' in LastPass. If you are adding new passwords, please make sure 'rabo_microsite' is contained somewhere in the title so that this rule can remain true.

### Known Issues / Room for Improvement

- Add Testing: Jest for Javascript related tests in directory build/tests. PHP testing TBD.
- Add Explicit Code Design Standards: via eslint, adopt a design pattern
- While developing for React, Javascript errors related to StrictMode will appear in the console. https://github.com/WordPress/gutenberg/issues/11360

---

### Resources / Documentation

- [Your Guide to Composer in WordPress](https://composer.rarst.net/)
- [Modern Wordpress Server Stack](https://www.smashingmagazine.com/2016/05/modern-wordpress-server-stack/)
- [PC's Custom Docker Image for Wordpress](https://github.com/ParadowskiCreativeSTL/php-fpm/blob/master/Dockerfile)

---
