<template>
    <div>
        <searchwp-message
            v-if="basicAuth"
            :type="'error'"
            :moreInfo="'https://searchwp.com/docs/hooks/searchwp_basic_auth_creds/'">
            <p>{{ i18n.basicAuth }}</p>
        </searchwp-message>
        <searchwp-message
            v-if="!validDatabase"
            :type="'error'"
            :action="{ target: recreateDatabaseTables, text: i18n.recreateTables }">
            <p>{{ i18n.databaseTablesMissing }}</p>
        </searchwp-message>
    </div>

</template>

<script>
import Vue from 'vue';
import { EventBus } from './../EventBus.js';
import Message from './Message.vue';

export default {
    name: 'SearchwpEnvironmentCheck',
    components: {
        'searchwp-message': Message
    },
    methods: {
        recreateDatabaseTables() {
            Vue.SearchwpRecreateDatabaseTables().then((response) => {
                this.validDatabase = true;
                // EventBus.$emit('databaseTablesRecreated');
                window.location.reload();
            }).catch(function (response) {
                this.validDatabase = false;
                alert('ERROR SEARCHWPRECREATEDBTABLES')
            });
        }
    },
    data: function(){
        return {
            basicAuth: false,
            validDatabase: _SEARCHWP_VARS.data.misc.valid_db,
            i18n: {
                basicAuth: _SEARCHWP_VARS.i18n.basic_auth_note,
                databaseTablesMissing: _SEARCHWP_VARS.i18n.database_tables_missing,
                recreateTables: _SEARCHWP_VARS.i18n.recreate_tables
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
    .swp-notices + #error {
        display: none;
    }
</style>
