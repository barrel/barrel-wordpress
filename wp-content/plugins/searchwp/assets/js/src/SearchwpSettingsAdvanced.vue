<template>

	<div class="searchwp-settings-advanced">

		<div class="searchwp-settings-advanced__primary_alpha">

			<div class="postbox metabox-holder searchwp-settings-advanced__stopwords-synonyms">

				<h3 class="hndle"><span>{{ i18n.searchTermHandling }}</span></h3>

				<div class="inside searchwp-settings-tab-actions">
					<ul>
						<li v-bind:class="[ 'searchwp-settings-tab', 'stopwords' === termHandlingState ? 'searchwp-settings-tab-is-active' : '' ]">
							<a href="#stopwords" @click.prevent="termHandlingState='stopwords'">{{ i18n.stopwords }}</a>
						</li>
						<li v-bind:class="[ 'searchwp-settings-tab', 'synonyms' === termHandlingState ? 'searchwp-settings-tab-is-active' : '' ]">
							<a href="#synonyms" @click.prevent="termHandlingState='synonyms'">{{ i18n.synonyms }}</a>
						</li>
					</ul>
				</div>

				<div v-bind:class="[ 'searchwp-settings-tab-container', 'searchwp-settings-is-tabbed-' + termHandlingState ]">
					<div class="searchwp-settings-advanced-stopwords" id="stopwords">
						<div class="inside">
							<component :is="translatedStopwordsNote"></component>
							<searchwp-stopwords/>
						</div>
					</div>
					<div class="searchwp-settings-advanced-synonyms" id="synonyms">
						<div class="inside">
							<component :is="translatedSynonymsNote"></component>
							<searchwp-synonyms/>
						</div>
					</div>
				</div>

			</div>

		</div>

		<div class="searchwp-settings-advanced__primary_beta">

			<div class="postbox metabox-holder searchwp-settings-advanced__actions-settings">

				<h3 class="hndle"><span>{{ i18n.actionsSettings }}</span></h3>
				<div class="inside">
					<searchwp-actions/>
					<searchwp-settings/>
				</div>

			</div>
			<div class="postbox metabox-holder searchwp-settings-advanced__transfer">

				<h3 class="hndle"><span>{{ i18n.engineConfigurationTransfer }}</span></h3>

				<div class="inside searchwp-settings-tab-actions">
					<ul>
						<li v-bind:class="[ 'searchwp-settings-tab', 'import' === exportImportState ? 'searchwp-settings-tab-is-active' : '' ]">
							<a href="#import" @click.prevent="exportImportState='import'">{{ i18n.import }}</a>
						</li>
						<li v-bind:class="[ 'searchwp-settings-tab', 'export' === exportImportState ? 'searchwp-settings-tab-is-active' : '' ]">
							<a href="#export" @click.prevent="exportImportState='export'">{{ i18n.export }}</a>
						</li>
					</ul>
				</div>

				<div v-bind:class="[ 'searchwp-settings-tab-container', 'searchwp-settings-is-tabbed-' + exportImportState ]">
					<div class="searchwp-settings-config-import" id="import">
						<div class="inside">
							<searchwp-config-import/>
						</div>
					</div>
					<div class="searchwp-settings-config-export" id="export">
						<div class="inside">
							<searchwp-config-export/>
						</div>
					</div>
				</div>

			</div>

		</div>

		<portal-target name="modaltor"></portal-target>

	</div>

</template>

<script>
import Vue from 'vue';
import SearchwpStopwords from './components/Stopwords.vue';
import SearchwpSynonyms from './components/Synonyms.vue';
import SearchwpActions from './components/Actions.vue';
import SearchwpSettings from './components/Settings.vue';
import SearchwpConfigExport from './components/ConfigExport.vue';
import SearchwpConfigImport from './components/ConfigImport.vue';

