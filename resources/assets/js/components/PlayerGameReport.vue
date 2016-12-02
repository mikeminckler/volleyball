<template>
 
    <div class="player-game-report">

        <div class="h3">{{ player.full_name }}  Stats Report {{ game_ids.length > 1 ? 'for ' + game_ids.length + ' games' : '' }}</div>

        <div class="row">
            <div class="column"></div>
            <div class="column" v-for="stat in stats">{{ stat.stat_name }}</div>
        </div>

        <div class="row game-report-stat" v-for="game in games">
            <div class="column">{{ game.versus_team }}</div>
            <div class="column player-game-stat" v-for="stat in game.stats">
                <div class="stat-score">{{ stat.score }}</div>
                <div class="stat-attempts" v-if="stat.attempts">({{ stat.attempts }})</div>
            </div>
        </div>

    </div>

</template>

<script>
    export default {
        components: {},

        mixins: [],

        data: function () {
            return {
                games: []
            }
        },

        props: ['player', 'game_ids'],

        watch: {
            'game_ids': 'loadPlayerGameReport'
        },

        beforeMount () {
            this.loadPlayerGameReport();
        },

        beforeDestroy() {
            window.socket.removeListener('App\\Events\\PlayerGameStatsUpdated');
        },

        mounted () {
            var vue = this;
            window.socket.on('App\\Events\\PlayerGameStatsUpdated', function (data) {
                vue.loadPlayerGameReport();
            });
        },

        methods: {
            loadPlayerGameReport: function() {
                var vue = this;

                let post_data = {
                    'game_ids': this.game_ids
                }
                
                vue.$http.post('/api/players/game-report/' + this.player.id, post_data).then( function(response) {
                    vue.games = response.data;
                });
            }
        }
    };
</script>
