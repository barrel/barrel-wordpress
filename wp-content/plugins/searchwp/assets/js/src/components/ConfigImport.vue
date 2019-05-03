<template>

	<div class="searchwp-settings-advanced__import">
		<h6>{{ i18n.importData }}</h6>
		<textarea v-model="engineImport" :placeholder="i18n.importHowTo"></textarea>
		<p v-if="!imported && !importing" class="searchwp-settings-advanced__import-action">
			<confirm
				:buttonLabel="i18n.import"
				:question="i18n.canNotBeUndone"
				:confirm="i18n.importConfig"
				:placement="'top'"
				v-on:confirmed="importConfig"/>
		</p>
		<searchwp-notice
			v-if="imported || importing"
			:type="imported ? 'success' : 'warning'"
			:dismissable="imported"
			v-on:dismissed="imported = false">
			<spinner
				v-if="importing"
				class="searchwp-spinner-message"
				:size="16"
				:line-size="2"
				:line-bg-color="'#ffb900'"
				:line-fg-color="'#fff8e7'"
				:text-fg-color="'#444444'"
				:message="i18n.importingEngineConfig" />
			<span v-else>{{ i18n.engineConfigImported }}</span>
		</searchwp-notice>
	</div>

</template>

<script>
import { EventBus } from './../EventBus.js';
import Spinner from 'vue-simple-spinner';
import Confirm from './Confirm.vue';
import SearchwpNotice from './Notice.vue';

export default {
	name: 'ConfigImport',
	components: {
		Spinner,
		Confirm,
		'searchwp-notice': SearchwpNotice
	},
	methods: {
		importConfig() {
			const data = {
				action: 'searchwp_config_import',
				import: this.engineImport,
				_ajax_nonce: _SEARCHWP_VARS.nonces.config_import
			};

			let self = this;
			self.imported = false;
			self.importing = true;

			setTimeout(function(){
				jQuery.post(ajaxurl, data, function(response) {
					if (!response.success) {
						alert('There was an error. Please try again.');
					} else {
						self.importing = false;
						self.imported = true;
						EventBus.$emit('configImported', self.engineImport);
					}
				});
			}, 250);
		}
	},
	data: function() {
		return {
			imported: false,
			importing: false,
			engineImport: '',
			i18n: {
				canNotBeUndone: _SEARCHWP_VARS.i18n.can_not_be_undone,
				engineConfigImported: _SEARCHWP_VARS.i18n.engine_config_imported,
				import: _SEARCHWP_VARS.i18n.import,
				importConfig: _SEARCHWP_VARS.i18n.import_config,
				importData: _SEARCHWP_VARS.i18n.import_data,
				importHowTo: _SEARCHWP_VARS.i18n.import_how_to,
				importingEngineConfig: _SEARCHWP_VARS.i18n.importing_engine_config
			}
		}
	}
}
</script>

<style lang="scss">
	.searchwp-settings-advanced__import {
		display: flex;
		flex-direction: column;
		flex: 1;

		h6 {
			margin: 0;
			font-size: 1em;
		}

		textarea {
			margin-top: 10px;
			flex: 1;
			display: block;
			resize: none;
			font-family: monospace;
			border: 1px solid #dcdcdc;
			padding: 0.5em;
			box-shadow: none;
			font-size: 1em;
		}

		.searchwp-notice-persist {
			margin-bottom: 5px;
		}

		.searchwp-settings-advanced__import-action {
			margin-bottom: 0;
			text-align: right;
		}
	}
</style>
