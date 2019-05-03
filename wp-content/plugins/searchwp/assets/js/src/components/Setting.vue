<template>
	<spinner
		v-if="saving"
		class="searchwp-spinner-message"
		:size="16"
		:line-size="2"
		:line-bg-color="'#1e8cbe'"
		:line-fg-color="'#ffffff'"
		:text-fg-color="'#444444'"
		:message="label"
		/>
	<searchwp-input-checkbox
		v-else
		:label="label"
		:checked="state"
		:tooltip="tooltip"
		:disabled="disabled"
		v-model="state"
		v-on:change="changed"/>
</template>

<script>
import Vue from 'vue';
import { EventBus } from './../EventBus.js';
import Spinner from 'vue-simple-spinner';
import SearchwpInputCheckbox from './InputCheckbox.vue';

export default {
	name: 'Setting',
	props: {
		name: {
			type: String,
			required: true
		},
		label: {
			type: String,
			required: true
		},
		value: {
			type: Boolean,
			required: true
		},
		tooltip: {
			type: String,
			default: ''
		},
		disabled: {
			type: Boolean,
			default: false
		}
	},
	components: {
		Spinner,
		'searchwp-input-checkbox': SearchwpInputCheckbox
	},
	methods: {
		saved() {
			this.saving = false;
		},
		changed() {
			this.saving = true;
			this.$emit('change', { value: this.state, name: this.name })
		}
	},
	created() {
		this.state = this.value;
		EventBus.$on('saved', this.saved);
	},
	data: function() {
		return {
			saving: false,
			state: false
		}
	}
}
</script>

<style lang="scss">

</style>
