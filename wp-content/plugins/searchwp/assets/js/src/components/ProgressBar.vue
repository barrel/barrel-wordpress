<template>
    <div :class="[
            disabled || alternateIndexer ? 'searchwp-progress-bar--is-disabled' : '',
            'searchwp-progress-bar'
        ]">
        <div :class="[
                progress < 100 ? 'searchwp-progress-bar--is-active' : '',
                'searchwp-progress-bar__progress'
            ]"
            :style="{ width: progress + '%' }"></div>
    </div>
</template>

<script>
export default {
    name: 'SearchwpProgressBar',
    data: function() {
        return {
            alternateIndexer: _SEARCHWP_VARS.data.misc.alternate_indexer
        }
    },
    props: {
        progress: {
            type: Number,
            default: 0,
            required: true
        },
        disabled: {
            type: Boolean,
            default: false,
            required: true
        }
    }
}
</script>

<style lang="scss">
    $colorBand: rgba(21, 159, 210, .2);

    .searchwp-progress-bar {
        width: 100%;
        position: relative;
        height: 8px;
        border-radius: 16px;
        transition:width 1s ease-in-out;
        background-color:#e4f7ff;
        background-size: 30px 30px;
        background-image:-webkit-gradient(linear, left top, right bottom,
        color-stop(.25, $colorBand), color-stop(.25, transparent),
        color-stop(.5, transparent), color-stop(.5, $colorBand),
        color-stop(.75, $colorBand), color-stop(.75, transparent),
        to(transparent));
        background-image:-webkit-linear-gradient(135deg, $colorBand 25%, transparent 25%,
        transparent 50%, $colorBand 50%, $colorBand 75%,
        transparent 75%, transparent);
        background-image:-moz-linear-gradient(135deg, $colorBand 25%, transparent 25%,
        transparent 50%, $colorBand 50%, $colorBand 75%,
        transparent 75%, transparent);
        background-image:-ms-linear-gradient(135deg, $colorBand 25%, transparent 25%,
        transparent 50%, $colorBand 50%, $colorBand 75%,
        transparent 75%, transparent);
        background-image:-o-linear-gradient(135deg, $colorBand 25%, transparent 25%,
        transparent 50%, $colorBand 50%, $colorBand 75%,
        transparent 75%, transparent);
        background-image:linear-gradient(135deg, $colorBand 25%, transparent 25%,
        transparent 50%, $colorBand 50%, $colorBand 75%,
        transparent 75%, transparent);
        -webkit-animation:animate-stripes 3s linear infinite;
        -moz-animation:animate-stripes 3s linear infinite;

        &.searchwp-progress-bar--is-disabled {
            background:#fff;
        }
    }

    .searchwp-progress-bar__progress {
        height: 8px;
        border-radius: 16px;
        background: #159FD2;
        transition: width 400ms linear;
        position: absolute;
        top: 0;
        left: 0;
    }

    @-webkit-keyframes animate-stripes {
        0% {background-position:0 0;} 100% {background-position:60px 0;}
    }

    @-moz-keyframes animate-stripes {
        0% {background-position:0 0;} 100% {background-position:60px 0;}
    }
</style>
