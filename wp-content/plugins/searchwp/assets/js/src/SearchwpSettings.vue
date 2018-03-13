<template>

    <div class="searchwp-settings">

        <searchwp-environment-check/>

        <div class="searchwp-engines-index-stats metabox-holder">
            <searchwp-engines/>
            <searchwp-index-stats/>
        </div>

    </div>

</template>

<script>
import SearchwpEnvironmentCheck from "./components/EnvironmentCheck.vue";
import SearchwpEngines from "./components/Engines.vue";
import SearchwpIndexStats from "./components/IndexStats.vue";

export default {
    name: 'SearchwpSettings',
    components: {
        'searchwp-environment-check': SearchwpEnvironmentCheck,
        'searchwp-engines': SearchwpEngines,
        'searchwp-index-stats': SearchwpIndexStats
    }
}
</script>

<style lang="scss">
    .searchwp-loading {
        background: transparent url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjgiIGhlaWdodD0iMjgiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgdmlld0JveD0iMCAwIDEwMCAxMDAiIHByZXNlcnZlQXNwZWN0UmF0aW89InhNaWRZTWlkIiBjbGFzcz0ibGRzLWRpc2siIHN0eWxlPSJiYWNrZ3JvdW5kOm5vbmUiPjxnIHRyYW5zZm9ybT0ibWF0cml4KC42IDAgMCAuNiA1MCA1MCkiPjxjaXJjbGUgcj0iNTAiIGZpbGw9ImdyYXkiLz48Y2lyY2xlIGN5PSItMjgiIHI9IjE1IiBmaWxsPSIjZmZmIiB0cmFuc2Zvcm09InJvdGF0ZSgzNDIpIj48YW5pbWF0ZVRyYW5zZm9ybSBhdHRyaWJ1dGVOYW1lPSJ0cmFuc2Zvcm0iIHR5cGU9InJvdGF0ZSIgY2FsY01vZGU9ImxpbmVhciIgdmFsdWVzPSIwIDAgMDszNjAgMCAwIiBrZXlUaW1lcz0iMDsxIiBkdXI9IjFzIiBiZWdpbj0iMHMiIHJlcGVhdENvdW50PSJpbmRlZmluaXRlIi8+PC9jaXJjbGU+PC9nPjwvc3ZnPg==') 50% 50% no-repeat;
        background-size: 28px 28px;

        * {
            visibility: hidden;
        }
    }

    .searchwp-engines-index-stats {
        display: flex;
        justify-content: space-between;

        .searchwp-engines {
            flex: 1;
        }

        .searchwp-index-stats {
            width: 350px;
            margin-left: 2.5%;
        }
    }
</style>
