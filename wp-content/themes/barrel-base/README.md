# Theme Installation

## Dependencies & Setup

1.  Ensure [Node.js](https://nodejs.org/) is installed globally on the target system.
2.  Run `npm i` in the theme directory.
3.  Run `npm start` to start ongoing development task, or `npm run build` to compile assets a single time.

### Setup
The theme makes use of the [barrel-cli](https://github.com/barrel/barrel-cli), which wraps much of the webpack and postcss functionality along with tooling for our modular development workflow.

If you have an issue with setup, please open an [issue](https://github.com/barrel/barrel-cli/issues) on GitHub.

### CSS Build Process
#### Overview
This theme uses postcss to build its stylesheets. The main config file can be found in the theme's root directory in a `postcss.config.js` file. If you do nothing other than add styles, all styles will
be added to the `<head>` tag of the theme, per usual. If you're interested in micro-optimizations, we make use of a postcss [plugin](https://www.npmjs.com/package/postcss-critical-split) to "defer" certain styles. Any "deferred" styles will be removed from the
`main.min.css` file and added to `deferred.min.css` in the assets directory. The deferred stylesheet will be loaded asynchronously (after all the other things). This might be useful if a large portion of
a module or stylesheet isn't needed for initial page load, or if page-speed scores are important to the client's business. 

#### Usage
The deferred styles are built specifically using the `build:lazy-css` script in `package.json`. `npm run build:lazy-css` is called on `npm run build` by default, so this process will run automatically.
Keep in mind that running `npm run build:lazy-css` will focus on building _only_ the deferred styles, and the main stylesheet will not be updated. 

To add styles to the deferred stylesheet, simply wrap them in css comments. In the below example, the `.defer-class-name` and `.defer-class-name__title` styles will be added the deferred stylesheet, and 
the `.main-class-name` styles will be loaded in the header the main.min.css styles.
```css
/* defer:start */
.defer-class-name {
    padding: 0;
    background-color: blue;
}
.defer-class-name__title {
    font-size: 25px;
}
/* defer:end */
.main-class-name {
    height: 15vh;
}
```

Alternatively, you can use a single comment to add a single block of styles to the deferred sheet using a `/* defer */` comment. The following styles will have the same result as above:
```css
.defer-class-name {
    padding: 0;
    background-color: blue;
    /* defer */
}
.defer-class-name__title {
    font-size: 25px;
    /* defer */
}
.main-class-name {
    height: 15vh;
}
```

#### Config
The deferred css file is being built using the config file in the `/tasks` directory, rather than the config file in theme root. 
Post css, for some reason, requires any config file to be named `postcss.config.js`, so we needed to store this one in a different 
directory to prevent conflicts in filename.. Sorry for any confusion there :)

The `/tasks/postcss.config.js` config file is based on the main `postcss.config.js` file in the theme root. Any changes in the root file 
will be reflected in the `tasks/postcss.config.js` file. You should not need to edit the `tasks/postcss.config.js` file unless specifically 
changing the deferred css build flow.

Right now, to get this working, we have to define the `postcss-critical-split` plugin options in both config files, which is pretty WET. 
Ideally, we'd be able to manipulate the plugin config and just change the `output` option in the `postcss-critical-split` plugin, as that's 
the only thing we really need to change for the deferred process. I would love to do something like 
`config.plugins.postcssCriticalSplit.options.output = 'critical'`, but couldn't figure it out :(

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

#### Plugin and Library Approvals
*Move to Contributing?*
In advanced of adding any new plugin or software library specifically for a requirement of the project, please open an issue and assign it to the lead or overseeing developer for approval. 

Most **WordPress plugins** will need to be added as a support or hotfix sub-branch of the upstream code to reduce review overhead. DO NOT add plugins into a feature branch or in develop directly.

There are several reasons to add a software library or **Software Development Kit** (SDK). There are also several considersations for adding a SDK to a project. Before adding a library, please consult with the lead or overseeing developer. 

The following questions will help guide the conversation for both plugins and SDKs:

- What licenses governs its usage?
- Is it "free as in beer" (gratis, freeware)? 
- Is it "free as in speech" (libre, open source)? 
- Can it be redistributed? 
- Does it cover important architecture, security, or design aspects of a project?
- Does it handle complex business features or logic?
- What are its dependencies?
- How often does the software receive maintenance or updates?
- How well-rated is the software by community or users?
- Are any APIs provided or exposed for development purposes?

Prepare to answer the above and other questions by opening an issue and assigning it to the lead or overseeing developer for approval.

### Notes

jQuery is deregistered by default in the `enqueue_scripts_and_styles()` method in `lib/class-theme-init.php`. If a plugin requires jQuery as a dependency, you should remove this code.
