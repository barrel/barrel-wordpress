# Theme Installation

## Dependencies & Setup

1.  Ensure [Node.js](https://nodejs.org/) is installed globally on the target system.
2.  Run `npm i` in the theme directory.
3.  Run `npm start` to start ongoing development task, or `npm run build` to compile assets a single time.

### Setup
The theme makes use of the [barrel-cli](https://github.com/barrel/barrel-cli), which wraps much of the webpack and postcss functionality along with tooling for our modular development workflow.

If you have an issue with setup, please open an [issue](https://github.com/barrel/barrel-cli/issues) on GitHub.

### WordPress Plugins
*The following plugins are always included:*

1. [Yoast SEO](https://wordpress.org/plugins/wordpress-seo/)
2. [Advanced Custom Fields](https://www.advancedcustomfields.com/)
3. [Gravity Forms](http://www.gravityforms.com/)
4. [Intuitive Custom Post Order](https://wordpress.org/plugins/intuitive-custom-post-order/)
5. [Custom Post Type UI](https://wordpress.org/plugins/custom-post-type-ui/)
6. [Kraken Image Optimizer](https://wordpress.org/plugins/kraken-image-optimizer/)
7. [Redirection](https://wordpress.org/plugins/redirection/)
8. [SearchWP](https://searchwp.com/)

*Pantheon Plugins:*
These plugins are only used on Pantheon.

1. [Native PHP Sessions for WordPress](https://wordpress.org/plugins/wp-native-php-sessions/) - only used with authenticated user traffic

### Notes

jQuery is deregistered by default in the `enqueue_scripts_and_styles()` method in `lib/class-theme-init.php`. If a plugin requires jQuery as a dependency, you should remove this code.
