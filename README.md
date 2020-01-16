![Paradowski Creative](/pc_logo.svg?raw=true "Paradowski Creative")

# Wordpress Project - Base Install: Beaver Builder

The hope of this repository is to have standard starting point when beginning a Wordpress project at Paradowski Creative. We will strive to follow the best practices to enable semantic, performant, and accessible websites. The Beaver Builder can be used as a page builder instead of Gutenberg and tooling should reflect that.

## Documentation

Please refer to the [wiki](./wiki.md) for full documentation, examples and other information.

## Installation

If you've never used a PC Wordpress project before, please refer to the "Installation" page of the [wiki](./wiki.md) .

To create a Wordpress project, you just need to:

1. Get a copy of this repo to your computer. Rename the directory to match your new project: `git clone git@bitbucket.org:paradowskicreative/wp-base-install.git <name>`.
2. Run a search and replace for `wp_base_install_gutenberg` to your new project's name. This is also the name of the theme.
3. Duplicate and remove the extension `.example` on all config files found and update to your new project's configurations.
4. Run `npm run setup` to install dependencies and `docker compose up -d` to turn on the virtual machine on your computer!

## Usage

If you've never used PC Wordpress project before, please refer to the "Development Workflow" page of the [wiki](./wiki.md) .

1. Run `npm run watch` to start developing your new Wordpress project.
2. To deploy the develop branch to the QA environment use `npm run deploy:qa`
3. To deploy the master branch to the Staging environment use `npm run deploy:staging`
