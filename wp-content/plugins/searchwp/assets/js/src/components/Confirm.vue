<template>
	<v-popover offset="6" :placement="placement">
		<searchwp-button :icon="icon" :label="buttonLabel" />
		<template slot="popover">
			<div class="searchwp-confirmation">
				<p v-if="question.length">{{ question }}</p>
				<button @click="confirmed" v-close-popover>{{ confirm }}</button>
			</div>
		</template>
	</v-popover>
</template>

<script>
import Vue from 'vue';
import SearchwpButton from './Button.vue';

export default {
	name: 'SearchwpConfirm',
	components: {
        SearchwpButton
    },
	props: {
		buttonLabel: {
			type: String,
			required: true
		},
		icon: {
			type: String,
			required: false
		},
		question: {
			type: String,
			default: 'Are you sure?',
			required: false
		},
		confirm: {
			type: String,
			default: 'Yes',
			required: false
		},
		placement: {
			type: String,
			default: 'bottom',
			required: false
		}
	},
	methods: {
		confirmed() {
			this.$emit('confirmed');
		}
	},
	data: function() {
		return {
		}
	}
}
</script>

<style lang="scss">
	.searchwp-confirmation {

		p {
			text-align: center;
			font-weight: bold;
			padding: 0.5em 1.5em 0;
			margin: 0 0 0.5em;
		}

		button {
			display: block;
			width: 100%;
			margin: 0.5em 0 0;
			padding: 0.3em 0.5em;
			border: 0;
			background: transparent;
			color: #fff;
			text-align: center;
			cursor: pointer;

			&:hover {
				background: #159FD2;
			}
		}
	}
</style>
