<template>
    <div>
        <searchwp-message
            v-if="basicAuth"
            type="error"
            moreInfo="https://searchwp.com/docs/hooks/searchwp_basic_auth_creds/">
            <p>{{ i18n.basicAuth }}</p>
        </searchwp-message>
    </div>

</template>

<script>
import Vue from 'vue';
import Message from './Message.vue';

export default {
    name: 'SearchwpEnvironmentCheck',
    components: {
        'searchwp-message': Message
    },
    data: function(){
        return {
            basicAuth: false,
            i18n: {
                basicAuth: _SEARCHWP_VARS.i18n.basic_auth_note
            }
        }
    },
    created() {
        Vue.SearchwpCheckBasicAuth().then((response) => {
            this.basicAuth = response;
        }).catch(function (response) {
            alert('Error HTTPBASICAUTHCHECK');
        });
    }
}
</script>

<style lang="scss">

</style>
