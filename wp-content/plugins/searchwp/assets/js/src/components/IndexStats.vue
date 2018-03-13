<template>
    <div class="searchwp-index-stats">

        <div class="searchwp-index-progress">

            <dl>
                <dt>{{ i18n.indexProgress }}</dt>
                <dd>{{ progress }}%</dd>
            </dl>

            <searchwp-progress-bar
                :progress="progress"
                :disabled="!initialSettingsSaved || emptyEngines"/>

        </div>

        <searchwp-message
            v-if="dirtyIndex || $root.misc.index_dirty"
            :type="'warning'"
            :action="{ target: resetIndex, text: i18n.rebuildIndex}">
            <p>{{ i18n.indexDirty }}</p>
        </searchwp-message>

        <searchwp-message v-if="waiting">
            <p>{{ i18n.autoScale }}</p>
        </searchwp-message>

        <table>
            <tbody>
                <tr>
                    <th>{{ i18n.lastActivity }}</th>
                    <td>{{ lastActivity }}</td>
                </tr>
                <tr>
                    <th>{{ i18n.indexed }}</th>
                    <td><span class="code">{{ indexed }}</span> {{ i18n.entries }}</td>
                </tr>
                <tr>
                    <th>{{ i18n.unindexed }}</th>
                    <td><span class="code">{{ unindexed }}</span> {{ i18n.entries }}</td>
                </tr>
                <tr>
                    <th>{{ i18n.mainRowCount }}</th>
                    <td><span class="code">{{ mainRowCount }}</span> {{ i18n.rows }}</td>
                </tr>
            </tbody>
        </table>

        <p class="description">{{ i18n.indexNote }}</p>

        <searchwp-message
            v-if="adminEnabled">
            <p>{{ i18n.adminSearchEnabled }}</p>
        </searchwp-message>

    </div>
</template>

<script>
import Vue from 'vue';
import keyfinder from 'keyfinder';
import { EventBus } from './../EventBus.js';
import SearchwpProgressBar from './ProgressBar.vue';
import SearchwpMessage from './Message.vue';

export default {
    name: 'SearchwpIndexStats',
    components: {
        'searchwp-progress-bar': SearchwpProgressBar,
        'searchwp-message': SearchwpMessage
    },
    data: function(){
        return {
            i18n: {
                adminSearchEnabled:  _SEARCHWP_VARS.i18n.admin_search_enabled,
                autoScale:  _SEARCHWP_VARS.i18n.auto_scale,
                indexProgress: _SEARCHWP_VARS.i18n.index_progress,
                lastActivity: _SEARCHWP_VARS.i18n.last_activity,
                indexDirty: _SEARCHWP_VARS.i18n.index_dirty,
                indexed: _SEARCHWP_VARS.i18n.indexed,
                unindexed: _SEARCHWP_VARS.i18n.unindexed,
                mainRowCount: _SEARCHWP_VARS.i18n.main_row_count,
                entries: _SEARCHWP_VARS.i18n.entries,
                rightNow: _SEARCHWP_VARS.i18n.right_now,
                rows: _SEARCHWP_VARS.i18n.rows,
                rebuildIndex: _SEARCHWP_VARS.i18n.rebuild_index,
                indexNote: _SEARCHWP_VARS.i18n.index_note
            },
            progress: _SEARCHWP_VARS.data.index_stats.progress,
            lastActivity: _SEARCHWP_VARS.data.index_stats.last_activity,
            timeSinceChange: 0,
            indexed: _SEARCHWP_VARS.data.index_stats.done,
            unindexed: _SEARCHWP_VARS.data.index_stats.remaining,
            mainRowCount: _SEARCHWP_VARS.data.index_stats.main_row_count,
            waiting: _SEARCHWP_VARS.data.index_stats.waiting,
            dirtyIndex: false,
            adminEnabled: _SEARCHWP_VARS.data.misc.admin_search,
            emptyEngines: _SEARCHWP_VARS.data.misc.empty_engines,
            initialSettingsSaved: _SEARCHWP_VARS.data.misc.initial_settings_saved
        }
    },
    methods: {
        resetIndex() {
            this.dirtyIndex = false;
            this.progress = 0;
            this.indexed = 0;
            this.unindexed = ' ';
            this.mainRowCount = 0;
            this.lastActivity = this.i18n.rightNow;

            Vue.SearchwpResetIndex();

            EventBus.$emit('indexReset');
        },
        updateIndexStats() {
            let jumpStart = false;
            let self = this;
            if ( self.timeSinceChange > 30 && ! self.waiting ) {
                jumpStart = true;
            }
            Vue.SearchwpGetIndexStats(jumpStart).then((response) => {
                if (self.progress < 100 && self.progress == response.progress) {
                    self.timeSinceChange = self.timeSinceChange + 5;
                } else {
                    self.timeSinceChange = 0;
                }
                self.progress = response.progress;
                self.lastActivity = response.last_activity;
                self.indexed = response.done;
                self.unindexed = response.remaining;
                self.mainRowCount = response.main_row_count;
                self.waiting = !!response.waiting;

                // Daisychain this call
                setTimeout(function(){
                    self.updateIndexStats();
                },5000);
            }).catch(function (response) {
                // Logged out?
            });
        }
    },
    created() {
        let self = this;

        if (!_SEARCHWP_VARS.data.misc.alternate_indexer) {
            self.updateIndexStats();
        }

        EventBus.$on('enginesSaved', function( fingerprints ) {
            self.dirtyIndex = fingerprints.dirtyIndex;
            self.initialSettingsSaved = true;

            // Determine whether there are no enabled post types
            let emptyEngines = true;
            keyfinder(fingerprints.engines, 'enabled').forEach(function(postTypeEnabled){
                if (postTypeEnabled) {
                    emptyEngines = false;
                }
            });

            self.emptyEngines = emptyEngines;
        });

        EventBus.$on('indexReset', function() {
            self.dirtyIndex = false;
            self.progress = 0;
            self.indexed = 0;
            self.unindexed = ' ';
            self.mainRowCount = 0;
            self.lastActivity = self.i18n.rightNow;
        });
    }
}
</script>

<style lang="scss">
    .searchwp-notice {
        background: #fff;
    }

    .searchwp-index-progress {

        dl {
            margin: 0 0 0.2em;
            padding: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        // Mimic .postbox h3 without side padding
        dt {
            margin: 0;
            font-size: 14px;
            padding: 8px 0;
            color: #23282d;
            font-weight: bold;
            line-height: 1.4;
        }

        dd {
            margin: 0;
            padding: 0 0 0 1em;
            line-height: 1;
        }

        + .searchwp-message {
            margin-top: 1em;
        }
    }

    .searchwp-index-stats {

        table {
            text-align: left;
            width: 100%;
            margin: 1em 0;
        }

        th {
            text-align: left;
            font-weight: bold;
        }

        th,
        td {
            padding: 0.7em 0.4em 0.7em;
            border-bottom: 1px solid #dfdfdf;
            border-collapse: collapse;
            line-height: 1;

            // Mimic WP Core <code></code>
            span {
                padding: 3px 5px 2px;
                margin: 0 1px;
                background: rgba( 0, 0, 0, 0.07 );
                font-size: 13px;
                line-height: 1;
                display: inline-block;
            }
        }

        th {
            padding-left: 0;
        }

        > p {
            margin-bottom: 2em;
        }
    }
</style>
