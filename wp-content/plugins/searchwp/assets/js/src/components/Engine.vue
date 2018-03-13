<template>
    <div class="searchwp-engine postbox">

        <h3 class="hndle">
            <span v-if="!editingLabel"
                @click="settings.hasOwnProperty('searchwp_engine_label') ? editingLabel = true : editingLabel = false"
                :class="{ 'searchwp-hndle-default' : ! settings.hasOwnProperty('searchwp_engine_label') }">{{ label }}
                <span class="dashicons dashicons-edit" v-if="settings.hasOwnProperty('searchwp_engine_label')"></span>
            </span>
            <input v-model="label"
                    @input="updateName()"
                    @keyup.enter="editingLabel = false"
                    @keyup.esc="editingLabel = false"
                    v-if="settings.hasOwnProperty('searchwp_engine_label') && (editingLabel || !settings.searchwp_engine_label)"/>
            <span class="button"
                @click.prevent="editingLabel = ! (editingLabel && settings.searchwp_engine_label)"
                v-if="settings.hasOwnProperty('searchwp_engine_label') && (editingLabel || !settings.searchwp_engine_label)">{{ i18n.done }}</span>
            <code v-if="settings.hasOwnProperty('searchwp_engine_label')">{{ getName }}</code>
            <a :href="$root.misc.stats_url + '&tab=' + getName">{{ i18n.statistics }}</a>
        </h3>

        <div class="inside">

            <p v-if="!settings.hasOwnProperty('searchwp_engine_label')" class="description searchwp-engine-note"><span class="dashicons dashicons-info"></span>{{ i18n.defaultEngineNote }}</p>
            <p v-else class="description searchwp-engine-note">{{ i18n.engineNote }}</p>

            <p v-if="!hasPostTypes" class="description searchwp-engine-note"><strong>{{ i18n.note }}</strong> {{ i18n.engineNoteNone }}</p>

            <div class="searchwp-engine-post-type"
                v-for="(postType, postTypeName) in settings"
                :key="postTypeName"
                v-if="postTypeName !== 'searchwp_engine_label' && model.objects[ postTypeName ].enabled">

                <div v-if="attributes && attributes[ postTypeName ]">

                    <h4 class="searchwp-engine-post-type__heading">
                        <span @click="toggleDetails(postTypeName)" class="searchwp-engine-post-type__label">
                            <span v-if="!details.includes(postTypeName)" class="dashicons dashicons-arrow-right"></span>
                            <span v-if="details.includes(postTypeName)" class="dashicons dashicons-arrow-down"></span>
                            {{ attributes[ postTypeName ].label }}
                        </span>
                        <searchwp-remove
                            v-if="details.includes(postTypeName)"
                            :text="i18n.exclude"
                            @click.native.prevent="removePostType(postTypeName)"/>
                    </h4>

                    <div v-if="details.includes(postTypeName)" class="searchwp-engine-post-type__details">

                        <div class="searchwp-engine-post-type__weights">
                            <table v-if="hasAttributes(postTypeName)">
                                <colgroup>
                                    <col class="searchwp-engine-post-type-attribute"/>
                                    <col class="searchwp-engine-post-type-attribute-weight"/>
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th>{{ i18n.attribute }}</th>
                                        <th>{{ i18n.weightMultiplier }}</th>
                                    </tr>
                                </thead>
                                <tbody v-if="attributes[ postTypeName ].supports && model.objects[ postTypeName ]">

                                    <!-- Native attributes (i.e. post_type_supports) -->
                                    <tr v-for="(supports, supportsKey) in attributes[ postTypeName ].supports"
                                        v-if="model.objects[ postTypeName ].weights[ supportsKey ]"
                                        :key="attributes[ postTypeName ].name + supportsKey">
                                        <td>
                                            {{ supports }}
                                            <searchwp-remove
                                                :icon="true"
                                                @click.native.prevent="model.objects[ postTypeName ].weights[ supportsKey ] = 0"/>
                                        </td>
                                        <td>
                                            <searchwp-slider v-model="model.objects[ postTypeName ].weights[ supportsKey ]"
                                                :height="3"
                                                :dot-size="11"
                                                :min="1"
                                                :max="weightMax"
                                                :interval="1"
                                                :formatter="getTooltipText"
                                                :tooltip="'hover'"
                                                :bg-style="{ backgroundColor: '#dddddd' }"
                                                :process-style="{ backgroundColor: '#159FD2' }"
                                                :tooltip-style="{ backgroundColor: '#159FD2', color: '#ffffff', fontSize: '13px' }"></searchwp-slider>
                                        </td>
                                    </tr>

                                    <!-- Taxonomies -->
                                    <tr v-if="model.objects[ postTypeName ].weights.tax && unusedTaxonomies(postTypeName).length !== Object.keys(model.objects[ postTypeName ].weights.tax).length"
                                        class="searchwp-engine-post-type__attribute-category">
                                        <th>{{ i18n.taxonomies }}</th>
                                    </tr>
                                    <tr v-for="(taxonomy, taxonomyKey) in attributes[ postTypeName ].taxonomies"
                                        v-if="model.objects[ postTypeName ].weights.tax[ attributes[ postTypeName ].taxonomies[ taxonomyKey ].name ]"
                                        :key="attributes[ postTypeName ].name + 'tax' + taxonomyKey">
                                        <td>
                                            {{ attributes[ postTypeName ].taxonomies[ taxonomyKey ].label }}
                                            <searchwp-remove
                                                :icon="true"
                                                @click.native.prevent="removeTaxonomy(postTypeName, taxonomyKey)"/>
                                        </td>
                                        <td>
                                            <searchwp-slider v-model="model.objects[ postTypeName ].weights.tax[ attributes[ postTypeName ].taxonomies[ taxonomyKey ].name ]"
                                                :height="3"
                                                :dot-size="11"
                                                :min="1"
                                                :max="weightMax"
                                                :interval="1"
                                                :formatter="getTooltipText"
                                                :tooltip="'hover'"
                                                :bg-style="{ backgroundColor: '#dddddd' }"
                                                :process-style="{ backgroundColor: '#159FD2' }"
                                                :tooltip-style="{ backgroundColor: '#159FD2', color: '#ffffff', fontSize: '13px' }"></searchwp-slider>
                                        </td>
                                    </tr>

                                    <!-- Document Properties -->
                                    <tr v-if="'attachment' == postTypeName && hasDocumentProperties"
                                        class="searchwp-engine-post-type__attribute-category">
                                        <th>{{ i18n.documentProperties }}</th>
                                    </tr>
                                    <tr v-for="(metakey, metakeyKey) in model.objects[ postTypeName ].weights.cf"
                                        v-if="'searchwp_content' == model.objects[ postTypeName ].weights.cf[ metakeyKey ].metakey || 'searchwp_pdf_metadata' == model.objects[ postTypeName ].weights.cf[ metakeyKey ].metakey"
                                        :key="attributes[ postTypeName ].name + 'meta' + metakeyKey">
                                        <td>
                                            <span v-if="'searchwp_content' == model.objects[ postTypeName ].weights.cf[ metakeyKey ].metakey">{{ i18n.documentContent }}</span>
                                            <span v-else-if="'searchwp_pdf_metadata' == model.objects[ postTypeName ].weights.cf[ metakeyKey ].metakey">{{ i18n.pdfMetadata }}</span>
                                            <searchwp-remove
                                                :icon="true"
                                                @click.native.prevent="removeMetakey( postTypeName, metakeyKey )"/>
                                        </td>
                                        <td>
                                            <searchwp-slider v-model="model.objects[ postTypeName ].weights.cf[ metakeyKey ].weight"
                                                :height="3"
                                                :dot-size="11"
                                                :min="1"
                                                :max="weightMax"
                                                :interval="1"
                                                :formatter="getTooltipText"
                                                :tooltip="'hover'"
                                                :bg-style="{ backgroundColor: '#dddddd' }"
                                                :process-style="{ backgroundColor: '#159FD2' }"
                                                :tooltip-style="{ backgroundColor: '#159FD2', color: '#ffffff', fontSize: '13px' }"></searchwp-slider>
                                        </td>
                                    </tr>

                                    <!-- Custom Fields -->
                                    <tr v-if="hasCustomFields( postTypeName )"
                                        class="searchwp-engine-post-type__attribute-category">
                                        <th>{{ i18n.customFields }}</th>
                                    </tr>
                                    <tr v-for="(metakey, metakeyKey) in model.objects[ postTypeName ].weights.cf"
                                        v-if="!(postTypeName == 'attachment' && ('searchwp_content' == model.objects[ postTypeName ].weights.cf[ metakeyKey ].metakey || 'searchwp_pdf_metadata' == model.objects[ postTypeName ].weights.cf[ metakeyKey ].metakey))"
                                        :key="attributes[ postTypeName ].name + 'meta' + metakeyKey">
                                        <td>
                                            <span v-if="'searchwpcfdefault' === model.objects[ postTypeName ].weights.cf[ metakeyKey ].metakey || 'searchwp cf default' === model.objects[ postTypeName ].weights.cf[ metakeyKey ].metakey">
                                                <em>{{ i18n.anyCustomField }}</em>
                                                <span class="dashicons dashicons-star-filled"></span>
                                            </span>
                                            <span v-else>
                                                {{ model.objects[ postTypeName ].weights.cf[ metakeyKey ].metakey }}
                                            </span>
                                            <searchwp-remove
                                                :icon="true"
                                                @click.native.prevent="removeMetakey( postTypeName, metakeyKey )"/>
                                        </td>
                                        <td>
                                            <searchwp-slider v-model="model.objects[ postTypeName ].weights.cf[ metakeyKey ].weight"
                                                :height="3"
                                                :dot-size="11"
                                                :min="1"
                                                :max="weightMax"
                                                :interval="1"
                                                :formatter="getTooltipText"
                                                :tooltip="'hover'"
                                                :bg-style="{ backgroundColor: '#dddddd' }"
                                                :process-style="{ backgroundColor: '#159FD2' }"
                                                :tooltip-style="{ backgroundColor: '#159FD2', color: '#ffffff', fontSize: '13px' }"></searchwp-slider>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                            <searchwp-message v-else :type="'warning'">
                                <p>{{ i18n.withoutAttributesNoResults }}</p>
                            </searchwp-message>

                            <searchwp-dropdown
                                v-if="unusedAttributes(postTypeName)"
                                :type="'contentTypes'"
                                :postType="postTypeName"
                                :buttonText="i18n.addAttribute"/>

                            <div class="searchwp-document-notes" v-if="'attachment' == postTypeName && (!$root.misc.ziparchive || !$root.misc.domdocument)">
                                <p v-if="!$root.misc.ziparchive" class="description"><strong>{{ i18n.note }}</strong> <code>ZipArchive</code> {{ i18n.notAvailableNoIndex }}</p>
                                <p v-if="!$root.misc.domdocument" class="description"><strong>{{ i18n.note }}</strong> <code>DOMDocument</code> {{ i18n.notAvailableNoIndex }}</p>
                            </div>

                        </div>

                        <div class="searchwp-engine-post-type__options">

                            <div>
                                <h5>{{ i18n.weightTransfer }}</h5>
                                <searchwp-input-text
                                    :label="i18n.transferWeightTo"
                                    :placeholder="i18n.singlePostId"
                                    v-if="$root.objects[ postTypeName ].attribution == 'id'"
                                    v-model="model.objects[ postTypeName ].options.attribute_to"/>
                                <searchwp-input-checkbox
                                    :label="i18n.transferWeightToParent"
                                    :checked="model.objects[ postTypeName ].options.parent"
                                    v-if="$root.objects[ postTypeName ].attribution == 'parent'"
                                    v-model="model.objects[ postTypeName ].options.parent"/>
                            </div>

                            <div v-if="stemming_supported">
                                <h5>{{ i18n.options }}</h5>
                                <searchwp-input-checkbox
                                    :label="i18n.useKeywordStem"
                                    :checked="model.objects[ postTypeName ].options.stem"
                                    v-model="model.objects[ postTypeName ].options.stem"/>
                            </div>

                            <div>
                                <h5>{{ i18n.rules }}</h5>
                                <searchwp-input-text
                                    :label="i18n.excludedIds"
                                    :placeholder="i18n.commaSeparatedIds"
                                    v-model="model.objects[ postTypeName ].options.exclude"/>

                                <div v-if="'attachment' == postTypeName" class="searchwp-input-taxonomy-terms">
                                    <label>Limit to file type</label>
                                    <div>
                                        <multiselect
                                            v-model="model.objects[ postTypeName ].options.mimes"
                                            label="label"
                                            track-by="value"
                                            :placeholder="i18n.choose"
                                            :options="$root.misc.mimes"
                                            :multiple="true"
                                            :searchable="false"
                                            :internal-search="false"
                                            :clear-on-select="true"
                                            :close-on-select="true"
                                            :max-height="300"
                                            :show-no-results="false"
                                            :hide-selected="true">
                                        </multiselect>
                                    </div>
                                </div>

                                <!-- Taxonomy exclusions -->
                                <searchwp-input-taxonomy-terms
                                    v-for="(taxonomy, taxonomyKey) in attributes[ postTypeName ].taxonomies"
                                    v-if="model.objects[ postTypeName ].options.hasOwnProperty( 'exclude_' + attributes[ postTypeName ].taxonomies[ taxonomyKey ].name)"
                                    :label="i18n.excluded + ' ' + attributes[ postTypeName ].taxonomies[ taxonomyKey ].label"
                                    :key="attributes[ postTypeName ].name + 'tax' + taxonomyKey"
                                    :mode="'exclude'"
                                    :postType="postTypeName"
                                    @termsUpdated="excludedTermsUpdated"
                                    :taxonomy="attributes[ postTypeName ].taxonomies[ taxonomyKey ].name"/>

                                <!-- Taxonomy limiters -->
                                <searchwp-input-taxonomy-terms
                                    v-for="(taxonomy, taxonomyKey) in attributes[ postTypeName ].taxonomies"
                                    v-if="model.objects[ postTypeName ].options.hasOwnProperty( 'limit_to_' + attributes[ postTypeName ].taxonomies[ taxonomyKey ].name)"
                                    :label="i18n.limitTo + ' ' + attributes[ postTypeName ].taxonomies[ taxonomyKey ].label"
                                    :key="attributes[ postTypeName ].name + 'tax' + taxonomyKey"
                                    :mode="'limit_to'"
                                    :postType="postTypeName"
                                    @termsUpdated="limitedToTermsUpdated"
                                    :taxonomy="attributes[ postTypeName ].taxonomies[ taxonomyKey ].name"/>

                                <searchwp-dropdown
                                    :type="'rules'"
                                    v-if="attributes[ postTypeName ].taxonomies.length"
                                    :postType="postTypeName"
                                    :buttonText="i18n.addLimitExcludeRule"/>
                            </div>

                        </div>

                    </div>

                </div>
            </div>

            <ul class="searchwp-engine-actions">
                <li>
                    <searchwp-dropdown
                        v-if="unusedPostTypes().length"
                        :type="'postTypes'"
                        :position="'below'"
                        :buttonText="i18n.addPostType"/>
                </li>
                <li v-if="model.name !== 'default'" class="searchwp-remove-engine">
                    <searchwp-remove :text="i18n.deleteEngine" @click.native.prevent="removeEngine()"></searchwp-remove>
                </li>
            </ul>

        </div>


    </div>
