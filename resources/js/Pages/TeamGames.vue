<template>

    <div class="content">

        <team-games-list :teamId="team.id" v-if="team.id"></team-games-list>
            
    </div>

</template>

<script>

    import Team from '@/Mixins/Team'

    export default {

        mixins: [Team],

        methods: {

        },

        beforeMount() {
            let team_id = this.$route.params.id;
            if (team_id == 'all') {
                team_id = this.$store.state.activeTeam.id;
            }
            this.loadTeam(team_id);
        },

        mounted() {
            

        },


    }

</script>
