import Vue from "vue";
import Popover from 'vue-js-popover';
import Multiselect from 'vue-multiselect';
import SearchwpAjax from "./inc/Ajax";
import SearchwpSettings from "./SearchwpSettings.vue";

Vue.use(SearchwpAjax);
Vue.use(Popover)
Vue.component('multiselect', Multiselect);
Vue.use(SearchwpAjax);

new Vue({
  el: '#searchwp-settings',
  data: _SEARCHWP_VARS.data ? _SEARCHWP_VARS.data : {},
  template: '<searchwp-settings/>',
  components: {
    'searchwp-settings': SearchwpSettings
  }
});
