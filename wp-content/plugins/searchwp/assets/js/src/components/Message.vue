<template>
    <div :class="['searchwp-message', 'searchwp-message__' + type]">
        <slot></slot>
        <div v-if="moreInfo">
            <a :href="moreInfo" class="button" target="_blank">{{ i18n.moreInfo }}</a>
        </div>
        <div v-if="typeof action.target === 'string' && action.text.length">
            <a :href="action.target" class="button">{{ action.text }}</a>
        </div>
        <div v-else-if="typeof action.target === 'function' && action.text.length">
            <a href="#" @click.prevent="action.target" class="button">{{ action.text }}</a>
        </div>
    </div>
</template>

<script>
export default {
    name: 'SearchwpMessage',
    data: function(){
        return {
            i18n: {
                moreInfo: _SEARCHWP_VARS.i18n.more_info
            }
        }
    },
    props: {
        type: {
            type: String,
            default: 'notice',
            required: false
        },
        moreInfo: {
            type: String,
            default: '',
            required: false
        },
        action: {
            type: Object,
            default: function(){
                return {
                    target: '#',
                    text: ''
                };
            },
            required: false
        }
    }
}
</script>

<style lang="scss">
    .searchwp-message {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin: 5px 0 15px;
        border-left: 4px solid #00a0d2;
        background: #fff;
        box-shadow: 0 1px 1px 0 rgba( 0, 0, 0, 0.1 );
        padding: 1px 12px;

        p {
            margin: 0.5em 0;
            padding: 2px;
        }

        .button {
            display: inline-block;
            margin: 0.5em 0 0.5em 2em;
        }

        &.searchwp-message__warning {
            border-left-color: #ffb900;
        }

        &.searchwp-message__success {
            border-left-color: #46b450;
        }

        &.searchwp-message__error {
            border-left-color: #dc3232;
        }
    }
</style>
