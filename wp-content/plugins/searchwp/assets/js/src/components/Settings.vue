<template>
	<div>
		<searchwp-notice
			v-if="indexDirty"
			:type="'warning'"
			:dismissable="true"
			v-on:dismissed="resetDirtyFlags()">
			<span>{{ i18n.indexNeedsReset }}</span>
		</searchwp-notice>
		<searchwp-notice
			v-if="newPartialMatches"
			:type="'warning'"
			:dismissable="true"
			v-on:dismissed="dismissed_partial_match_note = true">
			<component :is="translatedPartialMatchesNote"></component>
		</searchwp-notice>
		<ul :class="[ 'searchwp-settings-advanced__settings', saving ? 'searchwp-settings--is-saving' : '' ]">
			<li>
				<setting
					:name="'debugging'"
					:label="i18n.debuggingEnabled"
					:value="debugging"
					:disabled="saving"
					v-on:change="updateSetting($event)"/>
			</li>
			<li><setting
					:name="'indexer_alternate'"
					:label="i18n.useAlternateIndexer"
					:value="indexer_alternate"
					:disabled="saving"
					v-on:change="updateSetting($event)"/>
			</li>
			<li><setting
					:name="'parse_shortcodes'"
					:label="i18n.parseShortcodes"
					:value="parse_shortcodes"
					:disabled="saving"
					v-on:change="updateSetting($event);flagIndex()"/>
			</li>
			<li><setting
					:name="'partial_matches'"
					:label="i18n.partialMatches"
					:value="partial_matches"
					:disabled="saving"
					v-on:change="updateSetting($event)"/>
			</li>
			<li><setting
					:name="'indexer_aggressiveness'"
					:label="i18n.reducedIndexerAggressiveness"
					:value="indexer_aggressiveness"
					:disabled="saving"
					v-on:change="updateSetting($event)"/>
			</li>
			<li><setting
					:name="'min_word_length'"
					:label="i18n.removeMinWordLength"
					:value="min_word_length"
					:disabled="saving"
					v-on:change="updateSetting($event);flagIndex()"/>
			</li>
			<li class="searchwp-admin-search"><setting
					:name="'admin_search'"
					:label="i18n.adminEngine + ': '"
					:value="admin_search"
					:disabled="saving"
					v-on:change="updateSetting($event)"/>
				<v-popover offset="6" placement="bottom">
					<span class="searchwp-admin-search-label"><span class="dashicons dashicons-arrow-down"></span>{{ adminEngineLabel }}</span>
					<template slot="popover">
						<ul class="searchwp-popover-actions">
							<li v-for="engine in engines">
								<button v-close-popover @click="setAdminEngine(engine)">{{ engine.label }}</button>
							</li>
						</ul>
					</template>
				</v-popover>
			</li>
			<li><setting
					:name="'highlight_terms'"
					:label="i18n.highlightResults"
					:value="highlight_terms"
					:disabled="saving"
					v-on:change="updateSetting($event)"/>
			</li>
			<li><setting
					:name="'exclusive_regex_matches'"
					:label="i18n.exclusiveRegexMatches"
					:value="exclusive_regex_matches"
					:disabled="saving"
					v-on:change="updateSetting($event);flagIndex()"/>
			</li>
			<li><setting
					:name="'nuke_on_delete'"
					:label="i18n.removeAllData"
					:value="nuke_on_delete"
					:disabled="saving"
					v-on:change="updateSetting($event)"/>
			</li>
		</ul>
	</div>
</template>

<script>
import Vue from 'vue';
import { EventBus } from './../EventBus.js';
import Setting from './Setting.vue';
import SearchwpInputCheckbox from './InputCheckbox.vue';
import SearchwpNotice from './Notice.vue';

