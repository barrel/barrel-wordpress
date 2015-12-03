## Theme Javascript

Place any scripts that should be registered for the main theme in this directory. Include as many scripts as you need, separating components or modules by sub directories. Add each of your target components to the build grunt or gulp file based on the below guidelines.

###Guidelines:

1. At minimum, there should be a main theme script (`main.js`).
2. Consider if any scripts need to be loaded before the main script (`init.js`).
3. Consider if any vendor scripts are needed before the main script (`vendor.js`).
4. Configure grunt/gulp to output the above files as needed; all modules or components for each target should be concatenated and minified as per the grunt/gulp build process.