<template>
	<button class="searchwp-button button" @click="buttonClick">
		<span>
			<span v-if="icon && icon.length" :class="['dashicons', icon ]"></span>
			<span>{{ label }}</span>
		</span>
	</button>
</template>

<script>
import Vue from 'vue';

export default {
	name: 'SearchwpButton',
	props: {
		label: {
			type: String,
			required: true
		},
		icon: {
			type: String,
			required: false
		}
	},
	methods: {
		buttonClick() {
			this.$emit('buttonClick');
		}
	}
}
</script>

<style lang="scss">
	.searchwp-button {

		> span {
			display: flex;
			align-items: center;

			span {
				display: block;
			}
		}

		.dashicons {
			margin-right: 0.2em;
		}
	}
</style>
