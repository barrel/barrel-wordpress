<template>

    <div :class="[waiting ? 'searchwp-is-waiting' : '', 'searchwp-engines']">

        <searchwp-message
            v-if="!initialSettingsSaved"
            :type="'warning'"
            :moreInfo="'https://searchwp.com/?p=207'">
            <p>{{ i18n.initialSettingsNotice }}</p>
        </searchwp-message>
        <searchwp-message
            v-else-if="legacySettings"
            :type="'warning'"
            :action="{ target: dismissLegacySettings, text: i18n.dismiss}">
            <p>{{ i18n.legacySettingsNotice }} <br/><a href="https://searchwp.com/?p=104890" target="_blank">{{ i18n.moreInfo }} &raquo;</a></p>
        </searchwp-message>

        <searchwp-engine v-for="(engine, engineName) in engines"
            :key="engineName"
            :name="engineName"
            :settings="engine"/>

        <searchwp-message
            v-if="dirtyIndex"
            :type="'warning'"
            :action="{ target: resetIndex, text: i18n.rebuildIndex}">
            <p>{{ i18n.indexDirtyFromEnginesSave }}</p>
        </searchwp-message>

        <ul class="searchwp-engines-actions">
            <li :class="{ 'searchwp-loading': saving, 'searchwp-saving': saving || doingSave }">
                <a href="#" class="searchwp-button button button-primary" @click.prevent="saveEngines">{{ i18n.saveEngines }}</a>
            </li>
            <li v-if="!saving && !doingSave && !saved">
                <a @click.prevent="addEngine" href="#" class="searchwp-button button">{{ i18n.addEngine }}</a>
            </li>
            <li v-if="saved" class="searchwp-success">
                <span class="searchwp-button button searchwp-button-message"><span class="dashicons dashicons-yes"></span> {{ i18n.saved }}</span>
            </li>
        </ul>
    </div>

</template>

<script>
import Vue from 'vue';
import { EventBus } from './../EventBus.js';
import md5 from 'md5';
import keyfinder from 'keyfinder';
import unique from 'array-unique';
import SearchwpEngine from "./Engine.vue";
import SearchwpMessage from './Message.vue';

