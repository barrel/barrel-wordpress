<template>
	<div>
		<ul class="searchwp-settings-advanced__actions">
			<li>
				<confirm
					:buttonLabel="i18n.resetIndex"
					:question="i18n.areYouSure"
					:confirm="i18n.yesResetIndex"
					v-on:confirmed="resetIndex"/>
			</li>
			<li><button class="button" @click="wakeIndex">{{ i18n.wakeUpIndexer }}</button></li>
			<li><button class="button" @click="restoreNotices">{{ i18n.restoreNotices }}</button></li>
		</ul>

		<div class="searchwp-settings-advanced__actions-notices">

			<searchwp-notice
				v-if="indexIsReset || indexIsResetting"
				:type="indexIsReset ? 'success' : 'warning'"
				:dismissable="indexIsReset"
				v-on:dismissed="indexIsReset = false">
				<spinner
					v-if="indexIsResetting"
					class="searchwp-spinner-message"
					:size="16"
					:line-size="2"
					:line-bg-color="'#ffb900'"
					:line-fg-color="'#fff8e7'"
					:text-fg-color="'#444444'"
					:message="i18n.indexBeingReset" />
				<span v-else-if="indexerAlternate">{{ i18n.indexResetAlternate }}</span>
				<span v-else>{{ i18n.indexResetRebuilding }}</span>
			</searchwp-notice>

			<searchwp-notice
				v-if="indexIsWoken || indexIsWaking"
				:type="indexIsWoken ? 'success' : 'warning'"
				:dismissable="indexIsWoken"
				v-on:dismissed="indexIsWoken = false">
				<spinner
					v-if="indexIsWaking"
					class="searchwp-spinner-message"
					:size="16"
					:line-size="2"
					:line-bg-color="'#ffb900'"
					:line-fg-color="'#fff8e7'"
					:text-fg-color="'#444444'"
					:message="i18n.indexerWaking" />
				<span v-else-if="indexerAlternate">{{ i18n.indexerWokenAlternate }}</span>
				<span v-else>{{ i18n.indexerWoken }}</span>
			</searchwp-notice>

			<searchwp-notice
				v-if="noticesAreReset"
				:type="'success'"
				:dismissable="true"
				v-on:dismissed="noticesAreReset = false">
				{{ i18n.noticesReset }}
			</searchwp-notice>
		</div>
	</div>
</template>

<script>
import Vue from 'vue';
import { EventBus } from './../EventBus.js';
import Spinner from 'vue-simple-spinner';
import Confirm from './Confirm.vue';
import SearchwpNotice from './Notice.vue';

export default {
	name: 'Actions',
	components: {
		Spinner,
		Confirm,
		'searchwp-notice': SearchwpNotice
	},
	methods: {
		resetIndex() {
			let self = this;

			self.indexIsReset = false;
			self.indexIsResetting = true;

			Vue.SearchwpResetIndex().then((response) => {
				self.indexIsResetting = false;
				self.indexIsReset = true;
			}).catch(function (response) {
				alert('ERROR SEARCHWPINDEXRESET')
			});

			EventBus.$emit('resetIndex', name);
		},
		wakeIndex() {
			const data = {
				action: 'searchwp_wake_indexer',
				_ajax_nonce: _SEARCHWP_VARS.nonces.wake_indexer
			};

			let self = this;
			self.indexIsWoken = false;
			self.indexIsWaking = true;

			jQuery.post(ajaxurl, data, function(response) {
				if (!response.success) {
					alert('There was an error. Please try again.');
				} else {
					self.indexIsWaking = false;
					self.indexIsWoken = true;
				}
			});
		},
		restoreNotices() {
			this.noticesAreReset = true;

			const data = {
				action: 'searchwp_reset_notices',
				_ajax_nonce: _SEARCHWP_VARS.nonces.reset_notices
			};

			jQuery.post(ajaxurl, data, function(response) {
				if (!response.success) {
					alert('There was an error. Please try again.');
				}
			});
		}
	},
	data: function() {
		return {
			indexIsReset: false,
			indexIsResetting: false,
			indexIsWoken: false,
			indexIsWaking: false,
			noticesAreReset: false,
			statsAreReset: false,
			indexerAlternate: _SEARCHWP_VARS.data.settings.indexer_alternate,
			i18n: {
				areYouSure: _SEARCHWP_VARS.i18n.are_you_sure,
				indexBeingReset: _SEARCHWP_VARS.i18n.index_being_reset,
				indexResetAlternate: _SEARCHWP_VARS.i18n.index_reset_alternate,
				indexResetRebuilding: _SEARCHWP_VARS.i18n.index_reset_rebuilding,
				indexerWaking: _SEARCHWP_VARS.i18n.indexer_waking,
				indexerWoken: _SEARCHWP_VARS.i18n.indexer_woken,
				indexerWokenAlternate: _SEARCHWP_VARS.i18n.indexer_woken_alternate,
				noticesReset: _SEARCHWP_VARS.i18n.notices_reset,
				resetIndex: _SEARCHWP_VARS.i18n.reset_index,
				restoreNotices: _SEARCHWP_VARS.i18n.restore_notices,
				wakeUpIndexer: _SEARCHWP_VARS.i18n.wake_up_indexer,
				yesResetIndex: _SEARCHWP_VARS.i18n.yes_reset_index
			}
		}
	}
}
</script>

<style lang="scss">
	.searchwp-settings-advanced__actions {
		list-style: none;
		margin: 0;
		padding: 0.5em 0 0;
		display: flex;
		flex-wrap: wrap;

		> li {
			margin-right: 1em;
		}
	}

	.wp-core-ui .searchwp-settings-advanced__actions .button.searchwp-button {
		margin: 0;
	}
</style>
