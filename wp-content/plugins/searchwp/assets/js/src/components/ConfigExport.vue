<template>

	<div class="searchwp-settings-advanced__export">
		<div class="searchwp-settings-advanced__export_engines">
			<h6>{{ i18n.engines }}</h6>
			<ul>
				<li v-for="(engine, engineIndex) in engines" :key="'engine' + engineIndex">
					<searchwp-input-checkbox
						:label="engine.label"
						:checked="engine.export"
						v-on:change="toggleEngineExport(engineIndex)"/>
				</li>
			</ul>
		</div>
		<div class="searchwp-settings-advanced__export_json">
			<h6>{{ i18n.exportData }}</h6>
			<textarea
				@focus="$event.target.select()"
				@click="$event.target.select()"
				v-model="engineExport"
				:placeholder="i18n.noEnginesInExport"></textarea>
		</div>
	</div>

</template>

<script>
import { EventBus } from './../EventBus.js';
import SearchwpInputCheckbox from './InputCheckbox.vue';

export default {
	name: 'ConfigExport',
	components: {
		'searchwp-input-checkbox': SearchwpInputCheckbox
	},
	computed: {
		engineExport() {
			let toExport = {};
			let parsedFullConfig = JSON.parse(this.fullExport)

			for (const engine of this.engines) {
				if (!engine.export) {
					continue;
				}

				toExport[engine.name] = parsedFullConfig[engine.name];
			}

			return JSON.stringify(toExport);
		}
	},
	methods: {
		toggleEngineExport(engineIndex) {
			this.engines[engineIndex].export = !this.engines[engineIndex].export;
		},
		prep(engineConfigJSON) {
			const engineConfig = JSON.parse(engineConfigJSON);
			let engines = [];

			for (let engine in engineConfig) {
				if (engineConfig.hasOwnProperty(engine)) {
					engines.push({
						name: engine,
						label: 'default' === engine ? 'Default' : engineConfig[engine].searchwp_engine_label,
						export: true
					});
				}
			}

			this.engines = engines;
		}
	},
	created: function() {
		this.prep(_SEARCHWP_VARS.data.engines_config);

		// When an import is run, update.
		EventBus.$on('configImported', this.prep);
	},
	data: function() {
		return {
			engines: [],
			fullExport: _SEARCHWP_VARS.data.engines_config,
			i18n: {
				engines: _SEARCHWP_VARS.i18n.engines,
				exportData: _SEARCHWP_VARS.i18n.export_data,
				noEnginesInExport: _SEARCHWP_VARS.i18n.no_engines_in_export
			}
		}
	}
}
</script>

<style lang="scss">
	.searchwp-settings-advanced__export {
		display: flex;
		flex-direction: column;

		h6 {
			margin: 0;
			font-size: 1em;
		}
	}

	.searchwp-settings-advanced__export_engines {

		ul {
			margin: 0;
			padding: 0;
			list-style: none;
			display: flex;

			> * {
				margin-right: 1.5em;
			}
		}
	}

	.searchwp-settings-advanced__export_json {
		display: flex;
		flex-direction: column;
		flex: 1;
		padding-top: 15px;

		textarea {
			margin-top: 10px;
			resize: none;
			display: block;
			flex: 1;
			min-height: 11em;
			resize: none;
			font-family: monospace;
			border: 1px solid #dcdcdc;
			padding: 0.5em;
			box-shadow: none;
			font-size: 1em;
		}
	}
</style>
