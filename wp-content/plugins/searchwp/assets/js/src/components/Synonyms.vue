<template>
	<div class="searchwp-synonyms">

		<searchwp-notice
			v-if="termViolations && termViolations.length > 0"
			type="warning"
			:dismissable="false"
			link="https://searchwp.com/?p=164370"
			:linkText="i18n.moreInfo">
			<component :is="translatedMinWordLengthNote"></component>
		</searchwp-notice>

		<div class="searchwp-synonyms__list" id="searchwp-synonyms__list">
			<table v-if="synonyms && synonyms.length">
				<thead>
					<tr>
						<th>
							<searchwp-tooltip :content="i18n.searchTermNote">{{ i18n.searchTerm }}</searchwp-tooltip>
						</th>
						<th>
							<searchwp-tooltip :content="i18n.synonymsNote">{{ i18n.synonyms }}</searchwp-tooltip>
						</th>
						<th>
							<searchwp-tooltip :content="i18n.replaceNote">{{ i18n.replace }}</searchwp-tooltip>
						</th>
					</tr>
				</thead>
				<draggable
					v-model="synonyms"
					:options="{
						group: 'synonyms',
						handle: '.dashicons'
					}"
					:element="'tbody'">
					<tr v-for="(synonym, synonymIndex) in synonyms"
						:key="'synonym' + synonymIndex">
						<td class="searchwp-synonyms__term">
							<div>
								<button @click="remove(synonymIndex)"><span class="dashicons dashicons-dismiss"><span class="searchwp-hidden">{{ i18n.remove }}</span></span></button>
								<input @keyup.enter="add" v-model="synonym.term" @blur="checkSourceTerms()"/>
							</div>
						</td>
						<td class="searchwp-synonyms__synonyms">
							<input @keyup.enter="add" v-model="synonym.synonyms"/>
						</td>
						<td class="searchwp-synonyms__replace">
							<div>
								<searchwp-input-checkbox
									:label="i18n.replace"
									:checked="synonym.replace"
									v-model="synonym.replace"/>
								<span class="dashicons dashicons-menu"></span>
							</div>
						</td>
					</tr>
				</draggable>
			</table>
			<div v-else class="searchwp-no-synonyms">
				<p class="description">{{ i18n.synonymsNone }}</p>
			</div>
		</div>
		<ul v-if="!saved && !saving" class="searchwp-actions">
			<li><button class="button button-primary" @click="save">{{ i18n.saveSynonyms }}</button></li>
			<li><button class="button" @click="add">{{ i18n.addNew }}</button></li>
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
			<span v-else>{{ i18n.synonymsSaved }}</span>
		</searchwp-notice>
	</div>
</template>

<script>
import Vue from 'vue';
import draggable from 'vuedraggable';
import Tooltip from './Tooltip.vue';
import Spinner from 'vue-simple-spinner';
import Checkbox from './InputCheckbox.vue';
import SearchwpNotice from './Notice.vue';