export default {
    name: 'SearchwpEngines',
    components: {
        'searchwp-engine': SearchwpEngine,
        'searchwp-message': SearchwpMessage
    },
    data: function(){
        return {
            waiting: false,
            engines: _SEARCHWP_VARS.data.engines,
            initialFingerprint: '',
            fingerprint: '',
            dirtyIndex: false,
            saving: false,
            doingSave: false,
            saved: false,
            initialSettingsSaved: _SEARCHWP_VARS.data.misc.initial_settings_saved,
            legacySettings: _SEARCHWP_VARS.data.misc.legacy_settings,
            i18n: {
                addEngine: _SEARCHWP_VARS.i18n.add_engine,
                indexDirtyFromEnginesSave: _SEARCHWP_VARS.i18n.index_dirty_from_engines_save,
                problemSavingEngineSettings: _SEARCHWP_VARS.i18n.problem_saving_engine_settings,
                rebuildIndex: _SEARCHWP_VARS.i18n.rebuild_index,
                saveEngines: _SEARCHWP_VARS.i18n.save_engines,
                saved: _SEARCHWP_VARS.i18n.saved,
                dismiss: _SEARCHWP_VARS.i18n.dismiss,
                legacySettingsNotice: _SEARCHWP_VARS.i18n.legacy_settings_notice,
                initialSettingsNotice: _SEARCHWP_VARS.i18n.initial_settings_notice,
                moreInfo: _SEARCHWP_VARS.i18n.more_info,
                useDefaults: _SEARCHWP_VARS.i18n.use_defaults
            }
        };
    },
    methods: {
        dismissLegacySettings() {
            this.legacySettings = false;

            Vue.SearchwpSetSetting('legacy_engines', JSON.stringify(false)).then((response) => {});
        },
        resetIndex() {
            let self = this;

            self.dirtyIndex = false;

            EventBus.$emit('indexReset');

            self.waiting = true;

            Vue.SearchwpResetIndex().then((response) => {
                // Since we saved, we need to update the initial fingerprint
                self.initialFingerprint = JSON.parse(JSON.stringify(self.fingerprint));

                // If the alternate indexer is in use, we need to reload so the alternate indexer can be triggered
                if (_SEARCHWP_VARS.data.misc.alternate_indexer) {
                    window.location.reload();
                } else {
                    self.waiting = false;
                }
            }).catch(function (response) {
                alert('ERROR SEARCHWPINDEXRESET')
            });
        },
        addEngine() {
            let d = new Date();
            let engineHash = 'searchwp_engine_hash_' + md5( 'searchwp_engine_hash_' + d.getTime() );

            Vue.set(this.engines, engineHash, this.$root.engine_model);
        },
        saveEngines() {
            let self = this;
            let hadLegacySettings = false;

            self.saving = true;
            self.doingSave = true;

            // Update current fingerprint
            self.fingerprint = self.generateFingerprint();

            // If engines are being saved, it means that settings are no longer legacy
            if (self.legacySettings) {
                self.legacySettings = false;
                hadLegacySettings = true;
            }

            Vue.SearchwpSetSetting('engines', JSON.stringify(self.engines)).then((response) => {
                self.saving = false;

                // If engines are being saved, it means that settings are no longer legacy
                if (hadLegacySettings) {
                    self.dismissLegacySettings();
                }

                self.afterSaveEngines();
            }).catch(function (response) {
                alert(self.i18n.problemSavingEngineSettings);
            });
        },
        afterSaveEngines() {
            var self = this;

            // Since we're saving settings, initial settings must be true
            if (!this.initialSettingsSaved) {
                this.initialSettingsSaved = true;

                // Kick off the indexer
                this.resetIndex();
            } else {
                // Signal that the index is dirty after save and needs to be rebuilt
                self.dirtyIndex = self.isIndexDirty();
            }

            // Emit the model to components watching for engine saves
            EventBus.$emit('enginesSaved', {
                initialFingerprint: JSON.stringify(self.initialFingerprint),
                currentFingerprint: JSON.stringify(self.fingerprint),
                engines: self.engines,
                dirtyIndex: self.dirtyIndex
            });

            Vue.SearchwpSetSetting('index_dirty', JSON.stringify(self.dirtyIndex)).then((response) => {});

            // Show confirmation, then restore save button
            self.saved = true;
            setTimeout(function(){
                self.doingSave = false;
                self.saved = false;
            }, 1600);
        },
        isIndexDirty() {
            let indexDirty = false;

            // The index is dirty if any of the following is true:
            // - A post type that was indexed has been removed
            // - Change in taxonomies
            // - Change in metadata
            // - Change in rules

            // To determine whether a post type has been removed from the settings we can
            // compare the initial fingerprint post types with the current post types
            // We cannot simply count the enabled post types because if Posts were removed
            // and Pages added at the same time, we'd still have 1 post type, but it's different

            // Let's loop through the initial fingerprint and compare each value to the current
            // fingerprint; if the post type is missing it has been removed
            for (let i = 0; i < this.initialFingerprint.postTypes.length; i++) {
                if (this.fingerprint.postTypes.indexOf(this.initialFingerprint.postTypes[ i ]) == -1) {
                    indexDirty = true;
                }
            }

            // If the post types didn't change, we need to check the other criteria
            // This is more straightforward because any change means an unoptimized index
            if (!indexDirty) {
                // We want a copy of the fingerprints to manipulate without consequence
                let initialFingerprint = JSON.parse(JSON.stringify(this.initialFingerprint));
                let fingerprint = JSON.parse(JSON.stringify(this.fingerprint));

                // Post types are a special circumstance previously handled, they no longer apply
                delete initialFingerprint.postTypes;
                delete fingerprint.postTypes;

                // At this point it's a simple comparison of the two fingerprints
                indexDirty = JSON.stringify(initialFingerprint) !== JSON.stringify(fingerprint);
            }

            return indexDirty;
        },
        generateFingerprint() {
            // A fingerprint identifies the index-influencing engine attributes in play.
            // Context: if a fingerprint differs from the fingerprint of the existing
            // index, the index is no longer accurate and must be rebuilt.

            // At the base, a fingerprint is defined by the enabled custom post types.
            // Only enabled custom post types are indexed, because there's no reason
            // to index anything else.

            // The two attributes we're checking are taxonomies and custom fields,
            // so we're going to get a list across all engines, make the list unique
            // and sort the list, and then we have our fingerprint for comparison

            // This works because SearchWP uses a single index for all engines, they all
            // utilize different data stored in the index. That said, it's almost as though
            // we have one big giant engine with a bunch of attributes, we need to consider
            // the big picture across all engines, and then whatever each individual engine
            // uses will be integrated within the fingerprint.

            let fingerprint = {

                // The fingerprint is affected if admin searching is enabled; all post types must be indexed
                adminSearch: _SEARCHWP_VARS.data.misc.admin_search,

                // When a post type is removed it means there's overhead in the index
                postTypes: [],

                // Only enabled taxonomies are indexed
                taxonomies: [],

                // Only chosen meta keys are indexed
                metadata: [],

                // Rules affect what's in the index
                rules: [],
            };

            // Find all enabled post types
            for (let engine in this.engines) {
                if (this.engines.hasOwnProperty(engine)){
                    for (let postType in this.engines[ engine ]) {
                        if (this.engines[ engine ].hasOwnProperty(postType)){
                            if (this.engines[ engine ][ postType ].hasOwnProperty('enabled')) {
                                let enginePostType = this.engines[ engine ][ postType ];

                                if (enginePostType.enabled) {
                                    fingerprint.postTypes.push(postType);

                                    // Find all taxonomies with a weight > 0
                                    keyfinder(enginePostType, 'tax').forEach(function(postTypeTaxonomies){
                                        for (let taxonomy in postTypeTaxonomies){
                                            if (postTypeTaxonomies.hasOwnProperty(taxonomy)){

                                                // If the weight is zero, it's not part of the fingerprint because it won't be indexed
                                                if (postTypeTaxonomies[ taxonomy ] > 0) {
                                                    fingerprint.taxonomies.push(postType + '_' + taxonomy);
                                                }
                                            }
                                        }
                                    });

                                    // Find all metakeys
                                    keyfinder(enginePostType, 'cf').forEach(function(postTypeMetakeyPairs){
                                        for (let metakeyPair in postTypeMetakeyPairs){
                                            if (postTypeMetakeyPairs.hasOwnProperty(metakeyPair)){
                                                fingerprint.metadata.push(postType + '_' + postTypeMetakeyPairs[ metakeyPair ].metakey);
                                            }
                                        }
                                    });

                                    // Find all rules
                                    keyfinder(enginePostType, 'options').forEach(function(postTypeOptions){
                                        for (let option in postTypeOptions){
                                            if (postTypeOptions.hasOwnProperty(option)){
                                                if (0 === option.indexOf('limit_to_') || 0 === option.indexOf('exclude_')){
                                                    fingerprint.rules.push(postType + '_' + option);
                                                }
                                            }
                                        }
                                    });
                                }
                            }
                        }
                    }
                }
            }

            // There will be redundancies here, but duplicates interfere with the accuracy of the fingerprint
            [ 'postTypes', 'taxonomies', 'metadata', 'rules' ].forEach(function(criteria){
                // We need each criteria to be index-wide across all engines
                fingerprint[ criteria ] = unique( fingerprint[ criteria ] );
                fingerprint[ criteria ].sort(function (a, b) {
                    if (a < b) {
                        return -1;
                    } else if (a > b) {
                        return 1;
                    }

                    return 0;
                });
            });

            return fingerprint;
        }
    },
    created: function() {
        // When a taxonomy or custom field (across all engines) has been added
        // or removed we need to rebuild the index, because only those taxonomies
        // or custom fields are indexed so we're going to listen for those events
        // and determine whether the index needs to be rebuilt for accuracy.
        // Given that, every engine needs to listen to this event and when observed
        // all engines must report their engine configuration for further evaluation.

        let self = this;

        // We need an initial fingerprint for comparison
        self.initialFingerprint = this.generateFingerprint();
        self.fingerprint = this.initialFingerprint;

        EventBus.$on('indexReset', function() {
            self.dirtyIndex = false;

            // We also need to tell the root that the index is no longer dirty
            Vue.set(self.$root.$data.misc, 'index_dirty', false);
        });
    }
}
</script>

