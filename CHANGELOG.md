# Change Log
All notable changes to this project will be documented in this file.

## 4.1.0 - 2020-06-25
### CHANGED:
- Update gitlab-ci config with release theme name variable 
- Update wordpress-seo plugin 
- Update redirection plugin 
- Update pantheon-advanced-page-cache plugin 
- Update wp-native-php-sessions plugin 
- Update custom-post-type-ui plugin 
- Add note about break in update script process 
- Update update_plugins.sh script to be executable 
- Updates to gitlab/ci deployment scripting 
- Amended WordPress 5.4.2 release. 
- Update to WordPress 5.4.2. For more information, see https://wordpress.org/news/2020/06/wordpress-5-4-2-security-and-maintenance-release/ 
- [OTTO-357] Add protected_web_paths to upstream 
- change default for new sites from utf8 to utf8mb4  

## 4.0.0 - 2020-05-12
### CHANGED:
- Update to WordPress 5.4.1. For more information, see https://wordpress.org/news/2020/04/wordpress-5-4-1/ 
- Upstream update notice improvements 
- Update to WordPress 5.4. For more information, see https://wordpress.org/news/2020/03/adderley/ 
- Update to WordPress 5.3.2. For more information, see https://wordpress.org/news/2019/12/wordpress-5-3-1-security-and-maintenance-release/ 
- Update to WordPress 5.3.1. For more information, see https://wordpress.org/news/2019/12/wordpress-5-3-1-security-and-maintenance-release/ 
- Update to WordPress 5.3. For more information, see https://wordpress.org/news/2019/11/kirk/ 
- Update to WordPress 5.2.4. For more information, see https://wordpress.org/news/2019/10/wordpress-5-2-4-security-release/ 
- Enforce HTTPS. For more info see https://pantheon.io/blog/pantheon-now-enforces-https-default-plus-really-simple-hsts 
- Update .gitignore 
- Update to WordPress 5.2.3. For more information, see https://wordpress.org/news/2019/09/wordpress-5-2-3-security-and-maintenance-release/ 
- Remove `max-age` from logged-in responses 
- Improve WordPress dashboard Custom Upstream update notifications 
- Remove the Try Gutenberg callout code 
- Update to WordPress 5.2.2. For more information, see https://wordpress.org/news/2019/06/wordpress-5-2-2-maintenance-release/ 
- Update to WordPress 5.2.1. For more information, see https://wordpress.org/news/2019/05/jaco/ 

## 3.5.0 - 2020-05-12
### CHANGED:
- Modify prepare script to default variable of START with an 's' parameter 
- Add todo for later theme setting 
- Modify git import script 
- Add visual regression test job back and allow to fail 
- CI Script updates 
- Update CI docker image 

## 3.4.0 - 2019-11-06
### CHANGED:
- Add visual regression test suite 
- Update pantheon.upstream.yml to default to PHP73 and include auto-https enforcement 

## 3.3.1 - 2019-08-02
### CHANGED:
- Modify set env url script to leverage the search parameter only for the initial lookup 
- Update scripts that reference the environment name to only trim the last hyphen 

## 3.3.0 - 2019-07-30
### CHANGED:
- Update clone script procedures 
- Add pantheon-advanced-page-cache plugin 
- Update wordpress-seo plugin 
- Update searchwp plugin 
- Update redirection plugin 
- Update kraken-image-optimizer plugin 
- Update gravity-forms plugin 
- Update custom-post-types plugin 
- Update advanced-custom-fields-pro plugin 
- Update script to update WP core and plugins 

## 3.2.0 - 2019-07-16
### CHANGED:
- Update Zapier webhook catch URL 
- Add case blocks to prepare script for synchronizing git remotes and deploying to pantheon 
- Modify block to install dependencies, mnoving it lower than the current version detection 
- Remove exits within conditional blocks to treat as skip 
- Modify prepare script dialog output and current version detection 
- Update terminus command to find mulitdev by name 
- Update testing script variable with export 
- Modify readme and move/centralize markdown files to a single directory 
- Remove lines that delete the remote Pantheon branch after multidev deletion 

## 3.1.0 - 2019-05-11
### CHANGED:
- Add line to init gitflow with defaults 
- Remove THEME_NAME variable declaration for CI context 
- Remove older 2017 wp theme 
- Update Yoast plugin v11.1.1 
- Update Gravity forms to v2.4.9 
- Update Classic Editor plugin to v1.5 
- Update ACF plugin to v5.8.0 
- Update terminus multidev:delete command for 2.0 support 
- Update to WordPress 5.2. For more information, see https://wordpress.org/news/2019/05/jaco/ 

