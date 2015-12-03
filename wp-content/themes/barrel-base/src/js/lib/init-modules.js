/**
 * Finds all elements with a "data-module-init" attribute
 * and calls the corresponding script
 */
function initializeModules() {
  var modules = document.querySelectorAll('[data-module-init]');

  for (var i = 0; i < modules.length; i++) {
    var el = modules[i];
    var name = el.getAttribute('data-module-init');

    // Find the module script
    try {
      var Module = require('modules/'+name);
    } catch(e) {
      var Module = false;
      console.log(name+' module does not exist.');
    }

    // Initialize the module with the calling element
    if(Module) {
      new Module(el);
    }
  }
}

module.exports = initializeModules;
