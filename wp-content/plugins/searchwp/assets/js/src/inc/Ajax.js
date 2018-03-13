const SearchwpAjax = {};

SearchwpAjax.install = function (Vue, options) {

  /**
   * Save a setting to the database
   */
  Vue.SearchwpSetSetting = function (setting, value, group) {
      let data = {
          action: 'searchwp_set_setting',
          setting: setting,
          value: value
      };

      if (group) {
          data.group = group;
      }

      // Everything is nonce'd
      if (!_SEARCHWP_VARS.nonces[ setting ]) {
        alert(_SEARCHWP_VARS.i18n.errors.missing_nonce);
        return;
      }

      data._ajax_nonce = _SEARCHWP_VARS.nonces[ setting ];

      return new Promise(function(resolve, reject) {
        jQuery.post(ajaxurl, data, function(response) {
          if (response.success) {
            resolve(response);
          } else {
            reject(response);
          }
        });
      });
  };

  /**
   * Retrieve an existing SearchWP setting from the database
   */
  Vue.SearchwpGetSetting = function(setting, group) {
      let data = {
          action: 'searchwp_get_setting',
          setting: setting,
      };

      if (group) {
        data.group = group;
      }

      // Everything is nonce'd
      if (!_SEARCHWP_VARS.nonces[ setting ]) {
        alert(_SEARCHWP_VARS.i18n.errors.missing_nonce);
        return;
      }

      data._ajax_nonce = _SEARCHWP_VARS.nonces[ setting ];

      return new Promise(function (resolve, reject) {
          jQuery.post(ajaxurl, data, function(response) {
            if (response.success) {
              resolve(response.data);
            } else {
              reject(response);
            }
          });
      });
  };

  /**
   * Retrieve the index stats
   */
  Vue.SearchwpGetIndexStats = function(jumpstart) {
    return new Promise(function (resolve, reject) {
      let data = {
          action: 'searchwp_get_index_stats'
      };

      if (jumpstart) {
        jQuery.get('options-general.php?page=searchwp&swpjumpstart&' + new Date().getTime(), function(data){});
      }

      let nonce = 'get_index_stats';

      // Everything is nonce'd
      if (!_SEARCHWP_VARS.nonces[ nonce ]) {
        reject(_SEARCHWP_VARS.i18n.errors.missing_nonce);
      }

      data._ajax_nonce = _SEARCHWP_VARS.nonces[ nonce ];

      jQuery.post(ajaxurl + '?' + new Date().getTime(), data, function(response) {
        if (response.success) {
          resolve(response.data);
        } else {
          reject(response);
        }
      });
    });
  };

  /**
   * Retrieve the index stats
   */
  Vue.SearchwpResetIndex = function() {
    // Proceed with the reset
    return new Promise(function (resolve, reject) {
      let data = {
          action: 'searchwp_reset_index'
      };

      let nonce = 'reset_index';

      // Everything is nonce'd
      if (!_SEARCHWP_VARS.nonces[ nonce ]) {
        reject(_SEARCHWP_VARS.i18n.errors.missing_nonce);
      }

      data._ajax_nonce = _SEARCHWP_VARS.nonces[ nonce ];

      jQuery.post(ajaxurl, data, function(response) {
        if (response.success) {
          resolve(response.data);
        } else {
          reject(response);
        }
      });
    });
  };

  /**
   * Perform taxonomy term search
   */
  Vue.SearchwpSearchTaxonomyTerms = function(query, taxonomy, post_type) {
    return new Promise(function (resolve, reject) {
      let data = {
          action: 'searchwp_get_tax_terms',
          tax: taxonomy,
          q: query,
          post_type: post_type
      };

      let nonce = 'tax_' + taxonomy + '_' + post_type;

      // Everything is nonce'd
      if (!_SEARCHWP_VARS.nonces[ nonce ]) {
        reject(_SEARCHWP_VARS.i18n.errors.missing_nonce);
      }

      data._swpvtax_nonce = _SEARCHWP_VARS.nonces[ nonce ];

      jQuery.post(ajaxurl, data, function(response) {
        if (response.success) {
          resolve(response.data);
        } else {
          reject(response);
        }
      });
    });
  };

  /**
   * Save a setting to the database
   */
  Vue.SearchwpSetSetting = function (setting, value, group) {
      let data = {
          action: 'searchwp_set_setting',
          setting: setting,
          value: value
      };

      if (group) {
          data.group = group;
      }

      // Everything is nonce'd
      if (!_SEARCHWP_VARS.nonces[ setting ]) {
        alert(_SEARCHWP_VARS.i18n.errors.missing_nonce);
        return;
      }

      data._ajax_nonce = _SEARCHWP_VARS.nonces[ setting ];

      return new Promise(function(resolve, reject) {
        jQuery.post(ajaxurl, data, function(response) {
          if (response.success) {
            resolve(response);
          } else {
            reject(response);
          }
        });
      });
  };

  /**
   * Check status of HTTP Basic Authentication
   */
  Vue.SearchwpCheckBasicAuth = function() {
    return new Promise(function (resolve, reject) {
      let data = {
          action: 'searchwp_basic_auth'
      };

      let nonce = 'basic_auth';

      // Everything is nonce'd
      if (!_SEARCHWP_VARS.nonces[ nonce ]) {
        reject(_SEARCHWP_VARS.i18n.errors.missing_nonce);
      }

      data._ajax_nonce = _SEARCHWP_VARS.nonces[ nonce ];

      jQuery.post(ajaxurl, data, function(response) {
        if (response.success) {
          resolve(response.data);
        } else {
          reject(response);
        }
      });
    });
  };
};

export default SearchwpAjax;
