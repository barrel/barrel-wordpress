<template>
    <a class="searchwp-remove" href="#">
        <span v-if="!icon">{{ text }}</span>
        <span v-else class="dashicons dashicons-dismiss"></span>
    </a>
</template>

<script>
export default {
    name: 'SearchwpRemove',
    data: function() {
        return {}
    },
    props: {
        text: {
            type: String,
            default: _SEARCHWP_VARS.i18n.remove,
            required: false
        },
        icon: {
            type: Boolean,
            default: false
        }
    }
}
</script>

<style lang="scss">
    .searchwp-remove {
        color: #AA0000;
        text-decoration: underline;
        font-size: 12px;
        font-weight: normal;

        &:hover {
            color: #AA0000;
            text-decoration: none;

            span {
                opacity: 1;
            }
        }
    }
</style>