export default {
	name: 'Settings',
	components: {
		Setting,
		'searchwp-input-checkbox': SearchwpInputCheckbox,
		'searchwp-notice': SearchwpNotice
	},
	methods: {
		flagIndex() {
			Vue.SearchwpSetSetting('index_dirty', JSON.stringify(true)).then((response) => {});
		},
		updateSetting(setting) {
			let self = this;
			const name = setting.name;
			let value = setting.value;

			if (self.saving) {
				return;
			}

			self.saving = true;

			// value is referenced and we don't want to manipulate it directly
			let payload = value;

			// Searching in admin has a compound value: enabled & engine name
			if ('admin_search'==name) {
				payload = JSON.stringify({
					enabled: value,
					engine: this.adminEngine ? this.adminEngine : 'default'
				});
			}

			// We must have latency here for UI feedback.
			setTimeout(function(){
				const data = {
					action: 'searchwp_update_setting',
					setting: name,
					value: payload,
					_ajax_nonce: _SEARCHWP_VARS.nonces.update_setting
				};

				jQuery.post(ajaxurl, data, function(response) {
					self.saving = false;
					if (!response.success) {
						alert('There was an error. Please try again.');
					} else {
						self[name] = value;
						EventBus.$emit('saved', name);
					}
				});
			}, 300);
		},
		clearDirtyIndex() {
			// Mark the index as clean.
			Vue.SearchwpSetSetting('index_dirty', JSON.stringify(false)).then((response) => {});

			this.resetDirtyFlags();
		},
		resetDirtyFlags() {
			this.original_parse_shortcodes = this.parse_shortcodes;
			this.original_exclusive_regex_matches = this.exclusive_regex_matches;
			this.original_min_word_length = this.min_word_length;
		},
		setAdminEngine(engine) {
			this.adminEngine = engine.name;
			this.adminEngineLabel = engine.label;

			this.updateSetting({
				name: 'admin_search',
				value: this.admin_search
			});
		}
	},
	computed: {
		engines() {
			const engineConfig = JSON.parse(this.engineConfig);
			let engines = [];

			for (let engine in engineConfig) {
				if (engineConfig.hasOwnProperty(engine)) {
					engines.push({
						name: engine,
						label: 'default' === engine ? 'Default' : engineConfig[engine].searchwp_engine_label
					});
				}
			}

			return engines;
		},
		indexDirty() {
			return (this.parse_shortcodes !== this.original_parse_shortcodes)
					|| (this.exclusive_regex_matches !== this.original_exclusive_regex_matches)
					|| (this.min_word_length !== this.original_min_word_length);
		},
		newPartialMatches() {
			// This is only a concern if partial matches is NEWLY enabled. We don't want to show it all the time.
			return !this.dismissed_partial_match_note && this.partial_matches && (this.partial_matches !== this.original_partial_matches);
		},
		translatedPartialMatchesNote() {
			return {
				template: '<span>' + this.i18n.partialMatchesNote + '</span>'
			}
		}
	},
	created() {
		EventBus.$on('resetIndex', this.clearDirtyIndex);

		// We only store the admin engine name, so let's retrieve the label.
		for(let engine of this.engines) {
			if(engine.name===this.adminEngine) {
				this.adminEngineLabel = engine.label;
				break;
			}
		}
	},
	data: function() {
		return {
			adminEngine: _SEARCHWP_VARS.data.settings.admin_engine,
			adminEngineLabel: _SEARCHWP_VARS.i18n.default,
			engineConfig: _SEARCHWP_VARS.data.engines_config,
			dismissed_partial_match_note: false,
			saving: false,
			indexer_alternate: _SEARCHWP_VARS.data.settings.indexer_alternate,
			debugging: _SEARCHWP_VARS.data.settings.debugging,
			highlight_terms: _SEARCHWP_VARS.data.settings.highlight_terms,
			min_word_length: _SEARCHWP_VARS.data.settings.min_word_length,
			exclusive_regex_matches: _SEARCHWP_VARS.data.settings.exclusive_regex_matches,
			admin_search: _SEARCHWP_VARS.data.settings.admin_search,
			nuke_on_delete: _SEARCHWP_VARS.data.settings.nuke_on_delete,
			indexer_aggressiveness: _SEARCHWP_VARS.data.settings.indexer_aggressiveness,
			parse_shortcodes: _SEARCHWP_VARS.data.settings.parse_shortcodes,
			partial_matches: _SEARCHWP_VARS.data.settings.partial_matches,
			original_indexer_alternate: _SEARCHWP_VARS.data.settings.indexer_alternate,
			original_debugging: _SEARCHWP_VARS.data.settings.debugging,
			original_highlight_terms: _SEARCHWP_VARS.data.settings.highlight_terms,
			original_min_word_length: _SEARCHWP_VARS.data.settings.min_word_length,
			original_exclusive_regex_matches: _SEARCHWP_VARS.data.settings.exclusive_regex_matches,
			original_admin_search: _SEARCHWP_VARS.data.settings.admin_search,
			original_nuke_on_delete: _SEARCHWP_VARS.data.settings.nuke_on_delete,
			original_indexer_aggressiveness: _SEARCHWP_VARS.data.settings.indexer_aggressiveness,
			original_parse_shortcodes: _SEARCHWP_VARS.data.settings.parse_shortcodes,
			original_partial_matches: _SEARCHWP_VARS.data.settings.partial_matches,
			i18n: {
				debuggingEnabled: _SEARCHWP_VARS.i18n.debugging_enabled,
				exclusiveRegexMatches: _SEARCHWP_VARS.i18n.exclusive_regex_matches,
				indexNeedsReset: _SEARCHWP_VARS.i18n.index_needs_reset,
				adminEngine: _SEARCHWP_VARS.i18n.admin_engine,
				highlightResults: _SEARCHWP_VARS.i18n.highlight_results,
				parseShortcodes: _SEARCHWP_VARS.i18n.parse_shortcodes,
				partialMatches: _SEARCHWP_VARS.i18n.partial_matches,
				partialMatchesNote: _SEARCHWP_VARS.i18n.partial_matches_note,
				reducedIndexerAggressiveness: _SEARCHWP_VARS.i18n.reduced_indexer_aggressiveness,
				removeAllData: _SEARCHWP_VARS.i18n.remove_all_data,
				removeMinWordLength: _SEARCHWP_VARS.i18n.remove_min_word_length,
				useAlternateIndexer: _SEARCHWP_VARS.i18n.use_alternate_indexer
			}
		}
	}
}
</script>

