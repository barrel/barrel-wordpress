import Vue from "vue";
import VTooltip from "v-tooltip";
import Multiselect from "vue-multiselect";
import SearchwpAjax from "./inc/Ajax";
import SearchwpSettings from "./SearchwpSettings.vue";

Vue.use(SearchwpAjax);
Vue.use(VTooltip);

Vue.component("multiselect", Multiselect);

new Vue({
  el: "#searchwp-settings",
  data: _SEARCHWP_VARS.data ? _SEARCHWP_VARS.data : {},
  template: "<searchwp-settings/>",
  components: {
    "searchwp-settings": SearchwpSettings
  }
});
