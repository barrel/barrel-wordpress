## CSS and JS Source Code

### CSS Source File Guidelines
- Mixins, variables, typography, functional CSS, and global features for the site should go in the `css/base` folder. 
- Any new file added in css/base should be imported in the main.css file found in `css/base`.
- Any new modules added should also be added imported in main.css found `in css/base`.

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

#### Known Issues/ToDos
1. Right now, the deferred style build doesn't handle _other_ comments well. Any comment that isn't related to the build task _should_ be
getting stripped out with the `cssnano` plugin, but they're still showing up in the `deferred.min.css` file.
