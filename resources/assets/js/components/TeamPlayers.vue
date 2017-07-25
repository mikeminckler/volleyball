<template>

    <div class="team-players">
        <team-players-list :team="team" v-if="team"></team-players-list>
    </div>

</template>

<script>

    import TeamMixins from './TeamMixins'

    export default {

        mixins: [TeamMixins],

        data: function () {
            return {
                //
            }
        },

        props: [],

        computed: {
            //
        },

        created () {
            //
        },

        mounted () {
            //
        },

        beforeMount() {
            var vue = this;
            let team_id = vue.$route.params.id;
            this.loadTeam(team_id);
        },

        methods: {
            
        }
    };

</script>
