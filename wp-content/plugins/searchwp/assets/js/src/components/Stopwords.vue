<template>
	<div class="searchwp-stopwords">
		<input-tag
			v-model="stopwords"
			v-on:input="normalize"></input-tag>
		<ul v-if="!saved && !saving" class="searchwp-actions">
			<li>
				<button class="button button-primary" @click="save">{{ i18n.saveStopwords }}</button>
			</li>
			<li v-if="suggestions && suggestions.length">
				<button class="button" @click="showingSuggestions = true">{{ i18n.suggestions }}</button>
				<portal to="modaltor">
					<vue-modaltor
						:visible="showingSuggestions"
						@hide="showingSuggestions = false"
						:default-width="'600px'">
						<div class="searchwp-stopwords-suggestions__modal">
							<h4>{{ i18n.suggestedStopwords }}</h4>
							<p>{{ i18n.suggestedStopwordsNote }}</p>
							<table>
								<thead>
									<th>{{ i18n.term }}</th>
									<th>{{ i18n.indexPrevalence }}</th>
									<th>{{ i18n.addStopword }}</th>
								</thead>
								<tbody>
									<tr v-for="(suggestion, suggestionIndex) in suggestions"
										:key="'suggestion' + suggestionIndex">
										<td>{{ suggestion.term }}</td>
										<td>{{ parseFloat(suggestion.prevalence) }}%</td>
										<td><button class="button" @click="addStopword(suggestion.term)">{{ i18n.addToStopwords }}</button></td>
									</tr>
								</tbody>
							</table>
						</div>
					</vue-modaltor>
				</portal>
			</li>
			<li>
				<v-popover offset="6" placement="top">
					<searchwp-button :icon="'dashicons-admin-tools'" :label="'Actions'" />
					<template slot="popover">
						<ul class="searchwp-popover-actions">
							<li>
								<button v-close-popover @click="sort">{{ i18n.sortAlphabetically }}</button>
							</li>
							<li>
								<button v-close-popover @click="removeAll">{{ i18n.removeAll }}</button>
							</li>
							<li>
								<button v-close-popover @click="restoreDefaults">{{ i18n.restoreDefaults }}</button>
							</li>
						</ul>
					</template>
				</v-popover>
			</li>
		</ul>
		<searchwp-notice
			v-if="saved || saving"
			:type="saved ? 'success' : 'warning'"
			:dismissable="saved"
			v-on:dismissed="saved = false">
			<spinner
				v-if="saving"
				class="searchwp-spinner-message"
				:size="16"
				:line-size="2"
				:line-bg-color="'#ffb900'"
				:line-fg-color="'#fff8e7'"
				:text-fg-color="'#444444'"
				:message="i18n.saving" />
			<span v-else>{{ i18n.stopwordsSaved }}</span>
		</searchwp-notice>
	</div>
</template>

<script>
import Vue from 'vue';
import InputTag from './InputTag.vue';
import Spinner from 'vue-simple-spinner';
import SearchwpButton from './Button.vue';
import SearchwpNotice from './Notice.vue';

export default {
	name: 'Stopwords',
	components: {
		Spinner,
		InputTag,
		'searchwp-button': SearchwpButton,
		'searchwp-notice': SearchwpNotice
	},
	methods: {
		normalize() {
			// Split on commas, remove duplicates, lowercase.
			let stopwords = this.stopwords
				.reduce(
					(acc, stopword) => acc.concat(stopword.split(',').map(
						stopword => stopword.trim().toLowerCase()
					)
				), [])
				.filter(
					(stopword, idx, array) => array.indexOf(stopword) === idx
				);

			this.stopwords = stopwords;
		},
		save() {
			const data = {
				action: 'searchwp_update_stopwords',
				stopwords: JSON.stringify(this.stopwords),
				_ajax_nonce: _SEARCHWP_VARS.nonces.update_stopwords
			};

			let self = this;
			this.saved = false;
			this.saving = true;

			jQuery.post(ajaxurl, data, function(response) {
				self.saving = false;
				if (!response.success) {
					alert('There was an error. Please try again.');
				} else {
					self.saved = true;
				}
			});
		},
		addStopword(stopword) {
			this.stopwords.push(stopword);

			if (!this.suggestions.length) {
				this.showingSuggestions = false;
			}
		},
		removeAll() {
			this.stopwords = [];
		},
		restoreDefaults() {
			this.stopwords = this.stopwordsDefault;
		},
		sort() {
			let sorted = this.stopwords.sort(function(a, b) {
				if (a.toLowerCase() > b.toLowerCase()) {
					return 1;
				}

				if (b.toLowerCase() > a.toLowerCase()) {
					return -1;
				}

				return 0;
			});

			this.stopwords = sorted;
		}
	},
	computed: {
		suggestions() {
			let self = this;
			let diff = [];
			const suggestions = Array.from(self.stopwordsSuggestionsSource);

			suggestions.forEach(function(suggestion) {
				if (self.stopwords.indexOf(suggestion.term) < 0) {
					diff.push(suggestion);
				}
			});

			return diff;
		}
	},
	created() {
		if (_SEARCHWP_VARS.data.do_stopwords_suggestions) {
			const data = {
				action: 'searchwp_stopwords_suggestions',
				_ajax_nonce: _SEARCHWP_VARS.nonces.stopwords_suggestions
			};

			let self = this;

			jQuery.post(ajaxurl, data, function(response) {
				if (response.success) {
					self.stopwordsSuggestionsSource = response.data;
				}
			});
		}
	},
	data: function() {
		return {
			saving: false,
			saved: false,
			showingSuggestions: false,
			stopwords: _SEARCHWP_VARS.data.stopwords,
			stopwordsDefault: _SEARCHWP_VARS.data.stopwords_default,
			stopwordsSuggestionsSource: _SEARCHWP_VARS.data.stopwords_suggestions,
			i18n: {
				addStopword: _SEARCHWP_VARS.i18n.add_stopword,
				addToStopwords: _SEARCHWP_VARS.i18n.add_to_stopwords,
				indexPrevalence: _SEARCHWP_VARS.i18n.index_prevalence,
				removeAll: _SEARCHWP_VARS.i18n.remove_all,
				restoreDefaults: _SEARCHWP_VARS.i18n.restore_defaults,
				saveStopwords: _SEARCHWP_VARS.i18n.save_stopwords,
				saving: _SEARCHWP_VARS.i18n.saving,
				suggestions: _SEARCHWP_VARS.i18n.suggestions,
				stopwordsSaved: _SEARCHWP_VARS.i18n.stopwords_saved,
				sortAlphabetically: _SEARCHWP_VARS.i18n.sort_alphabetically,
				suggestedStopwords: _SEARCHWP_VARS.i18n.suggested_stopwords,
				suggestedStopwordsNote: _SEARCHWP_VARS.i18n.suggested_stopwords_note,
				term: _SEARCHWP_VARS.i18n.term
			}
		}
	}
}
</script>

