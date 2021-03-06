<template>

    <div class="team-game-stats">

        <div class="row stats-player">
            <div class="column player-name"></div>
            <div class="column" v-for="stat in team.stats">
                {{ stat.stat_name }}
            </div>
            <div class="column icon" v-if="team.stats">
                <div class="icon" @click="undo = !undo">
                    <i class="fas fa-undo"></i>
                </div>
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

                <div class="column icon" v-if="undo">
                    <div class="icon" @click="removeLastPlayerStat(player.id)">
                        <i class="fas fa-undo"></i>
                    </div>
                </div>

            </div>

        </transition-group>

    </div>

</template>

<script>

    import Team from '@/Mixins/Team'
    import Stat from '@/Mixins/Stat'

    export default {

        mixins: [Stat, Team],

        props: ['team_id', 'game', 'controls'],

        data: function() {
            return {
                undo: false
            }
        },

        methods: {

            updatePlayerStats: function(player_id) {
                let time = new Date().getTime();
                let player_index = this.$lodash.findIndex(this.team.players, function(o) {return o.id == player_id});
                this.team.players[player_index].updated_at = time;
            }, 

            updateStat: function(stat_id) {
                let time = new Date().getTime();
                let stat_index = this.$lodash.findIndex(this.team.stats, function(o) {return o.id == stat_id});
                this.team.stats[stat_index].updated_at = time;
            },

            removeLastPlayerStat: function(playerId)
            {
                this.$http.post('/api/players/remove-last-stat/' + playerId).then( response => {
                    this.$store.dispatch('addFeedback', {'type': 'success', 'message': response.data.success});
                }, error => {
                    this.$store.dispatch('addFeedback', {'type': 'error', 'message': error.response.data.error});
                });
            }

        },

        mounted() {

            this.loadTeam(this.team_id);

            window.socket.emit('join-room', 'team.' + this.team_id);

            window.socket.on('App\\Events\\TeamUpdated', data => {
                this.$store.dispatch('addFeedback', {'type': 'success', 'message': data.message});
                this.loadTeam(this.team_id);
            });

            window.socket.on('App\\Events\\PlayerGameStatsUpdated', data => {
                this.updatePlayerStats(data.player.id);
                this.updateStat(data.stat.id);
            });
        
        },

        beforeDestroy() {

            window.socket.removeListener('App\\Events\\TeamUpdated');
            window.socket.removeListener('App\\Events\\PlayerGameStatsUpdated');
            window.socket.emit('leave-room', 'team.' + this.team_id);

        }

    };
</script>
