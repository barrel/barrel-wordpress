<script>
export default {
    name: 'SearchwpButton',
    render(createElement) {
        let modifiers = {};

        if (this.position == 'below') {
            modifiers = {
                bottom: true
            };
        } else {
            modifiers = {
                top: true
            };
        }

        return createElement(
            'a', {
                directives: [
                    {
                        name: 'popover',
                        arg: this.popover,
                        modifiers: modifiers
                    },
                ],
                nativeOn: {
                    click: this.clickHandler
                },
                on: {
                    click: this.clickHandler
                },
                'class': ['searchwp-button', 'button'],
                domProps: {
                    innerHTML: this.buttonText
                }
            }
        );
    },
    methods: {
        clickHandler() {
            if (!this.popover) {
                this.$emit('click');
            } else {
                // This is a popover so the popover is shown
            }
        }
    },
    props: {
        popover: {
            type: [Boolean, String],
            default: false,
            required: false
        },
        buttonText: {
            type: String,
            default: 'Button',
            required: true
        },
        position: {
            type: String,
            default: 'above',
            required: false
        }
    }
}
</script>

<style lang="scss">
    .wp-core-ui .button.searchwp-button {
        display: inline-block;
        margin-top: 0.9em;
        margin-bottom: 0.1em;
    }
</style>