export default {
	name: 'SearchwpSettingsAdvanced',
	components: {
		'searchwp-actions': SearchwpActions,
		'searchwp-settings': SearchwpSettings,
		'searchwp-config-export': SearchwpConfigExport,
		'searchwp-config-import': SearchwpConfigImport,
		'searchwp-stopwords' : SearchwpStopwords,
		'searchwp-synonyms' : SearchwpSynonyms
	},
	computed: {
		translatedStopwordsNote() {
			return {
				template: '<p>' + this.i18n.stopwordsNote + '</p>'
			}
		},
		translatedSynonymsNote() {
			return {
				template: '<p>' + this.i18n.synonymsNote + '</p>'
			}
		}
	},
	data: function() {
		return {
			exportImportState: 'import',
			termHandlingState: 'stopwords',
			i18n: {
				actionsSettings: _SEARCHWP_VARS.i18n.actions_settings,
				engineConfigurationTransfer: _SEARCHWP_VARS.i18n.engine_configuration_transfer,
				export: _SEARCHWP_VARS.i18n.export,
				import: _SEARCHWP_VARS.i18n.import,
				searchTermHandling: _SEARCHWP_VARS.i18n.search_term_handling,
				stopwords: _SEARCHWP_VARS.i18n.stopwords,
				stopwordsNote: _SEARCHWP_VARS.i18n.stopwords_note,
				synonyms: _SEARCHWP_VARS.i18n.synonyms,
				synonymsNote: _SEARCHWP_VARS.i18n.synonyms_note
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
	.swp-notices.swp-group {
		display: none !important;
	}

	#wpbody-content .searchwp-settings-advanced {

		* {
			box-sizing: border-box;
		}

		.metabox-holder {
			padding-top: 0;
		}

		.postbox {

			.hndle {
				cursor: default;
			}

			.inside {
				padding-bottom: 0;
			}
		}
	}

	.searchwp-settings-advanced {
		display: flex;
		flex-wrap: wrap;
		justify-content: space-between;
		padding-top: 10px;

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

		.vue-portal-target  {
			width: 100% !important;
		}
	}

	.searchwp-settings-advanced__primary_alpha,
	.searchwp-settings-advanced__primary_beta {
		width: calc(50% - 10px);
		display: flex;
		flex-direction: column;

		> * {
			display: flex;
			flex-direction: column;
		}
	}



	.searchwp-settings-advanced__stopwords-synonyms,
	.searchwp-settings-advanced__transfer {
		flex: 1;
		display: flex;
		flex-direction: column;

		&.postbox .inside {

			&.searchwp-settings-tab-actions {
				margin-top: 6px;
				margin-bottom: 0;
				border-bottom: 1px solid #eee;
			}
		}
	}

	.searchwp-settings-advanced__transfer {
		display: flex;
		flex: 1;
	}

	.searchwp-settings-is-tabbed-import {
		flex: 1;
	}

	.searchwp-settings-config-export,
	.searchwp-settings-config-import {
		flex: 1;
		display: flex;
		flex-direction: column;

		.inside {
			flex: 1;
			display: flex;
			flex-direction: column;
		}
	}

	.searchwp-settings-config-export {
		height: 100%;
	}

	.searchwp-settings-config-import {
		bottom: 0;
	}

	.searchwp-settings-advanced__export {
		flex: 1;
	}

	.searchwp-settings-advanced__export_json {
		flex: 1;
		display: flex;
		flex-direction: column;

		textarea {
			flex: 1;
		}
	}

	.searchwp-settings-tab-actions {

		ul {
			display: flex;
			align-items: center;
			flex-wrap: wrap;
			list-style: none;
			margin: 0;
			padding: 0;
		}

		li {
			margin: 0;
			padding: 0;
		}
	}

	.searchwp-settings-tab a {
		border-bottom: 2px solid transparent;
		display: block;
		padding: 0.5em 0 10px;
		margin: 0 1em 0 0;
		line-height: 1;
		text-decoration: none;
		color: #00a0d2;

		&:focus {
			box-shadow: none;
		}
	}

	.searchwp-settings-tab-is-active a {
		border-bottom: 2px solid #00a0d2;
		font-weight: bold;
	}

	.searchwp-settings-tab-container {
		position: relative;
		overflow: hidden;
		flex: 1;

		> * {
			transition: all 300ms ease-in-out;
			top: 0;
			left: 0;
			width: 100%;
		}

		> *:first-child {
			transform: translateX(0);
			height: 100%;
		}

		> *:last-child {
			transform: translateX(100%);
			position: absolute;
		}

		&.searchwp-settings-is-tabbed-synonyms,
		&.searchwp-settings-is-tabbed-export {

			> *:first-child {
				transform: translateX(-100%);
			}

			> *:last-child {
				transform: translateX(0);
			}
		}
	}

	// One tab needs to define the height, and the other tabs 'fit' into it. Import/Export is one way
	// and Stopwords/Synonyms is the opposite, so this corrects that.
	.searchwp-settings-advanced__transfer {

		.searchwp-settings-tab-container {

			> *:first-child {
				position: absolute;
				height: auto;
			}

			> *:last-child {
				position: static;
				height: 100%;
			}
		}
	}

	.searchwp-settings-advanced-stopwords {
		display: flex;

		.inside {
			display: flex;
			flex-direction: column;
			flex: 1;

			> p {
				margin-top: 0;
			}
		}

		.searchwp-stopwords {
			display: flex;
			flex: 1;
			flex-direction: column;
		}

		.vue-input-tag-wrapper {
			flex: 1;
			align-content: flex-start;
		}
	}

	ul.searchwp-actions {
		list-style: none;
		margin: 0;
		padding: 0.7em 0 0;
		display: flex;
		flex-wrap: wrap;

		> li {
			margin: 0 1em 0 0;
		}
	}

	.searchwp-desc-action {
		display: flex;
		justify-content: space-between;
	}

	.searchwp-settings-advanced-synonyms {
		display: flex;
		flex-direction: column;
		flex: 1;
		bottom: 0;

		.inside {
			flex: 1;
			display: flex;
			flex-direction: column;
			position: absolute;
			top: 0;
			right: 0;
			bottom: 0;
			left: 0;
			margin: 0; // we're a little complex here because we only want the synonyms table to scroll...
			padding: 0;

			// because of the margin/padding reset we need to shim everything inside
			> p {
				padding-right: 12px;
				padding-left: 12px;
			}

			.searchwp-synonyms__list {
				padding-left: 12px;
				padding-right: 12px;
			}

			.searchwp-actions {
				padding-right: 12px;
				padding-left: 12px;
				margin-top: 11px;
				margin-bottom: 11px;
			}

			.searchwp-notice-persist {
				margin: 11px 12px;
			}
		}

		.searchwp-synonyms {
			overflow: auto;
		}
	}

	.searchwp-settings-config-export {
		min-height: 20em;
	}

	@media screen and (max-width:1280px) {
		.searchwp-settings-advanced__primary_alpha,
		.searchwp-settings-advanced__primary_beta {
			width: 100%;
		}
	}
</style>
