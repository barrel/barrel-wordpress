<template>
	<div class="searchwp-statistics-chart searchwp-statistics-chart--line">
		<line-chart
			:chart-data="datacollection"
			:options="options"
		></line-chart>
	</div>
</template>

<script>
	import LineChart from '../inc/LineChart.js'

	export default {
	components: {
		LineChart
	},
	props: {
		datacollection: Object,
		options: {
			type: Object,
			default: {}
		}
	}
}
</script>

<style lang="scss">
	.searchwp-statistics-chart {
		width: 100%;
		// height: 30px;
		position: relative;
	}
</style>
