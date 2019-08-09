

# Javascript Dependencies

```{
  "name": "wp_base_install",
  "version": "1.0.0",
  "description": "WP Base Install Javascript Dependencies",
  "main": "index.js",
  "scripts": {
    "lint": "npx eslint ./build/javascripts",
    "lint:fix": "npx eslint --fix ./build/javascripts",
    "test": "jest",
    "build": "webpack",
    "prod": "webpack --mode production",
    "watch": "webpack --watch",
    "block:add": "./tools/blocks/index.js add",
    "pull:db:qa": "./tools/wordpress/index.js pull db qa",
    "push:db:qa": "./tools/wordpress/index.js push db qa",
    "pull:uploads:qa": "./tools/wordpress/index.js pull uploads qa",
    "push:uploads:qa": "./tools/wordpress/index.js push uploads qa",
    "pull:db:staging": "./tools/wordpress/index.js pull db staging",
    "push:db:staging": "./tools/wordpress/index.js push db staging",
    "pull:uploads:staging": "./tools/wordpress/index.js pull uploads staging",
    "push:uploads:staging": "./tools/wordpress/index.js push uploads staging",
    "pull:db:prod": "./tools/wordpress/index.js pull db prod",
    "push:db:prod": "./tools/wordpress/index.js push db prod",
    "pull:uploads:prod": "./tools/wordpress/index.js pull uploads prod",
    "push:uploads:prod": "./tools/wordpress/index.js push uploads prod",
    "deploy:qa": "npm run build && ./tools/wordpress/index.js push code qa develop",
    "deploy:staging": "npm run prod && ./tools/wordpress/index.js push code staging",
    "deploy:prod": "npm run prod && ./tools/wordpress/index.js push code prod",
    "ssh:qa": "./tools/ssh.js ssh qa",
    "ssh:staging": "./tools/ssh.js ssh staging",
    "ssh:prod": "./tools/ssh.js ssh prod",
    "lighthouse": "./tools/lighthouse.js",
    "setup": "./tools/setup.js"
  },
  "repository": {
    "type": "git",
    "url": "git+https://bitbucket.org/paradowskicreative/wp_base_install.git"
  },
  "jest": {
    "verbose": true,
    "preset": "jest-puppeteer"
  },
  "author": "",
  "license": "ISC",
  "homepage": "https://bitbucket.org/paradowskicreative/wp_base_install#readme",
  "devDependencies": {
    "@babel/core": "^7.4.0",
    "@babel/preset-env": "^7.4.2",
    "autoprefixer": "^9.1.5",
    "babel-eslint": "^10.0.1",
    "babel-jest": "^24.1.0",
    "babel-loader": "^8.0.5",
    "babel-plugin-add-module-exports": "^1.0.0",
    "babel-plugin-transform-react-jsx": "^6.24.1",
    "babel-preset-env": "^1.7.0",
    "browser-sync": "^2.26.3",
    "browser-sync-webpack-plugin": "^2.2.2",
    "commander": "^2.19.0",
    "css-loader": "^1.0.0",
    "cssnano": "^4.1.4",
    "dotenv": "^6.1.0",
    "eslint": "^5.6.1",
    "file-loader": "^2.0.0",
    "glob": "^7.1.3",
    "jest": "^24.1.0",
    "jest-puppeteer": "^4.0.0",
    "mini-css-extract-plugin": "^0.4.4",
    "node-sass": "^4.9.3",
    "normalize-scss": "^7.0.1",
    "optimize-css-assets-webpack-plugin": "^5.0.1",
    "postcss-loader": "^3.0.0",
    "precss": "^3.1.2",
    "puppeteer": "^1.12.2",
    "sass-loader": "^7.1.0",
    "uglifyjs-webpack-plugin": "^2.0.1",
    "webpack": "^4.20.2",
    "webpack-cli": "^3.1.2"
  },
  "dependencies": {
    "@babel/polyfill": "^7.0.0",
    "react": "^16.8.6"
  }
}
```