<style lang="scss">
    .searchwp-is-waiting {
        opacity: 0.5;
        pointer-events: none;
        cursor: wait;
    }

    .searchwp-engines * {
        box-sizing: border-box;
    }

    .wp-core-ui .searchwp-button-message {
        background: transparent !important;
        border-color: transparent !important;
        box-shadow: none !important;

        .dashicons {
            width: 26px;
            height: 28px;
            font-size: 28px;
        }
    }

    .searchwp-engines-actions {
        margin: 0;
        padding: 0.5em 0 0;
        list-style: none;
        display: flex;
        align-items: center;

        > * {
            margin: 0 1em 0 0;
        }

    }

    .wp-core-ui {
        // We don't want the top margin on buttons here
        .searchwp-engines-actions .button.searchwp-button {
            margin-top: 0;
        }

        .searchwp-success {

            .button {
                color: #43ad4c; // Slightly darker than Message's success because we're not on white
            }
        }
    }

    @media screen and (max-width: 1023px) {
        .searchwp-engines-index-stats {
            flex-direction: column-reverse;
            width: 100%;

            .searchwp-engines,
            .searchwp-index-stats {
                width: 100%;
                margin: 0;
            }

            .searchwp-index-stats {
                margin-bottom: 1em;
            }
        }
    }
</style>
