import Vue from "vue";
import PortalVue from "portal-vue";
import VueModalTor from "vue-modaltor";
import VTooltip from "v-tooltip";
import SearchwpAjax from "./inc/Ajax";
import SearchwpSettingsAdvanced from "./SearchwpSettingsAdvanced.vue";

Vue.use(PortalVue);
Vue.use(VueModalTor);
Vue.use(VTooltip);
Vue.use(SearchwpAjax);

new Vue({
  el: "#searchwp-settings-advanced",
  data: _SEARCHWP_VARS.data ? _SEARCHWP_VARS.data : {},
  template: "<searchwp-settings-advanced/>",
  components: {
    "searchwp-settings-advanced": SearchwpSettingsAdvanced
  }
});
