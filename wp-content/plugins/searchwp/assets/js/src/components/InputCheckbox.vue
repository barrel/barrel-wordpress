<template>
    <div class="searchwp-input-checkbox">
        <input
            type="checkbox"
            :id="'searchwp_input_checkbox_' + _uid"
            v-model="val"/>
        <label :for="'searchwp_input_checkbox_' + _uid">{{ label }}</label>
    </div>
</template>

<script>
export default {
    name: 'SearchwpInputCheckbox',
    data() {
        return {
            val: this['checked'],
        };
    },
    model: {
        prop: 'checked',
        event: 'change'
    },
    props: {
        checked: {
            type: Boolean
        },
        label: {
            type: String,
            default: ''
        }
    },
    watch: {
        val(value) {
            this.$emit('change', value);
        }
    }
}
</script>

<style lang="scss">
    .searchwp-input-checkbox {
        display: flex;
        align-items: center;
        line-height: 1.4;
        padding-top: 0.5em;

        input {
            display: inline-block;
            margin-top: 1px;
            margin-right: 0.6em;
        }
    }
</style>
