![Paradowski Creative](/pc_logo.svg?raw=true "Paradowski Creative")

# Wordpress Project - Base Install: Gutenberg

The hope of this repository is to have standard starting point when beginning a Wordpress project at Paradowski Creative. We will strive to follow the best practices to enable semantic, performant, and accessible websites.

## Documentation

Please refer to the [wiki](./wiki.md) for full documentation, examples and other information.

## Installation

If you've never used a PC Wordpress project before, please refer to the "Installation" page of the [wiki](./wiki.md) .

To create a Wordpress project, you just need to:

1. Get a copy of this repo to your computer. Rename the directory to match your new project: `git clone git@bitbucket.org:paradowskicreative/wp-base-install.git <name>`.
2. Run a search and replace for `schmitz_portfolio` to your new project's name. This is also the name of the theme.
3. Duplicate and remove the extension `.example` on all config files found and update to your new project's configurations.
4. Run `npm run setup` to install dependencies and `docker compose up -d` to turn on the virtual machine on your computer!

## Usage

If you've never used PC Wordpress project before, please refer to the "Development Workflow" page of the [wiki](./wiki.md) .

1. Run `npm run watch` to start developing your new Wordpress project.
2. To deploy the develop branch to the QA environment use `npm run deploy:qa`
3. To deploy the master branch to the Staging environment use `npm run deploy:staging`

## Creating Blocks Via ACF

1. Identify blocks from comp
2. navigate to "inc/acf-site-settings", register new ACF blocks in \$pc_blocks array (Block slug and title are required).
3. Create a directory and file for each new ACF block regitered in "/template-parts/acf-blocks" (ie. "/template-parts/acf-blocks/block_slug/block_slug.php").
4. Go to Custom Fields in WP admin interface and create anew field group for each new ACF block. Assign them like so: if block is === "block-slug"
5. Create the necessary fields for each new ACF block in the field groups
6. Navigate to "/template-parts/acf-blocks/block_slug/block_slug.php" and pull in the corresponding fields for each block using (ACF get_field), and assign them PHP variables.
   6a. Each block should be wrapped in a main block class with alignment classes and optional "is-admin" appended. As well as a unique ID per block. These are added automatically via WP & ACF. Examples exist in any of the existing "/template-parts/acf-blocks/block_slug/block_slug.php" files.
7. Create scss files for each new ACF Block ("build/stylesheets/acf-blocks/") & include them in editor.scss and main.scss
   7a. Editor specific styling can be added for a block by creating a second scss file to be only included in the editor.scss or by targting the .is-admin class in the blocks Scss file
