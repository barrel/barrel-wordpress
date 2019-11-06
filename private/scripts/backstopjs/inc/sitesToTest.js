/**
 * @todo make this leverage ENV variables
 * 
 * $PANTHEON_SITE_ID = barrel-base-theme
 */
module.exports = {
    "barrel-base-theme": {
        label: "Barrel Base Theme",
        productionBaseUrl: "https://dev-barrel-base-theme.pantheonsite.io/",
        nonProductionBaseUrl: "https://develop-barrel-base-theme.pantheonsite.io/",
        pathsToTest: [
            "/",
            "/contribution/",
        ]
    }
};
