import Vue from "vue";
import VueModalTor from "vue-modaltor";
import VTooltip from "v-tooltip";
import SearchwpStatistics from "./SearchwpStatistics.vue";

Vue.use(VueModalTor);
Vue.use(VTooltip);

new Vue({
  el: "#searchwp-statistics",
  data: _SEARCHWP_VARS.data ? _SEARCHWP_VARS.data : {},
  template: "<searchwp-statistics/>",
  components: {
    "searchwp-statistics": SearchwpStatistics
  }
});
