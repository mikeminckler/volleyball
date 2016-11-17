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
    import StatsMixins from './StatsMixins'

    export default {

        mixins: [ListTransition, TeamMixins, StatsMixins],

        /*
        data: function () {
            return {
                team_id: ''
            }
        },
        */

        props: ['team_id'],

        watch: {
            team_id: function(value) {
                var vue = this;

                vue.loadTeam(value);

                window.socket.emit('join-room', 'team.' + value);

                window.socket.on('App\\Events\\TeamUpdated', function (data) {
                    vue.$store.dispatch('addFeedback', {'type': 'success', 'message': data.message});
                    vue.loadTeam(value);
                });
            }
        },

        methods: {
        },

        beforeDestroy() {

            window.socket.removeListener('App\\Events\\TeamUpdated');
            window.socket.emit('leave-room', 'team.' + this.team_id);

        }

    };
</script>
