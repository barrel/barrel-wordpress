<template>

    <div class="postbox metabox-holder searchwp-search-counts">
        <h3 class="hndle"><span>{{ title }}</span></h3>
        <div class="inside">
            <p class="description" v-if="!searches.length">{{ empty }}</p>
            <table v-else>
                <thead>
                    <th>{{ i18n.search }}</th>
                    <th>{{ i18n.count }}</th>
                </thead>
                <tbody>
                    <tr v-for="(search, searchIndex) in searches" :key="'search' + searchIndex">
                        <td>
                            <div class="searchwp-search-counts__query">
                                <delete v-on:onclick="ignore(search.hash)"></delete>
                                <span>{{ search.query }}</span>
                            </div>
                        </td>
                        <td>
                            {{ search.searchcount }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</template>

<script>
import Delete from './Delete.vue';

export default {
    name: 'SearchCounts',
    components: {
        Delete
    },
    props: {
        title: {
            type: String,
            default: _SEARCHWP_VARS.i18n.searches,
            required: false
        },
        searches: {
            type: Array,
            default: [],
            required: true
        },
        empty: {
            type: String,
            default: _SEARCHWP_VARS.i18n.no_searches,
            required: false
        }
    },
    methods: {
        ignore: function(queryHash) {
            let self = this;
            const data = {
                action: 'searchwp_ignore_search',
                hash: queryHash,
                _ajax_nonce: _SEARCHWP_VARS.nonces.ignore_search
            };

            jQuery.post(ajaxurl, data, function(response) {
                if (response.success) {
                    self.$emit('ignoresChanged');
                } else {
                    alert('There was an error. Please try again.');
                }
            });
        }
    },
    data () {
        return {
            i18n: {
                search: _SEARCHWP_VARS.i18n.search,
                count: _SEARCHWP_VARS.i18n.count
            }
        }
    }
}
</script>

<style lang="scss">
    #wpbody-content .searchwp-search-counts {

        &.metabox-holder {
            padding-top: 0;

            &.postbox {

                .hndle {
                    cursor: default;
                }
            }
        }

        table {
            width: 100%;
        }

        th {
            text-align: left;
            font-weight: bold;
        }

        td {
            vertical-align: top;
            padding: 0.2em 0;
        }

        .searchwp-search-counts__query {
            display: flex;

            button {
                margin-right: 0.35em;
            }
        }

    }
</style>