## 3.0.0 - 2019-05-03
### CHANGED:
- Update WordPress to v5.1.1
- Add Classic Editor plugin
- Update all pre-installed plugins
- Update .editorconfig rule to allow json files to end with new lines
- Modify theme directory switch and theme dependency injection blocks
- Update package.json npm test
- Update deploy and test scripts with better exit codes
- Add streamlined config within `.gitlab-ci.yml`
- Add standardized colors referenced in each script
- Add WIP scripts: `clone`, `init`, `update-plugin`, `loop-module`, and `import-module`
- Add `set-env-url` script to allow dynamic creation of GitLab "Environment URL" via GitLab API

## 2.1.0 - 2019-01-30
### CHANGED:
- Switch postcss mixin dependency 

## 2.0.1 - 2019-01-16
### CHANGED:
- Relocate changelog and track deferred styles 

## v2.0.0 - 2019-01-16
### CHANGED:
- Add uploads proxy function (works with lando)
- Add GitLab CI stage and script to create merge request (manual)
- Add CSS linting tests (new projects only)
- Add clone script (new projects only, project initialization)
- Add editorconfig tests (all projects)
- Add script to remove multidev envs (manual)
- Fix sync into CPT UI JSON files (theme)
- Add test to confirm JSON validity
- Invert critical css workflow to identify "deferred" styles instead of "critical" styles
- Add file-size threshold to control inline-css in the `<head>` tag
- Modify and overhaul the main readme documentation
- Add initial module for social media icons
- Job stages has been reduced to test, deploy, and merge_request
- Most theme test jobs have been reduced to a single job script
- Many CI tests can be run from npm scripts
- Fix environment clone logic on deployment script - will now only clone environment if multidev doesn't already exist
- Add retry parameters to the test stages. Jobs will retry a maximum of 2 times

## v1.12.0 - 2018-09-27
### CHANGED:
- Update Barrel CLI 
- Add base style utility classes
- Add screenshot generation script
- Enable multidev deployments by default

## v1.11.0 - 2018-09-20
### CHANGED:
- Add auto-deploy script for hotfixes

## v1.10.0 - 2018-09-13
### CHANGED:
- Update plugins

## v1.9.2 - 2018-09-11
### CHANGED:
- Add quicksilver creation hook
- Add call to zapier webhook for above
- Fix linting errors

## v1.9.1 - 2018-08-31
### CHANGED:
- Update plugins

## v1.9.0 - 2018-08-31
### CHANGED:
- Add test-module script for test pipeline
- Add ambient video module

## v1.8.0 - 2018-08-28
### CHANGED:
- Update wordpress core version
- Update all plugins
- Add condition to acceptance pipeline to check if git remote exists already before trying to create it

## v1.7.3 - 2018-08-15
### CHANGED:
- Add dom and util js files

## v1.7.2 - 2018-08-01
### CHANGED:
- Add condition around critical.css

## v1.7.1 - 2018-08-1
### CHANGED:
- Fix name of theme in create module script

## v1.7.0 - 2018-08-1
### CHANGED:
- Add create module script
- Update critical css workflow

## v1.6.0 - 2018-07-06
### CHANGED:
- Add Critical CSS workflow
- Update images module
- Update css workflow for postcss
- Update scripts
- Add barrel mu plugin

## 1.4.0 - 2018-03-13
### CHANGED:
- Update all plugins
- Add barrel-cli, including webpack, postcss, and config
- Stub improvements to CI on GitLab

## 1.3.0 - 2018-02-11
### CHANGED:
- Update Core and platform
- Add webpack config, remove browserify

## 1.2.2 - 2017-11-02
### CHANGED:
- Update Core and supplied plugins

## 1.2.1 - 2017-10-19
### CHANGED:
- Update Core
- Remove old requirements/tasks
- Bug fixes

## 1.2.0 - 2017-09-18
### CHANGED:
- Update Core and supplied plugins
- Update must-use plugins for XML-RPC
- Update standardjs and php syntax checks for gitlab ci
- Add routines to facilitate with post types and taxonomies

## 1.1.1 - 2017-07-10
### CHANGED:
- Update core redirect logic
- Update XML-RPC disabled mu-plugin
- Update build scripts

## 1.1.0 - 2017-05-16
### CHANGED:
- Remove obsolete theme components
- Upgrade plugins and WordPress core

## 1.0.0 - 2015-10-01
### CHANGED:
- Initial base theme commit

