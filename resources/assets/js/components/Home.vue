<template>

    <div class="content">

        <team-games-list :teamId="team.id" v-if="team.id"></team-games-list>
    
    </div>

</template>

<script>

    import ListTransition from './ListTransition'
    import Helpers from './Helpers'
    import TeamMixins from './TeamMixins'

    export default {

        mixins: [ListTransition, Helpers, TeamMixins],

        methods: {
        
        },

        mounted() {

            this.team = this.$store.state.activeTeam;
            window.socket.on('App\\Events\\GamesRefresh', data => {
                this.loadActiveTeam();
            });

        },

        beforeDestroy() {
            window.socket.removeListener('App\\Events\\GamesRefresh');
        }

    }
</script>