</template>

<script>
import Vue from 'vue';
import { EventBus } from './../EventBus.js';
import md5 from 'md5';
import slugify from 'slugify';
import vueSlider from 'vue-slider-component';
import SearchwpRemove from './Remove.vue';
import SearchwpButton from './Button.vue';
import SearchwpInputText from './InputText.vue';
import SearchwpInputCheckbox from './InputCheckbox.vue';
import SearchwpDropdown from './Dropdown.vue';
import SearchwpInputTaxonomyTerms from './InputTaxonomyTerms.vue';
import SearchwpMessage from './Message.vue';

export default {
    name: 'SearchwpEngine',
    components: {
        'searchwp-slider': vueSlider,
        'searchwp-remove': SearchwpRemove,
        'searchwp-button': SearchwpButton,
        'searchwp-dropdown': SearchwpDropdown,
        'searchwp-input-text': SearchwpInputText,
        'searchwp-input-checkbox': SearchwpInputCheckbox,
        'searchwp-input-taxonomy-terms': SearchwpInputTaxonomyTerms,
        'searchwp-message': SearchwpMessage
    },
    computed: {
        getName: function() {
            return this.model.name;
        },
        hasDocumentProperties: function() {
            // If the meta key is present in the unusedMetakeys then it is not part of the engine
            // Therefore, if the index is missing from the unusedMetakeys, then we have it here
            let hasDocumentContent = -1 === this.indexOfObjectPropertyValue(
                                        this.unusedMetakeys( 'attachment', true ),
                                        'name',
                                        'searchwp_content');
            let hasPdfMetadata = -1 === this.indexOfObjectPropertyValue(
                                        this.unusedMetakeys( 'attachment', true ),
                                        'name',
                                        'searchwp_pdf_metadata');

            // If we have document content OR PDF metadata
            return hasDocumentContent || hasPdfMetadata;
        },
        hasPostTypes: function() {
            let postTypes = [];

            for (var postType in this.model.objects) {
                if ('searchwp_engine_label' !== postType && this.model.objects[ postType ].enabled) {
                    postTypes.push(postType);
                }
            }

            return postTypes.length ? true : false;
        }
    },
    methods: {
        excludedTermsUpdated(taxPostType, taxTaxonomy, taxSelectedTermsCount) {
            if (taxSelectedTermsCount<1) {
                // This taxonomy no longer applies
                Vue.delete(this.model.objects[ taxPostType ].options, 'exclude_' + taxTaxonomy);
            }
        },
        limitedToTermsUpdated(taxPostType, taxTaxonomy, taxSelectedTermsCount) {
            if (taxSelectedTermsCount<1) {
                // This taxonomy no longer applies
                Vue.delete(this.model.objects[ taxPostType ].options, 'limit_to_' + taxTaxonomy);
            }
        },
        removeTaxonomy(postTypeName, taxonomyKey) {
            // Reset the weight to zero (effectively removes it)
            this.model.objects[ postTypeName ].weights.tax[ this.attributes[ postTypeName ].taxonomies[ taxonomyKey ].name ] = 0;

            // This change influences the index
            this.indexInfluenced();
        },
        indexInfluenced() {
            // All engines are listening for this emit. When received each will report
            // its model to listeners, which in turn can further process for analysis.
            // This is just the trigger that kicks off the event overall, no data here.
            EventBus.$emit('indexInfluenced');
        },
        updateName() {
            let newName = slugify(this.label.replace(/ /gi, '_'), {
                replacement: '_',
                remove: /[^a-z0-9_]/gi,
                lower: true
            });
            // TODO: We should only need to update one model...
            Vue.set(this.model, 'name', newName);
            Vue.set(this.$parent.engines[ this.ref ], 'searchwp_engine_label', this.label);
        },
        hasAttributes(postTypeName) {
            let attributes = [];
            let model = this.model.objects[ postTypeName ];

            // Native
            for (var supported in this.attributes[ postTypeName ].supports) {
                // let supportsKey = this.attributes[ postTypeName ].supports[ supported ];
                if (model.weights[ supported ]) {
                    attributes.push( supported );
                }
            }

            // Taxonomies
            for (var taxonomyKey in this.attributes[ postTypeName ].taxonomies) {
                let taxonomy = this.attributes[ postTypeName ].taxonomies[ taxonomyKey ].name;

                // We need to check if this taxonomy was added since the last save...
                if (!this.model.objects[ postTypeName ].weights.tax || Array.isArray(this.model.objects[ postTypeName ].weights.tax)) {
                    Vue.set(this.model.objects[ postTypeName ].weights, 'tax', {});
                }

                if (model.weights.tax[ taxonomy ]) {
                    attributes.push( taxonomy );
                }
            }

            // Custom Fields
            for (var metakeyKey in model.weights.cf) {
                let metakey = model.weights.cf[ metakeyKey ].metakey;
                attributes.push( metakey );
            }

            return attributes.length ? true : false;
        },
        removeEngine() {
            Vue.delete(this.$root.engines, this.ref);
        },
        removePostType(postTypeName) {
            // Disable the post type
            this.model.objects[ postTypeName ].enabled = false;

            // Remove custom fields to prevent index dirt
            Vue.set(this.model.objects[ postTypeName ].weights, 'cf', {});

            // Remove taxonomies to prevent index dirt
            if (this.model.objects[ postTypeName ].weights.tax) {
                for (var taxonomyKey in this.attributes[ postTypeName ].taxonomies) {
                    let taxonomy = this.attributes[ postTypeName ].taxonomies[ taxonomyKey ].name;
                    if (this.model.objects[ postTypeName ].weights.tax[ taxonomy ]) {
                        this.model.objects[ postTypeName ].weights.tax[ taxonomy ] = 0;
                    }
                }
            }

            // TODO: Rules are not removed (maybe leave them for convenience? In a way allows for 'undo')

            // Also remove the flag that these details are being shown
            if (this.details.indexOf(postTypeName) > -1) {
                this.details.splice(this.details.indexOf(postTypeName), 1);
            }
        },
        indexOfObjectPropertyValue(array, property, value) {
            for (var i = 0; i < array.length; i += 1 ) {
                if (array[ i ][ property ] === value ) {
                    return i;
                }
            }

            return -1;
        },
        hasCustomFields( postTypeName ) {
            // Attachments are a special use case because they have Custom Fields for
            // document content and PDF metadata. If those are the only two Custom Fields
            // added to the engine, there's technically no Custom Fields added
            let theseCustomFields = this.model.objects[ postTypeName ].weights.cf && Object.keys(this.model.objects[ postTypeName ].weights.cf).length > 0;

            if (theseCustomFields && 'attachment' == postTypeName) {
                // Check to see if the only Custom Fields are reserved SearchWP meta keys
                let customFields = this.model.objects[ postTypeName ].weights.cf;
                let reserved = 0;

                for (var customField in customFields) {
                    if (customFields[ customField ].metakey == 'searchwp_content') {
                        reserved++;
                    }
                    if (customFields[ customField ].metakey == 'searchwp_pdf_metadata') {
                        reserved++;
                    }
                }

                if (Object.keys(this.model.objects[ postTypeName ].weights.cf).length <= reserved) {
                    theseCustomFields = false;
                }
            }

            return theseCustomFields;
        },
        removeMetakey (postType, metakey) {
            Vue.delete(this.model.objects[ postType ].weights.cf, metakey)
        },
        unusedPostTypes() {
            let unusedPostTypes = [];
            let enginePostTypes = this.model.objects;

            for (var enginePostType in enginePostTypes) {
                if ('searchwp_engine_label' !== enginePostType && ! enginePostTypes[ enginePostType ].enabled){
                    unusedPostTypes.push({
                        name: enginePostType,
                        label: this.$root.$data.objects[ enginePostType ].label,
                        value: enginePostType
                    });
                }
            }

            return unusedPostTypes;
        },
        unusedNativeAttributes( postType ) {
            let attributes = [];
            let supports = this.$root.$data.objects[ postType ].supports;

            for (var key in supports) {
                attributes.push(key);
            }

            let attributeWeights = this.model.objects[ postType ].weights;

            // A native attribute is unused if it has a weight of zero
            return attributes.filter(function (attribute) {
                return attributeWeights[ attribute ] == 0;
            });
        },
        unusedTaxonomies( postType ) {
            let taxonomies = this.$root.$data.objects[ postType ].taxonomies;
            let taxonomyWeights = this.model.objects[ postType ].weights.tax;

            // A taxonomy is unused if it has a weight of zero
            return taxonomies.filter(function (taxonomy) {
                return taxonomyWeights[ taxonomy.name ] == 0;
            });
        },
        unusedMetakeys( postType, unfiltered ) {
            let metakeys = [];
            let availableMetakeys = this.$root.$data.objects[ postType ].meta_keys;
            let metakeyWeights = this.model.objects[ postType ].weights.cf;

            for (var i in availableMetakeys) {
                let metakey = availableMetakeys[i];
                let applicable = true;

                for (var key in metakeyWeights) {
                    if (metakeyWeights[ key ].metakey == metakey) {
                        applicable = false;
                        break;
                    }
                }

                if ( applicable ) {
                    if (unfiltered || ('searchwp_content' !== metakey && 'searchwp_pdf_metadata' !== metakey)){
                        metakeys.push({
                            name: metakey,
                            label: metakey,
                            value: metakey
                        });
                    }
                }
            }

            return metakeys;
        },
        unusedAttributes( postType ) {
            return this.unusedNativeAttributes(postType).length || this.unusedTaxonomies(postType).length || this.unusedMetakeys(postType, true).length;
        },
        addContentType(selectedOption) {
            console.log(selectedOption);
        },
        toggleDetails(postType) {
            if (this.details.includes(postType)) {
                this.details = this.details.filter(function (item){
                    return item != postType;
                });
            } else {
                this.details.push(postType);
            }
        },
        getTooltipText(value) {
            // The maximum value can be filtered, so we need to define 'zones'

            if (value < Math.ceil(this.weightMax * 0.02)) {
                return _SEARCHWP_VARS.i18n.minimum + ' (' + value + ')';
            }

            if (value < Math.ceil(this.weightMax * 0.3)) {
                return _SEARCHWP_VARS.i18n.a_little + ' (' + value + ')';
            }

            if (value < Math.ceil(this.weightMax * 0.65)) {
                return _SEARCHWP_VARS.i18n.average + ' (' + value + ')';
            }

            if (value < Math.ceil(this.weightMax * 0.99)) {
                return _SEARCHWP_VARS.i18n.a_lot + ' (' + value + ')';
            }

            return _SEARCHWP_VARS.i18n.maximum + ' (' + value + ')';
        }
    },
    data: function() {
        return {
            model: {
                name: this.name,
                objects: {}
            },
            dirtyIndex: false,
            ref: this.name,
            label: _SEARCHWP_VARS.i18n.default,
            editingLabel: false,
            attributes: this.$root.$data.objects,
            stemming_supported: this.$root.$data.stemming_supported,
            weightMax: parseInt( _SEARCHWP_VARS.data.misc.max_weight, 10 ),
            i18n: {
                addAttribute: _SEARCHWP_VARS.i18n.add_attribute,
                addContentType: _SEARCHWP_VARS.i18n.add_content_type,
                addLimitExcludeRule: _SEARCHWP_VARS.i18n.add_limit_exclude_rule,
                addPostType: _SEARCHWP_VARS.i18n.add_post_type,
                anyCustomField: _SEARCHWP_VARS.i18n.any_custom_field,
                assignWeightTo: _SEARCHWP_VARS.i18n.assign_weight_to,
                attribute: _SEARCHWP_VARS.i18n.attribute,
                attributeResultsTo: _SEARCHWP_VARS.i18n.attribute_results_to,
                attribution: _SEARCHWP_VARS.i18n.attribution,
                choose: _SEARCHWP_VARS.i18n.choose,
                commaSeparatedIds: _SEARCHWP_VARS.i18n.comma_separated_ids,
                contentType: _SEARCHWP_VARS.i18n.content_type,
                customField: _SEARCHWP_VARS.i18n.custom_field,
                customFields: _SEARCHWP_VARS.i18n.custom_fields,
                default: _SEARCHWP_VARS.i18n.default,
                defaultEngineNote: _SEARCHWP_VARS.i18n.default_engine_note,
                deleteEngine: _SEARCHWP_VARS.i18n.delete_engine,
                documentContent:  _SEARCHWP_VARS.i18n.document_content,
                documentProperties: _SEARCHWP_VARS.i18n.document_properties,
                done: _SEARCHWP_VARS.i18n.done,
                engineNote: _SEARCHWP_VARS.i18n.engine_note,
                engineNoteNone: _SEARCHWP_VARS.i18n.engine_note_none,
                exclude:  _SEARCHWP_VARS.i18n.exclude,
                excluded:  _SEARCHWP_VARS.i18n.excluded,
                excludedIds: _SEARCHWP_VARS.i18n.excluded_ids,
                limitTo:  _SEARCHWP_VARS.i18n.limit_to,
                limitedTo:  _SEARCHWP_VARS.i18n.limited_to,
                note: _SEARCHWP_VARS.i18n.note,
                notAvailableNoIndex: _SEARCHWP_VARS.i18n.not_available_no_index,
                options: _SEARCHWP_VARS.i18n.options,
                pdfMetadata:  _SEARCHWP_VARS.i18n.pdf_metadata,
                rules: _SEARCHWP_VARS.i18n.rules,
                singlePostId: _SEARCHWP_VARS.i18n.single_post_id,
                statistics: _SEARCHWP_VARS.i18n.statistics,
                taxonomy: _SEARCHWP_VARS.i18n.taxonomy,
                taxonomies: _SEARCHWP_VARS.i18n.taxonomies,
                transferWeightTo: _SEARCHWP_VARS.i18n.transfer_weight_to,
                transferWeightToParent: _SEARCHWP_VARS.i18n.transfer_weight_to_parent,
                useKeywordStem: _SEARCHWP_VARS.i18n.use_keyword_stem,
                weight: _SEARCHWP_VARS.i18n.weight,
                weightMultiplier: _SEARCHWP_VARS.i18n.weight_multiplier,
                weightAssignment: _SEARCHWP_VARS.i18n.weight_assignment,
                weightTransfer: _SEARCHWP_VARS.i18n.weight_transfer,
                withoutAttributesNoResults: _SEARCHWP_VARS.i18n.without_attributes_no_results
            },
            details: []
        };
    },
    props: {
        settings: {
            type: Object,
            default: {},
            required: false
        },
        name: {
            type: String,
            default: 'default',
            required: true
        }
    },
    created: function() {
        // We need to prep the model to match the existing format
        for (var key in this.settings) {
            if ('searchwp_engine_label' == key) {
                this.label = this.settings[ key ];
                continue;
            }

            this.model.objects[ key ] = this.settings[ key ];
        }

        // Because of the way the legacy engine config is stored, if certain
        // options were not set, they're missing, but we need them in place
        for (var key in this.model.objects) {
            if (this.model.objects[ key ].options && !this.model.objects[ key ].options.hasOwnProperty('stem')) {
                this.model.objects[ key ].options.stem = false;
            }
        }

        // In other cases the value is "0" (string) when we in fact want an empty string
        for (var key in this.model.objects) {
            if (!this.model.objects[ key ].options) {
                break;
            }

            if (!this.model.objects[ key ].options.hasOwnProperty('stem')) {
                this.model.objects[ key ].options.stem = false;
            } else {
                if ('0'===this.model.objects[ key ].options.stem) {
                    this.model.objects[ key ].options.stem = false;
                } else {
                    this.model.objects[ key ].options.stem = !!this.model.objects[ key ].options.stem;
                }
            }

            for (var option in this.model.objects[ key ].options) {
                if (this.model.objects[ key ].options[ option ] === '0') {
                    this.model.objects[ key ].options[ option ] = '';
                }
            }
        }

        if (!this.label) {
            this.label = this.i18n.default;
        }

        // If we're creating a new supplemental engine, the name is going
        // to be a meaningless hash, so let's make that better
        if (this.model.name.substring(0,21) == 'searchwp_engine_hash_') {
            this.updateName();
        }
    }
}
</script>

