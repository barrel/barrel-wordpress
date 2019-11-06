// External dependencies
const backstop = require('backstopjs');
const log = require('fancy-log');
const colors = require('ansi-colors');

// Local dependencies
const throwError = require('./utils').throwError;
const backstopConfig = require('./backstopConfig');
const sitesToTest = require('./sitesToTest');

module.exports = function (siteToTest) {

    // Ensure the selected site exists in the config
    const siteExists = Object.prototype.hasOwnProperty.call(sitesToTest, siteToTest);

    // Throw an error if it doesn't
    if (!siteExists) {
        throwError(`${colors.bold(siteToTest)} is not a valid site. Check the name you entered against the ${colors.grey('sitesToTest.js')} config file`);
    }

    const site = sitesToTest[siteToTest];
    
    // Stash the site label
    const boldLabel = `${colors.bold(`${site.label}`)}`;

    // Let the user know we are starting the tests
    log(colors.bgYellow(`Running visual regression tests on ${boldLabel}...\n`));

    // Generate site specific configuration.
    const currentConfig = backstopConfig(site.nonProductionBaseUrl, site.productionBaseUrl, site.pathsToTest, siteToTest);

    // Disable logging since BackstopJS is noisy
    // console.log = function () {};

    backstop('reference', {
        config: currentConfig
    }).then(() => {
        backstop('test', {
            config: currentConfig
        }).then(() => {
            log(colors.bgGreen(colors.white(`Backstop JS tests passed for ${site.label}!`)));
        }).catch(() => {
            log(colors.bgRed(colors.white(`Backstop JS tests failed for ${site.label}!`)));
        });
    }).catch(() => {
        log(colors.bgRed(colors.white(`Backstop JS tests failed for ${site.label}!`)));
    });
};