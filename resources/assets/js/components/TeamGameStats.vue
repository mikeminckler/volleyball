<template>

    <div class="team-game-stats">

        <div class="row stats-player">
            <div class="column player-name"></div>
            <div class="column" v-for="stat in team.stats">
                {{ stat.stat_name }}
            </div>
        </div>

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

                <div class="column player-name">{{ player.full_name }}</div>

                <div class="column" v-for="stat in team.stats">

                    <player-game-stat :stat="stat" :team="team" :player="player" :game="game" :controls="controls"></player-game-stat>

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

        props: ['team_id', 'game', 'controls'],

        methods: {

            updatePlayerStats: function(player_id) {
                let time = new Date().getTime();
                let player_index = _.findIndex(this.team.players, function(o) {return o.id == player_id});
                this.team.players[player_index].updated_at = time;
            }, 

            updateStat: function(stat_id) {
                let time = new Date().getTime();
                let stat_index = _.findIndex(this.team.stats, function(o) {return o.id == stat_id});
                this.team.stats[stat_index].updated_at = time;
            }

        },

        mounted() {

            var vue = this;

            vue.loadTeam(this.team_id);

            window.socket.emit('join-room', 'team.' + this.team_id);

            window.socket.on('App\\Events\\TeamUpdated', function (data) {
                vue.$store.dispatch('addFeedback', {'type': 'success', 'message': data.message});
                vue.loadTeam(this.team_id);
            });

            window.socket.on('App\\Events\\PlayerGameStatsUpdated', function (data) {
                vue.updatePlayerStats(data.player.id);
                vue.updateStat(data.stat.id);
            });
        
        },

        beforeDestroy() {

            window.socket.removeListener('App\\Events\\TeamUpdated');
            window.socket.removeListener('App\\Events\\PlayerGameStatsUpdated');
            window.socket.emit('leave-room', 'team.' + this.team_id);

        }

    };
</script>
