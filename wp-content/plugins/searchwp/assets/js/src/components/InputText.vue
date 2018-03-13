<template>
    <div class="searchwp-input-text">
        <label :for="'searchwp_input_text_' + _uid">{{ label }}</label>
        <input
            type="text"
            ref="input"
            :value="value"
            :placeholder="placeholder"
            :id="'searchwp_input_text_' + _uid"
            @input="$emit('input', $event.target.value)"/>
    </div>
</template>

<script>
export default {
    name: 'SearchwpInputText',
    props: {
        value: {
            type: [String, Number],
            default: ''
        },
        label: {
            type: String,
            default: ''
        },
        placeholder: {
            type: String,
            default: ''
        }
    }
}
</script>

<style lang="scss">
    .searchwp-input-text {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1em;

        label {
            display: block;
            width: 40%;
        }

        input {
            display: block;
            width: 55%;
            border: 1px solid #e8e8e8;
            background: #fff;
            border-radius: 3px;
            padding: 8px;
            box-shadow: none;
        }
    }
</style>