<style lang="scss">
	.searchwp-stopwords {

		.vue-input-tag-wrapper {
			border: 0;
			padding: 0;

			.input-tag {
				display: flex;
				align-items: center;
				border-color: #dcdcdc;
				background-color: #f5f5f5;
				color: #686868;
				padding: 0 5px;

				span {
					font-weight: normal;
				}

				a.remove {
					font-weight: bold;
					color: #686868;
					opacity: 0.6;
					padding-left: 4px;

					&:before {
						display: block;
						content: '';
						width: 8px;
						height: 8px;
						background-color: transparent;
						background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTUiIGhlaWdodD0iMTUiIHZpZXdCb3g9IjAgMCAxNSAxNSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48dGl0bGU+QXJ0Ym9hcmQ8L3RpdGxlPjxnIGZpbGw9IiM2ODY4NjgiIGZpbGwtcnVsZT0iZXZlbm9kZCI+PHBhdGggZD0iTTEuMTM2IDMuMjU3NEwzLjI1NzQgMS4xMzYgMTMuODY0IDExLjc0MjZsLTIuMTIxNCAyLjEyMTR6Ii8+PHBhdGggZD0iTTEuMTM2IDExLjc0MjZMMTEuNzQyNiAxLjEzNmwyLjEyMTQgMi4xMjE0TDMuMjU3NCAxMy44NjR6Ii8+PC9nPjwvc3ZnPg==');
						background-size: 100% 100%;
						background-repeat: no-repeat;
						background-position: 50% 50%;
						margin-top: 1px;
					}

					&:hover:before {
						background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTUiIGhlaWdodD0iMTUiIHZpZXdCb3g9IjAgMCAxNSAxNSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48dGl0bGU+QXJ0Ym9hcmQ8L3RpdGxlPjxnIGZpbGw9IiNkYzMyMzIiIGZpbGwtcnVsZT0iZXZlbm9kZCI+PHBhdGggZD0iTTEuMTM2IDMuMjU3NEwzLjI1NzQgMS4xMzYgMTMuODY0IDExLjc0MjZsLTIuMTIxNCAyLjEyMTR6Ii8+PHBhdGggZD0iTTEuMTM2IDExLjc0MjZMMTEuNzQyNiAxLjEzNmwyLjEyMTQgMi4xMjE0TDMuMjU3NCAxMy44NjR6Ii8+PC9nPjwvc3ZnPg==');
					}
				}
			}

			.new-tag {
				box-shadow: none;
				margin-right: 0.5em;
				padding: 0;
			}
		}
	}

	.searchwp-stopwords-suggestions__modal {
		max-width: 600px;
		text-align: left;

		table {
			width: 100%;
			border-collapse: collapse;
			font-size: 0.9em;
			margin-top: 1em;

			th {
				padding-bottom: 0.7em;
				text-align: left;
				white-space: nowrap;
			}

			td {
				border-top: 1px solid #eaeaea;
				padding: 0.5em 2em 0.4em 0;

				&:last-of-type {
					padding-right: 0;
				}
			}

			.searchwp-delete {
				position: relative;
				top: 1px;
			}
		}
	}
</style>
