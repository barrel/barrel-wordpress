var $ = require( "jquery" );

/**
 * Finds all elements with a "data-module-init" attribute
 * and calls the corresponding script
 */
function initializeModules() {
  var modules = document.querySelectorAll( "[data-module-init]" );

  for ( var i = 0; i < modules.length; i++ ) {
    var el = modules[ i ];
    var $el = $( el );
    var name = el.getAttribute( "data-module-init" );

    // Find the module script
    try {
      var Module = require( "modules/" + name );
    } catch ( e ) {
      console.log( e.toString() );
      var Module = false;
    }

    // Initialize the module with the calling element
    if ( typeof Module === "function" && !$el.data( "module" ) ) {
      var mod = new Module( el );

      // Save module instance to jQuery data
      $( el ).data( "module", mod );
    }
  }
}

module.exports = initializeModules;