export default {
	name: 'Synonyms',
	components: {
		'searchwp-tooltip': Tooltip,
		'searchwp-input-checkbox': Checkbox,
		draggable,
		Spinner,
		'searchwp-notice': SearchwpNotice
	},
	computed: {
		translatedMinWordLengthNote() {
			return {
				template: '<span>' + this.i18n.minWordLengthNote.replace( '{{ minWordLength }}', this.minWordLength ) + '</span>'
			}
		}
	},
	methods: {
		remove(index) {
			this.synonyms.splice(index, 1);
			this.checkSourceTerms();
		},
		save() {
			const data = {
				action: 'searchwp_update_synonyms',
				synonyms: JSON.stringify(this.synonyms),
				_ajax_nonce: _SEARCHWP_VARS.nonces.update_synonyms
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
		add() {
			this.synonyms.push({
				term: '',
				synonyms: '',
				replace: false
			});

			// Scroll to the bottom
			setTimeout(function(){
				var objDiv = document.getElementById("searchwp-synonyms__list");
				objDiv.scrollTop = objDiv.scrollHeight;

				// Select the last input
				var lastInput = document.querySelectorAll("#searchwp-synonyms__list tbody > tr:last-of-type > td:first-of-type input");
				for(const input of lastInput) {
					input.focus();
				}
			}, 100);
		},
		checkSourceTerms() {
			this.termViolations.splice(0);
			this.checkForMinCharacterCount();
		},
		checkForMinCharacterCount() {
			this.synonyms.map(synonym => {
				if (synonym.term.length < this.minWordLength) {
					this.addViolation('minWordLength');
				}
			});
		},
		addViolation(violation) {
			if (this.termViolations.indexOf(violation) == -1) {
				this.termViolations.push(violation);
			}
		}
	},
	created() {
		let synonyms = _SEARCHWP_VARS.data.synonyms;

		if (!synonyms || !synonyms.length) {
			return;
		}

		this.synonyms = synonyms.map(synonym => {
			synonym.synonyms = synonym.synonyms.join(', ');
			return synonym;
		});

		this.checkSourceTerms();
	},
	data: function() {
		return {
			saving: false,
			saved: false,
			synonyms: [],
			minWordLength: _SEARCHWP_VARS.data.min_word_length,
			termViolations: [],
			i18n: {
				addNew: _SEARCHWP_VARS.i18n.add_new,
				minWordLengthNote: _SEARCHWP_VARS.i18n.min_word_length_note,
				moreInfo: _SEARCHWP_VARS.i18n.more_info,
				remove: _SEARCHWP_VARS.i18n.remove,
				replace: _SEARCHWP_VARS.i18n.replace,
				replaceNote: _SEARCHWP_VARS.i18n.replace_note,
				saveSynonyms: _SEARCHWP_VARS.i18n.save_synonyms,
				saving: _SEARCHWP_VARS.i18n.saving,
				searchTerm: _SEARCHWP_VARS.i18n.search_term,
				searchTermNote: _SEARCHWP_VARS.i18n.search_term_note,
				synonyms: _SEARCHWP_VARS.i18n.synonyms_maybe_plural,
				synonymsNone: _SEARCHWP_VARS.i18n.synonyms_none,
				synonymsNote: _SEARCHWP_VARS.i18n.synonyms_note_tooltip,
				synonymsSaved: _SEARCHWP_VARS.i18n.synonyms_saved
			}
		}
	}
}
</script>

<style lang="scss">
	.searchwp-synonyms {
		display: flex;
		flex-direction: column;
		flex: 1;

		table {
			width: 100%;
		}

		thead {
			position: sticky;
			top: 0;

			tr, th {
				background: #fff;
			}
		}

		th {
			text-align: left;
			padding-bottom: 0.2em;
		}

		td {
			padding: 0.3em 0;
		}

		.searchwp-tooltip {

			.dashicons {
				color: #ccc;
			}
		}
	}

	.searchwp-synonyms__list {
		flex: 1;
		overflow: auto;
	}

	.searchwp-synonyms__term {

		button {
			padding: 0;
			margin: 0 6px 0 0;
			display: block;
			border: 0;
			box-shadow: none;
			background: transparent;
			color: #ccc;
			cursor: pointer;
		}

		> div {
			display: flex;
			align-items: center;
		}
	}

	.searchwp-synonyms__term,
	.searchwp-synonyms__synonyms {

		input {
			display: block;
			width: 85%;
			border: 1px solid #e8e8e8;
			background: #fff;
			border-radius: 3px;
			padding: 8px;
			box-shadow: none;
		}
	}

	.searchwp-hidden {
		border: 0;
		clip: rect(0 0 0 0);
		height: 1px;
		margin: -1px;
		overflow: hidden;
		padding: 0;
		position: absolute;
		width: 1px;
	}

	.searchwp-synonyms__replace {

		> div {
			display: flex;
			align-items: center;
			justify-content: space-between;
		}

		.searchwp-input-checkbox {
			padding-top: 0;
		}

		.dashicons {
			cursor: move;
			color: #ccc;
			display: block;
			margin-left: 0.5em;
		}
	}

	.searchwp-no-synonyms {
		margin: 2em 10%;
		text-align: center;
		padding: 2em;
		border: 1px solid #dcdcdc;
		border-radius: 3px;
		background: #f7f7f7;

		p.description {
			font-size: 1.3em;
		}
	}
</style>
