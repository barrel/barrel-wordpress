Barrel Nyc : El theme-o
=======================


#### The Code.

##### Configurable Options

On page load, the ```functions.php``` file retrieves the options, post_types, enqueued scripts and styles spec'd out in the ```config/<ENV>.config.php``` file to build the theme.

In this file/class exists: 

1. A deregister property for all default scripts you do not want to load
2. A register JS property (to understand array values view [WP > Register Script](http://codex.wordpress.org/Function_Reference/wp_register_script))
3. A deregister property for all default CSS you do not want to load
4. A register CSS property (to understand array values view [WP > Register Script](http://codex.wordpress.org/Function_Reference/wp_register_style))
5. A post type label array to spec out the label names for all declared post types
6. A register post types array to define all post types to be included in theme

At the top of the ```functions.php``` theme constant are defined.

1. ENV tells the theme which config fiel to load (see above)
2. THEME_VERSION is a version number appended to all enqueued links

##### Gruntfile

The gruntfile includes:

1. Source mapping for debugging less and compiled JS

2. Minification

3. Sprite creation

4. An autoprefixer for CSS3 properties like transitions

##### CSS + Less

Less is used to pre-compile the stylesheet.

All theme variables exist in ```less/variables.less```

A very lightweight version of Twitter/Boostrap3 exists in ```less/inc/boostrap/*```

Boostrap mixins are included and utilised wherever possible

The Boostrap grid scaffold is included as well as the normalize and print sheets

Font Awesome in included through less and used as a default for all icons

All theme-specific styles are broken out into different less sheets in ```less/site-specific/```

As a rule each template, regardless of whether it is a partial or not, has a corresponding less sheet

Each less template less sheet uses 1 namespacing class only (specifity should be kept to a minimum)

This class is appended to the first occuring document.* HTML tag in each PHP template file

Classes are used by default rather than ID's

```@media``` queries are placed within the template specific less sheet, below all default styles

```@media``` are used to activate over-riding styles rather than to limit the base styles for an element

The basic make-up of element style definition is as follows:

```CSS
.class{
	.dimensions
	.positioning
	.display-options
	.element-background
	.margins
	.padding
	.border-options
	.type
	.transitions
	.effects
}
```

Sprites are compiled using grunt and an auto created sprite less sheet exists in ```less/site-specific/```

All images contained with in the ```img/ui/``` folder get converted into ```img/sprite.png```

##### ACF

ACF Lite is used by default

A ACF register field helper has been created to speed up the process of registering new field groups through code.

This helper can be found at ```library/helpers/acf.helper.class.php```

Field groups are defined in arrays at the bottom of the helper

##### Settings page

A settings page is automatically initialised with this theme

A Settings page helper has been created to easily add basic settings to the page

This helper can be found at ```library/helpers/options.helper.class.php```

The settings are defined in an array at the bottom of the helper

##### Custom functions

Custom functions are broken out into admin and theme files

Theme functions are grouped together per template file (when this makes sense)

##### JS

JS is architectured into modules and loaded asynchronously with ```require.js```

```js/conf.js``` lays out any configurations for Require

```app.js``` initiates the application

jQuery + underscore libraries are used by default