<style lang="scss">

    .js .postbox.searchwp-engine .hndle {
        cursor: default;
    }

    .searchwp-engine {

        .hndle {
            display: flex;
            align-items: center;

            a {
                display: block;
                margin-left: auto;
                font-size: 12px;
            }

            span {
                cursor: pointer;

                &.searchwp-hndle-default {
                    cursor: default;
                }

                &:hover span {
                    opacity: 0.8;
                }
            }

            span,
            input,
            code {
                display: inline-block;
                margin-right: 1em;
            }

            span span {
                margin-right: 0;
                opacity: 0.35;
            }

            input,
            code {
                font-weight: normal;
            }

            input {
                border: 1px solid #e5e5e5;
            }
        }

        p.description {
            margin: 1.15em 0 0;
        }
    }

    .searchwp-engine-post-type {
        background-color: #fafafa;
        border: 1px solid #ddd;
        border-radius: 2px;
        margin-top: 1em;
    }

    .searchwp-engine-post-type__heading {
        margin: 0;
        padding: 8px 1em 8px 4px; // From .hndle mostly, but left padding accommodates extra space from dashicon
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 14px;
        line-height: 1.4;

        .searchwp-engine-post-type__label {
            display: block;
            flex: 1;
            cursor: pointer;
        }

        a {
            display: block;
            margin-left: 2em;
        }

        .dashicons {

            &:before {
                color: #BCBCBC;
            }
        }
    }

    .searchwp-engine-post-type__details {
        padding: 12px 0.5em;
        display: flex;
        position: relative;
        border-top: 1px solid #ddd;

        > * {
            width: 50%;
        }

        &:before {
            display: block;
            position: absolute;
            content: '';
            width: 1px;
            top: 1.5em;
            bottom: 1.5em;
            left: 50%;
            background: #ddd;
            z-index: 0;
        }
    }

    .searchwp-engine-post-type__weights {
        padding-right: 1em;
        padding-bottom: 0;

        .searchwp-remove {
            text-decoration: none;
        }

        .searchwp-message {
            margin-right: 0.5em;
            margin-left: 0.5em;
        }

        .dashicons {
            margin-top: -2px;
            transform: scale(0.8);
            opacity: 0.5;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            position: relative;
            z-index: 0;
        }

        .searchwp-engine-post-type-attribute {
            width: 60%;
        }

        .searchwp-engine-post-type-attribute-weight {
            width: 40%;
        }

        th {
            text-align: left;
            padding: 0.5em;
        }

        td {
            padding: 0.5em;

            .searchwp-remove {
                display: inline-block;
                visibility: hidden;
            }
        }

        tr:hover {

            td {
                background-color: #f0f0f0;

                .searchwp-remove {
                    visibility: visible;
                }
            }
        }

        // Because of the table row hover state we need to add this margin
        .searchwp-dropdown {
            margin-right: 0.5em;
            margin-left: 0.5em;
        }

        .vue-slider-component {
            margin-top: 2px;
        }
    }

    .searchwp-engine-post-type__options {
        padding: 0.5em 0.5em 0 1.5em;

        > div {
            margin-bottom: 2em;

            &:last-of-type {
                margin-bottom: 0.5em;
            }
        }

        // Mimic the table headings from the other side
        h5 {
            font-size: 13px;
            line-height: 1.4em;
            margin: 0 0 0.25em;
        }
    }

    .searchwp-engine-actions {
        margin: 0;
        padding: 0.5em 0 0;
        list-style: none;
        display: flex;
        align-items: center;
        justify-content: space-between;

        > * {
            margin: 0 1em 0 0;

            &:last-of-type {
                margin-right: 0;
            }
        }

        .searchwp-remove-engine {
            visibility: hidden;
            margin: 0.9em 2px 0.1em auto; // Match button margins, push to the right
            line-height: 26px;
            height: 28px;
        }

        .searchwp-dropdown {
            margin-top: 0;
            border-top: 0;
            padding-top: 0;
        }
    }

    .searchwp-engine .inside {
        padding-bottom: 2px;

        &:hover {
            .searchwp-remove-engine {
                visibility: visible;
            }
        }
    }

    .searchwp-document-notes {
        padding: 10px 0.5em 0;
    }

    .searchwp-engine-note {

        span {
            display: inline-block;
            margin-right: 4px;
            vertical-align: bottom;
        }
    }

    .searchwp-input-taxonomy-terms {
        display: flex;
        justify-content: space-between;
        margin-bottom: 1em;

        label {
            display: block;
            width: 45%;
            padding-top: 0.45em; // Attempt to align with chosen terms
        }

        > div {
            display: block;
            width: 55%;
        }
    }

    @media screen and (max-width: 1279px) {
        .searchwp-engine-post-type__details {
            flex-direction: column;

            > * {
                width: 100%;
            }

            .searchwp-engine-post-type__weights {
                padding-right: 0;
                margin-bottom: 2.5em;
            }

            .searchwp-engine-post-type__options {
                padding: 0.5em;
            }

            &:before {
                display: none;
            }
        }
    }
</style>
