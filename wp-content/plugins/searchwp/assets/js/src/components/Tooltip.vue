<template>
	<span class="searchwp-tooltip">
		<span>
			<slot></slot>
		</span>
		<span
			class="dashicons dashicons-editor-help"
			v-tooltip="content"
		></span>
	</span>
</template>

<script>
import Vue from 'vue';

export default {
	props: {
		content: String
	}
}
</script>

<style lang="scss">
	.searchwp-tooltip {
		display: flex;
		align-items: center;

		.dashicons {
			margin-left: 0.2em;
			width: 18px;
			height: 18px;
			font-size: 18px;
		}
	}
</style>
