<template>

    <div class="team-game-stats">

        <section>

            <div v-for="(player, index) in team.players" 
                class="stats-player row"
            >
                <div class="column">{{ player.full_name }}</div>
            </div>

        </section>

    </div>

</template>

<script>

    import ListTransition from './ListTransition'
    import TeamMixins from './TeamMixins'
    import StatMixins from './StatMixins'

    export default {

        mixins: [ListTransition, TeamMixins, StatMixins],

        /*
        data: function () {
            return {
                team_id: ''
            }
        },
        */

        props: ['team_id'],

        methods: {
        },

        mounted() {

            var vue = this;

            vue.loadTeam(this.team_id);

            window.socket.emit('join-room', 'team.' + this.team_id);

            window.socket.on('App\\Events\\TeamUpdated', function (data) {
                vue.$store.dispatch('addFeedback', {'type': 'success', 'message': data.message});
                vue.loadTeam(this.team_id);
            });
        
        },

        beforeDestroy() {

            window.socket.removeListener('App\\Events\\TeamUpdated');
            window.socket.emit('leave-room', 'team.' + this.team_id);

        }

    };
</script>
