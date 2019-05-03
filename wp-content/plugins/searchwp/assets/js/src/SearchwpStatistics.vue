<template>

    <div class="searchwp-statistics">

        <ul class="searchwp-statistics__actions">
            <li>
                <searchwp-button
                    :icon="'dashicons-admin-generic'"
                    :label="i18n.ignored"
                    v-on:buttonClick="showingIgnoredSearches = true" />
            </li>
            <li>
                <confirm
                    :icon="'dashicons-dismiss'"
                    :buttonLabel="i18n.resetStats"
                    :question="i18n.areYouSure"
                    :confirm="i18n.yesResetStats"
                    v-on:confirmed="resetStats"/>
            </li>
        </ul>
        <vue-modaltor
            :visible="showingIgnoredSearches"
            @hide="hideIgnoredSearches"
            :default-width="'600px'">
            <div class="searchwp-statistics__modal searchwp-statistics__ignored-searches-details">
                <h4>{{ i18n.ignoredMessage }}</h4>
                <table v-if="ignoredQueries && ignoredQueries.length">
                    <tbody>
                        <tr v-for="(ignoredQuery, ignoredQueryIndex) in ignoredQueriesFiltered"
                            :key="'ignored' + ignoredQueryIndex">
                            <td>
                                <delete v-on:onclick="unIgnoreQuery(ignoredQuery.hash)"></delete>
                                {{ ignoredQuery.query }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p v-else class="description">{{ i18n.noIgnoredQueries }}</p>
            </div>
        </vue-modaltor>

        <div
            v-bind:class="['postbox', 'searchwp-statistics__searches-over-time', searchesOverTime ? '' : 'searchwp-statistics__is-loading']">
            <line-chart
                :datacollection="searchesOverTime"
                :options="searchesOverTimeOptions"
                :height="'300px'"
            ></line-chart>
        </div>

        <div class="searchwp-statistics__populars">

            <search-counts
                :searches="popularToday"
                :title="i18n.today"
                v-on:ignoresChanged="reload"
            ></search-counts>

            <search-counts
                :searches="popularWeek"
                :title="i18n.week"
                v-on:ignoresChanged="reload"
            ></search-counts>

            <search-counts
                :searches="popularMonth"
                :title="i18n.month"
                v-on:ignoresChanged="reload"
            ></search-counts>

            <search-counts
                :searches="popularYear"
                :title="i18n.year"
                v-on:ignoresChanged="reload"
            ></search-counts>

            <search-counts
                :searches="failed"
                :title="i18n.noResultsSearches"
                v-on:ignoresChanged="reload"
            ></search-counts>

        </div>

        <transition name="fade">
            <div class="searchwp-statistics__loading"
                v-if="loading"
                v-bind:style="{ top: loaderPositionTop + 'px', left: loaderPositionLeft + 'px' }">
                <spinner
                    :size="55"
                    :line-size="6"
                ></spinner>
            </div>
        </transition>

    </div>

</template>

<script>
import Vue from 'vue';
import Spinner from 'vue-simple-spinner';
import SearchwpButton from './components/Button.vue';
import LineChart from './components/LineChart.vue';
import SearchCounts from './components/SearchCounts.vue';
import Delete from './components/Delete.vue';
import Confirm from './components/Confirm.vue';

export default {
    name: 'SearchwpStatistics',
    components: {
        Spinner,
        LineChart,
        SearchCounts,
        Delete,
        Confirm,
        SearchwpButton
    },
    methods: {
        resetStats: function() {
            const data = {
                action: 'searchwp_reset_stats',
                _ajax_nonce: _SEARCHWP_VARS.nonces.reset_stats
            };

            let self = this;

            jQuery.post(ajaxurl, data, function(response) {
                if (!response.success) {
                    alert('There was an error. Please try again.');
                } else {
                    self.reload();
                }
            });
        },
        apiRequest: function(data = {}) {
            data.action = 'searchwp_get_statistics';
            data.engine = _SEARCHWP_VARS.data.engine;
            data._ajax_nonce = _SEARCHWP_VARS.nonces.get_statistics;

            return new Promise(function(resolve, reject) {
                jQuery.post(ajaxurl, data, function(response) {
                    if (response.success) {
                        resolve(response);
                    } else {
                        reject(response);
                    }
                });
            });
        },
        formatDataForChart: function (data) {
            let chartData = {
                labels: data.labels,
                datasets: []
            };

            for (let i = 0; i < data.datasets.length; i++) {
                let dataset = data.datasets[i];
                let options = {
                    label: dataset.engine,
                    data: dataset.dataset,
                    borderColor: 'rgba(69, 170, 242, 1)',
                    backgroundColor: 'rgba(112, 190, 245, 0.22)',
                    type: 'line',
                    borderWidth: 2,
                    fill: true
                };

                // Merge the defaults into any options set
                chartData.datasets.push(options);
            }

            return chartData;
        },
        updateSearchesOverTime: function(data) {
            this.searchesOverTime = this.formatDataForChart(data);
        },
        reload: function() {
            this.loading = true;
            this.apiRequest().then((response) => {
                this.updateSearchesOverTime(response.data.searches_over_time);
                this.popularToday = response.data.popular_today;
                this.popularWeek = response.data.popular_week;
                this.popularMonth = response.data.popular_month;
                this.popularYear = response.data.popular_year;
                this.failed = response.data.failed;
                this.ignoredQueries = response.data.ignored;
                this.loading = false;
            });
        },
        updateLoaderPosition: function() {
            let topEl = document.getElementById('wpadminbar');
            let leftEl = document.getElementById('adminmenuback');
            this.loaderPositionTop = topEl ? topEl.offsetHeight : 0;
            this.loaderPositionLeft = leftEl ? leftEl.offsetWidth : 0;
        },
        hideIgnoredSearches: function() {
            this.showingIgnoredSearches = false;
            this.reload();
        },
        unIgnoreQuery: function(hash) {
            // Remove from display (we're not refreshing until modal is closed)
            let newIgnoredQueries = this.ignoredQueries;
            for (let i = 0; i < this.ignoredQueries.length; i++) {
                if (hash === this.ignoredQueries[i].hash) {
                    Vue.set(this.ignoredQueries[i], 'unignored', true);
                    break;
                }
            }

            // Persist the unignore
            const data = {
                action: 'searchwp_unignore_search',
                hash: hash,
                _ajax_nonce: _SEARCHWP_VARS.nonces.unignore_search
            };

            jQuery.post(ajaxurl, data, function(response) {
                if (!response.success) {
                    alert('There was an error. Please try again.');
                }
            });
        }
    },
    computed: {
        ignoredQueriesFiltered: function() {
            return this.ignoredQueries.filter(ignoredQuery => !ignoredQuery.unignored);
        }
    },
    mounted () {
        this.updateLoaderPosition();
        window.addEventListener('resize', this.updateLoaderPosition);
        this.reload();
    },
    data() {
        return {
            loading: true,
            loaderPositionTop: 0,
            loaderPositionLeft: 0,
            searchesOverTime: null,
            searchesOverTimeOptions: {
                maintainAspectRatio: false,
                legend: {
                    display: false
                },
                tooltips: {
                    cornerRadius: 2,
                    titleMarginBottom: 10,
                    xPadding: 16,
                    yPadding: 9,
                    displayColors: false // We want the fill color to be semi-transparent but that doesn't translate well here
                }
            },
            popularToday: [],
            popularWeek: [],
            popularMonth: [],
            popularYear: [],
            failed: [],
            ignoredQueries: [],
            showingIgnoredSearches: false,
            i18n: {
                areYouSure: _SEARCHWP_VARS.i18n.are_you_sure,
                ignored: _SEARCHWP_VARS.i18n.ignored,
                ignoredMessage: _SEARCHWP_VARS.i18n.ignored_message,
                month: _SEARCHWP_VARS.i18n.month,
                noIgnoredQueries: _SEARCHWP_VARS.i18n.no_ignored,
                noResultsSearches: _SEARCHWP_VARS.i18n.no_results_searches,
                resetStats: _SEARCHWP_VARS.i18n.reset_stats,
                today: _SEARCHWP_VARS.i18n.today,
                week: _SEARCHWP_VARS.i18n.week,
                year: _SEARCHWP_VARS.i18n.year,
                yesResetStats: _SEARCHWP_VARS.i18n.yes_reset_stats
            }
        }
    }
}
</script>

<style lang="scss">
  @import "./styles/_popover.scss";
  @import "./styles/_spinner.scss";
</style>
<style lang="scss">
    .searchwp-statistics {

        .modal-vue-wrapper {
            z-index: 999999 !important;

            .modal-vue-overlay {
                background: rgba( 30, 30, 30, 0.5 ) !important;
            }

            .modal-vue-panel {
                max-width: 600px;
                overflow-y: scroll;
                background: #fff !important;

                .modal-vue-content {
                    padding-right: 1em;
                }
            }
        }
    }

    .wp-core-ui .button.searchwp-button {
        margin-top: 0;
    }

    .searchwp-statistics__searches-over-time {
        padding: 1.5em 1em 1em;
    }

    .searchwp-hidden {
        border: 0;
        clip: rect(0 0 0 0);
        height: 1px;
        margin: -1px;
        overflow: hidden;
        padding: 0;
        position: absolute;
        width: 1px;
    }

    .searchwp-statistics__actions {
        position: absolute;
        top: 20px;
        right: 20px;
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;

        > * {
            margin-left: 0.75em;
        }
    }

    .searchwp-statistics__action {
        display: inline-block;

        > span {
            display: flex;
            align-items: center;
        }

        .dashicons {
            margin-right: 0.2em;
        }
    }

    .searchwp-statistics__populars {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;

        > * {
            width: 19%;
            margin-bottom: 20px;

            @media screen and (max-width:1540px) {
                width: 23.5%;
            }

            @media screen and (max-width:1280px) {
                width: 31%;
            }

            @media screen and (max-width:1024px) {
                width: 49%;
            }

            @media screen and (max-width:700px) {
                width: 100%;
            }
        }
    }

    .searchwp-statistics__loading {
        position: fixed;
        top: 0;
        right: 0;
        left: 0;
        bottom: 0;
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.85);
    }

    .searchwp-statistics__loading-details {
        position: relative;
    }

    .searchwp-statistics__loading-details-container {

        > * {
            opacity: 1;
        }
    }
    .searchwp-statistics__loading-details-loader {
        opacity: 0;
        position: absolute;
        width: 0;
        height: 0;
        overflow: hidden;
    }

    .searchwp-statistics__is-loading-details {

        .searchwp-statistics__loading-details-container {
            opacity: 0;
        }

        .searchwp-statistics__loading-details-loader {
            opacity: 1;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100%;
            padding-top: 15vh;
        }
    }

    .searchwp-statistics__modal {
        max-width: 600px;
        text-align: left;

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9em;
            margin-top: 1em;

            th {
                padding-bottom: 0.7em;
            }

            td {
                border-top: 1px solid #eaeaea;
                padding: 0.5em 0 0.4em;
            }

            .searchwp-delete {
                position: relative;
                top: 1px;
            }
        }
    }
</style>
