<template>
	<div :class="['searchwp-notice-persist', 'notice', 'notice-' + type, dismissable ? 'is-dismissable' : '']">
		<p>
			<span class="searchwp-notice-text"><slot></slot></span>
			<a v-if="link && link.length && linkText && linkText.length" :href="link" target="_BLANK">{{ linkText }}</a>
			<button v-if="buttonText" @click="emitButtonClick" class="button">{{ buttonText }}</button>
			<button v-if="dismissable" @click="emitDismissed" type="button" class="notice-dismiss">
				<span class="screen-reader-text">{{ i18n.dismiss }}</span>
			</button>
		</p>
	</div>
</template>

<script>
import Vue from 'vue';

export default {
	name: 'SearchwpNotice',
	props: {
		type: {
			type: String,
			default: 'success',
			required: false
		},
		link: {
			type: String,
			required: false
		},
		linkText: {
			type: String,
			required: false
		},
		buttonText: {
			type: String,
			required: false
		},
		dismissable: {
			type: Boolean,
			required: false,
			default: false
		}
	},
	methods: {
		emitDismissed() {
			this.$emit('dismissed');
		},
		emitButtonClick() {
			this.$emit('buttonClick');
		}
	},
	data: function() {
		return {
			i18n: {
				dismiss: _SEARCHWP_VARS.i18n.dismiss
			}
		}
	}
}
</script>

<style lang="scss">
	.searchwp-notice-persist {
		margin: 10px 0 3px;

		// WordPress core override.
		&.notice {
			margin: 10px 0 3px;
		}

		&.is-dismissable {
			position: relative;
			padding-right: 38px;
		}

		p {
			display: flex;
			align-items: center;
			justify-content: space-between;
		}

		span {
			display: inline-block;
			flex: 1;
		}

		a, button {
			display: inline-block;
			margin-left: 1em;
		}

		.searchwp-notice-text a {
			margin-left: 0;
		}

		&.notice-success {
			background-color: #ebf8ec;
		}

		&.notice-error {
			background-color: #ffefef;
		}

		&.notice-warning {
			background-color: #fff8e7;
		}

		.notice-dismiss {

			&:before {
				line-height: 22px;
			}
		}
	}
</style>
