# Change Log
All notable changes to this project will be documented in this file.

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