<style lang="scss">
	.searchwp-settings-advanced__settings {
		list-style: none;
		margin: 0;
		padding: 0.5em 0 0;
		display: flex;
		flex-wrap: wrap;
		justify-content: space-between;

		> li {
			width: calc(50% - 10px);
		}

		.searchwp-spinner-message {
			padding-top: 0.5em; // Match dimensions of checkbox.

			.vue-simple-spinner-text {
				padding-left: 0.65em;
			}
		}
	}

	.searchwp-settings--is-saving li {
		cursor: not-allowed !important;
		opacity: 0.8;

		label {
			cursor: not-allowed !important;
		}
	}

	.searchwp-admin-search {
		display: flex;
		align-items: center;
		overflow: hidden;

		* {
			white-space: nowrap;
		}

		.v-popover {
			padding-top: 0.5em;
			max-width: 100%;
			display: flex;
			align-items: center;
			cursor: pointer;

			.trigger {
				display: block;
				max-width: 100%;

				> span {
					max-width: 100%;
					white-space: nowrap;
					overflow: hidden;
					text-overflow: ellipsis;
					display: flex;
					align-items: center;
				}
			}
		}

		.searchwp-admin-search-label {

			.dashicons {
				font-size: 18px;
				height: 18px;
				width: 18px;
			}
		}
	}

	@media screen and (max-width:550px) {
		.searchwp-settings-advanced__settings > li {
			width: 100%;
		}
	}
</style>
