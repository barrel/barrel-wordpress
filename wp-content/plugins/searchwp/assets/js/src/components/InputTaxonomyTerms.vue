<template>
    <div class="searchwp-input-taxonomy-terms">
        <label>{{ label }}</label>
        <div>
            <multiselect
                v-model="selectedTerms"
                label="label"
                track-by="value"
                :placeholder="i18n.findTerms"
                :options="terms"
                :multiple="true"
                :searchable="true"
                :loading="isLoading"
                :internal-search="false"
                :clear-on-select="true"
                :close-on-select="true"
                :max-height="300"
                :show-no-results="true"
                :hide-selected="true"
                @input="updateTerms()"
                @closed="closed = true"
                @search-change="asyncFind">
                <span slot="noResult">{{ i18n.noTermsFound }}</span>
            </multiselect>
        </div>
    </div>
</template>

<script>
import debounce from 'debounce';
import Vue from 'vue';

export default {
    name: 'SearchwpInputTaxonomyTerms',
    data: function() {
        return {
            selectedTerms: this.$parent.model.objects[ this.postType ].options[ this.mode + '_' + this.taxonomy],
            terms: [],
            isLoading: false,
            open: false,
            closed: true,
            i18n: {
                findTerms: _SEARCHWP_VARS.i18n.find_terms,
                noTermsFound: _SEARCHWP_VARS.i18n.no_terms_found
            }
        }
    },
    methods: {
        updateTerms() {
            Vue.set(this.$parent.model.objects[ this.postType ].options, this.mode + '_' + this.taxonomy, this.selectedTerms);
            this.$emit('termsUpdated', this.postType, this.taxonomy, this.selectedTerms.length)
        },
        asyncFind: debounce(function (query) {
            this.isLoading = true;
            if (''==query) {
                this.isLoading = false;
                return;
            }
            Vue.SearchwpSearchTaxonomyTerms(query, this.taxonomy, this.postType).then((response) => {
                this.terms = response;
                this.isLoading = false;
            }).catch(function (response) {
                this.isLoading = false;
            });
        }, 500),
        clearAll () {
            this.selectedTerms = [];
        }
    },
    created() {
        if (this.terms.length) {
            this.selectedTerms = this.terms;
        }
    },
    props: {
        label: {
            type: String,
            default: '',
            required: true
        },
        taxonomy: {
            type: String,
            required: true
        },
        postType: {
            type: String,
            required: true
        },
        mode: {
            type: String,
            default: 'exclude',
            required: true
        },
        value: {
            type: Object,
            default: function() { return {}; },
            required: false
        }
    }
}
</script>
