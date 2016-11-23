<template>

    <div class="team-game-stats">

        <transition-group 
            name="form-list" 
            tag="div"
            v-bind:css="false"
            v-on:before-enter="beforeEnter"
            v-on:enter="enter"
            v-on:leave="leave"
        >

            <div v-for="(player, index) in team.players" 
                class="stats-player row"
                :key="player.id"
                :data-index="index"
            >

                <div class="column">{{ player.full_name }}</div>

                <div class="column" v-for="stat in stats">

                    <player-game-stat :stat="stat" :team="team" :player="player" :game="game"></player-game-stat>

                </div>

            </div>

        </transition-group>

    </div>

</template>

<script>

    import ListTransition from './ListTransition'
    import TeamMixins from './TeamMixins'
    import StatMixins from './StatMixins'

    export default {

        mixins: [ListTransition, StatMixins, TeamMixins],

        props: ['team_id', 'game'],

        methods: {
        },

        mounted() {

            var vue = this;

            vue.loadTeam(this.team_id);

            vue.loadTeamStats(this.team_id);

            window.socket.emit('join-room', 'team.' + this.team_id);

            window.socket.on('App\\Events\\TeamUpdated', function (data) {
                vue.$store.dispatch('addFeedback', {'type': 'success', 'message': data.message});
                vue.loadTeam(this.team_id);
            });

            window.socket.on('App\\Events\\PlayerGameStatsUpdated', function (data) {
                let index = _.findIndex(vue.team.players, function(o) {return o.id == data.player.id});
                vue.team.players[index].updated_at = new Date().getTime();
            });
        
        },

        beforeDestroy() {

            window.socket.removeListener('App\\Events\\TeamUpdated');
            window.socket.emit('leave-room', 'team.' + this.team_id);

        }

    };
</script>
