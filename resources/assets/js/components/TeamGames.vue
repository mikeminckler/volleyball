<template>

    <div class="content">

        <team-games-list :teamId="team.id" v-if="team.id"></team-games-list>
            
    </div>

</template>

<script>

    import TeamMixins from './TeamMixins'

    export default {

        mixins: [TeamMixins],

        methods: {

        },

        beforeMount() {
            let team_id = this.$route.params.id;
            this.loadTeam(team_id);
        },

        mounted() {
            

        },


    }

</script>
