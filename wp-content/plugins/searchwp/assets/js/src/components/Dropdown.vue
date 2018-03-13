<template>
    <div class="searchwp-dropdown">

        <searchwp-button
            v-if="!showing"
            :position="position"
            :popover="'popover' + _uid"
            :buttonText="buttonText"
            @click.native.prevent/>

        <popover :width="position == 'above' ? 180 : 150" :ref="'popoverRef' + _uid" :name="'popover' + _uid">
            <ul v-if="type=='postTypes'">
                <li v-for="unusedPostType in getUnusedPostTypes()"
                    :key="unusedPostType.name"
                    @click="addPostType(unusedPostType.name)">{{ unusedPostType.label }}</li>
                <li v-if="numberOfExcludedPostTypes()" class="searchwp-excluded-types-note">{{ i18n.excluded }}: {{ numberOfExcludedPostTypes() }}</li>
            </ul>
            <ul v-else-if="type=='contentTypes'">
                <li v-if="getUnusedNativeAttributes().length"
                    @click="showDetails('native')">{{ i18n.nativeAttribute }}</li>
                <li v-if="getUnusedTaxonomies().length"
                    @click="showDetails('taxonomy')">{{ i18n.taxonomy }}</li>
                <li v-if="'attachment' == postType && isWithoutCustomField('searchwp_content')"
                    @click="addMetakeyRecord('searchwp_content', true)">{{ i18n.documentContent }}</li>
                <li v-if="'attachment' == postType && isWithoutCustomField('searchwp_pdf_metadata')"
                    @click="addMetakeyRecord('searchwp_pdf_metadata', true)">{{ i18n.pdfMetadata }}</li>
                <li v-if="getUnusedMetakeys().length"
                    @click="showDetails('meta')">{{ i18n.customField }}</li>
            </ul>
            <ul v-else-if="type=='rules'">
                <li v-if="getUnusedRuleTaxonomies('exclude').length"
                    @click="showDetails('excludeTaxonomy')">{{ i18n.excludeByTaxonomy }}</li>
                <li v-if="getUnusedRuleTaxonomies('limit_to').length"
                    @click="showDetails('limitToTaxonomy')">{{ i18n.limitByTaxonomy }}</li>
            </ul>
        </popover>

        <div v-if="showing" class="searchwp-dropdown-choices">

            <multiselect
                v-if="showing=='native'"
                label="label"
                :placeholder="i18n.chooseNativeAttribute"
                :options="getUnusedNativeAttributes()"
                :searchable="false"
                :allow-empty="false"
                :reset-after="true"
                @select="addNativeContentType"></multiselect>

            <multiselect
                v-if="showing=='taxonomy'"
                label="label"
                :placeholder="i18n.chooseTaxonomy"
                :options="getUnusedTaxonomies()"
                :searchable="false"
                :allow-empty="false"
                :reset-after="true"
                @select="addTaxonomy"></multiselect>

            <multiselect
                v-if="showing=='meta'"
                label="label"
                :placeholder="i18n.chooseCustomField"
                :options="getUnusedMetakeys()"
                :searchable="true"
                :allow-empty="true"
                :reset-after="true"
                @select="addMetakey"></multiselect>

            <multiselect
                v-if="showing=='excludeTaxonomy'"
                label="label"
                :placeholder="i18n.chooseTaxonomy"
                :options="getUnusedRuleTaxonomies('exclude')"
                :searchable="false"
                :allow-empty="false"
                :reset-after="true"
                @select="addExcludedTaxonomy"></multiselect>

            <multiselect
                v-if="showing=='limitToTaxonomy'"
                label="label"
                :placeholder="i18n.chooseTaxonomy"
                :options="getUnusedRuleTaxonomies('limit_to')"
                :searchable="false"
                :allow-empty="false"
                :reset-after="true"
                @select="addLimitedToTaxonomy"></multiselect>

            <searchwp-button
                @click.native.prevent="showing=''"
                :buttonText="i18n.done"/>

        </div>

    </div>
</template>

<script>
import Vue from 'vue';
import md5 from 'md5';
import { EventBus } from './../EventBus.js';
import SearchwpButton from './Button.vue';

export default {
    name: 'SearchwpDropdown',
    components: {
        'searchwp-button': SearchwpButton
    },
    methods: {
        numberOfExcludedPostTypes() {
            return Object.keys(this.$root.misc.excluded_from_search).length;
        },
        isWithoutCustomField( metakey ) {
            return -1 !== this.$parent.indexOfObjectPropertyValue(this.getUnusedMetakeys( true ), 'name', metakey);
        },
        getUnusedPostTypes() {
            let unusedPostTypes = [];
            let enginePostTypes = this.$parent.model.objects;

            for (var enginePostType in enginePostTypes) {
                if ('searchwp_engine_label' !== enginePostType && !enginePostTypes[ enginePostType ].enabled){
                    unusedPostTypes.push({
                        name: enginePostType,
                        label: this.$root.$data.objects[ enginePostType ].label,
                        value: enginePostType
                    });
                }
            }

            return unusedPostTypes;
        },
        getUnusedNativeAttributes() {
            // The native attributes for this post type are what the post type supports as defined in the root
            // but multiselect expects a specifically formated array of objects, so we'll need to adapt
            let attributes = [];
            let supports = this.$root.$data.objects[ this.postType ].supports;

            for (var key in supports) {
                attributes.push({
                    name: key,
                    label: supports[ key ],
                    value: key
                });
            }

            let attributeWeights = this.$parent.model.objects[ this.postType ].weights;

            // A native attribute is unused if it has a weight of zero
            return attributes.filter(function (attribute) {
                return attributeWeights[ attribute.name ] == 0;
            });
        },
        getUnusedTaxonomies() {
            let taxonomies = this.$root.$data.objects[ this.postType ].taxonomies;
            let taxonomyWeights = this.$parent.model.objects[ this.postType ].weights.tax;

            // A taxonomy is unused if it has a weight of zero *or if it is missing (meaning it was added since the last engine save)*
            return taxonomies.filter(function (taxonomy) {
                return !taxonomyWeights.hasOwnProperty(taxonomy.name) || taxonomyWeights[ taxonomy.name ] == 0;
            });
        },
        getUnusedRuleTaxonomies( ruleType ) {
            let taxonomies = this.$root.$data.objects[ this.postType ].taxonomies;
            let engineOptions = this.$parent.model.objects[ this.postType ].options;

            // A taxonomy is unused if there is no {ruleType} property for it
            return taxonomies.filter(function (taxonomy) {
                return !engineOptions.hasOwnProperty( ruleType + '_' + taxonomy.name);
            });
        },
        getUnusedMetakeys( unfiltered ) {
            let metakeys = [];
            let availableMetakeys = this.$root.$data.objects[ this.postType ].meta_keys;
            let metakeyWeights = this.$parent.model.objects[ this.postType ].weights.cf;

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
                            label: metakey === 'searchwpcfdefault' ? this.i18n.anyCustomField + ' ★' : metakey,
                            value: metakey
                        });
                    }
                }
            }

            return metakeys;
        },
        showDetails( details ) {
            // Hide the popover
            this.$refs['popoverRef' + this._uid].visible = false;

            // Show the multiselect
            this.showing = details;
        },
        addNativeContentType( attribute ) {
            this.visible = !this.visible;
            this.$parent.model.objects[ this.postType ].weights[ attribute.name ] = 1;

            // If there are no unused attributes left, there's no reason to continue showing anything
            if (this.getUnusedNativeAttributes().length == 0) {
                this.showing = '';
            }
        },
        addTaxonomy( taxonomy ) {
            this.visible = !this.visible;

            // There's a chance that this is the first taxonomy, so we need to make sure the model is set up
            if (!this.$parent.model.objects[ this.postType ].weights.tax || Array.isArray(this.$parent.model.objects[ this.postType ].weights.tax)) {
                Vue.set(this.$parent.model.objects[ this.postType ].weights, 'tax', {});
            }

            // There's a chance that the taxnomy doesn't exist in the model yet (e.g. when
            // a taxonomy has been added since the last engine save)
            if (!this.$parent.model.objects[ this.postType ].weights.tax.hasOwnProperty(taxonomy.name)) {
                Vue.set(this.$parent.model.objects[ this.postType ].weights.tax, taxonomy.name, 1);
            } else {
                this.$parent.model.objects[ this.postType ].weights.tax[ taxonomy.name ] = 1;
            }

            // If there are no unused taxonomies left, there's no reason to continue showing anything
            if (this.getUnusedTaxonomies().length == 0) {
                this.showing = '';
            }

            // This change influences the index
            this.indexInfluenced();
        },
        indexInfluenced() {
            // All engines are listening for this emit. When received each will report
            // its model to listeners, which in turn can further process for analysis.
            // This is just the trigger that kicks off the event overall, no data here.
            EventBus.$emit('indexInfluenced');
        },
        addExcludedTaxonomy( taxonomy ) {
            this.visible = !this.visible;
            Vue.set(this.$parent.model.objects[ this.postType ].options, 'exclude_' + taxonomy.name, []);

            if (this.getUnusedRuleTaxonomies('exclude').length == 0) {
                this.showing = '';
            }
        },
        addLimitedToTaxonomy( taxonomy ) {
            this.visible = !this.visible;
            Vue.set(this.$parent.model.objects[ this.postType ].options, 'limit_to_' + taxonomy.name, []);

            if (this.getUnusedRuleTaxonomies('limit_to').length == 0) {
                this.showing = '';
            }
        },
        addPostType( postType ) {
            this.$refs['popoverRef' + this._uid].visible = false;
            Vue.set(this.$parent.model.objects[ postType ], 'enabled', true);
        },
        addMetakeyRecord( metakey, passive ) {
            if (passive) {
                this.$refs['popoverRef' + this._uid].visible = false;
            }

            let modelKeyHash = 'swppv' + md5( this.postType + metakey);

            if (!this.$parent.model.objects[ this.postType ].weights.cf) {
                Vue.set(this.$parent.model.objects[ this.postType ].weights, 'cf', {});
            }

            // Due to limitations of JavaScript, Vue cannot observe object property changes, so this:
            Vue.set(this.$parent.model.objects[ this.postType ].weights.cf, modelKeyHash, {
                metakey: metakey,
                weight: 1
            });

            // This change influences the index
            this.indexInfluenced();
        },
        addMetakey( metakey, id ) {
            this.visible = !this.visible;
            this.addMetakeyRecord( metakey.name );

            // If there are no unused Custom Fields left, there's no reason to continue showing anything
            if (this.getUnusedMetakeys().length == 0) {
                this.showing = '';
            }
        }
    },
    data: function() {
        return {
            showing: '',
            i18n: {
                addExclusion: _SEARCHWP_VARS.i18n.add_exclusion,
                addLimiter: _SEARCHWP_VARS.i18n.add_limiter,
                anyCustomField: _SEARCHWP_VARS.i18n.any_custom_field,
                chooseNativeAttribute: _SEARCHWP_VARS.i18n.choose_native_attribute,
                chooseCustomField: _SEARCHWP_VARS.i18n.choose_custom_field,
                chooseTaxonomy: _SEARCHWP_VARS.i18n.choose_taxonomy,
                customField: _SEARCHWP_VARS.i18n.custom_field,
                documentContent: _SEARCHWP_VARS.i18n.document_content,
                documentProperties: _SEARCHWP_VARS.i18n.document_properties,
                done: _SEARCHWP_VARS.i18n.done,
                excludeByTaxonomy: _SEARCHWP_VARS.i18n.exclude_by_taxonomy,
                excluded: _SEARCHWP_VARS.i18n.excluded,
                limitByTaxonomy: _SEARCHWP_VARS.i18n.limit_by_taxonomy,
                pdfMetadata: _SEARCHWP_VARS.i18n.pdf_metadata,
                nativeAttribute: _SEARCHWP_VARS.i18n.native_attribute,
                search: _SEARCHWP_VARS.i18n.search,
                taxonomy: _SEARCHWP_VARS.i18n.taxonomy
            }
        }
    },
    props: {
        postType: {
            type: String,
            default: 'post',
            required: false
        },
        type: {
            type: String,
            default: 'contentTypes',
            required: true
        },
        buttonText: {
            type: String,
            required: true
        },
        position: {
            type: String,
            default: 'above',
            required: false
        }
    }
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style lang="scss">
    .searchwp-dropdown {
        border-top: 1px solid #ddd;
        margin-top: 1em;
        padding-top: 0.5em;
    }

    .searchwp-dropdown-choices {
        display: flex;
        align-items: center;
        padding-top: 0.6em; // Position the dropdown in the same spot as the button
    }

    .vue-popover {
        background: #414141;
        color: #fff;
        border-radius: 3px;
        margin-top: -6px;
        padding: 0;

        a {
            color: #fff;
        }

        ul {
            margin: 0;
            padding: 0.6em 0;
            list-style: none;
        }

        li {
            padding: 0.3em 0.7em;
            line-height: 1.5;
            margin: 0;
            cursor: pointer;

            &:hover {
                background: #159FD2;
            }

            &.searchwp-excluded-types-note {
                border-top: 1px solid rgba( 255, 255, 255, 0.25 );
                cursor: default;
                color: rgba( 255, 255, 255, 0.55 );
                margin-top: 0.5em;
                padding-top: 0.6em;

                &:hover {
                    background: transparent;
                }
            }
        }

        &.dropdown-position-top:before {
            border-top-color: #414141;
        }

        &.dropdown-position-bottom {
            margin-top: 6px;

            &:before {
                border-bottom-color: #414141;
            }
        }
    }

    .wp-core-ui {

        .searchwp-dropdown {

            div {

                .searchwp-button {
                    margin: 0 0 0 1em;
                }
            }
        }
    }

    // Multiselect component overrides
    .multiselect {
        color: #444;

        .multiselect__single {
            margin-bottom: 5px;
        }

        .multiselect__input {
            margin-bottom: 4px;
            border: 0;
            box-shadow: none;
        }
    }

    .multiselect,
    .multiselect__tags {
        min-height: 30px;
    }

    .multiselect__tags {
        padding: 4px 40px 0 2px;
        border-radius: 3px;
    }

    .multiselect__select {
        height: 32px;
        width: 30px;
        padding: 4px;
    }

    .searchwp-engine .multiselect__content {
        z-index: 9999 !important;
    }

    .multiselect__input,
    .multiselect__single {
        line-height: 22px;
        width: auto;
        background-color: transparent;
    }

    .multiselect__element {
        margin: 0;
    }

    .multiselect__option {
        padding: 8px;
        line-height: 1.4;
        min-height: 30px;
        font-size: 13px;
    }

    .multiselect__option--selected:after,
    .multiselect__option--highlight:after {
        display: none;
    }

    .multiselect__option--selected {
        background: #fff;
        font-weight: normal;
        color: #444;
    }

    .multiselect__option--highlight,
    .multiselect__option--highlight.multiselect__option--selected {
        background: #159FD2;
    }

    .multiselect__spinner {
        height: 28px;

        &:before,
        &:after {
            border-color: #159FD2 transparent transparent;
        }
    }

    .multiselect__tag {
        background: #159FD2;
        font-size: 13px;
        padding: 4px 26px 4px 7px;
        border-radius: 3px;
        margin-right: 5px;
        margin-bottom: 4px;
    }

    .multiselect__tag-icon {
        line-height: 19px;
        border-radius: 3px;

        &:after {
            color: darken( #159FD2, 20% );
            font-size: 15px;
        }
    }

    .multiselect__tag-icon:focus,
    .multiselect__tag-icon:hover {
        background: #159FD2;
    }
</style>
