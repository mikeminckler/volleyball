<template>

    <div class="content">

        <section class="header">
            <div class="h1">{{ player.full_name }} Games</div>
        </section>

        <section v-if="player.games">

            <transition-group 
                name="list" 
                tag="div"
                v-bind:css="false"
                v-on:before-enter="beforeEnter"
                v-on:enter="enter"
                v-on:leave="leave"
            >
                <div class="row" 
                    v-for="(game, index) in player.games"
                    :key="game.id"
                    :data-index="index"
                >
                    <div class="column shrink">
                        <input type="checkbox" :id="game.id" :name="'game-' + game.id" :value="game.id" @click="toggleGameSelect" :checked="gameCheck(game.id)">
                    </div>
                    <div class="column">
                        {{ game.team1_name }} vs {{ game.team2_name }}
                    </div>

                    <div class="column">
                        {{ game.score }}
                    </div>

                    <div class="column">
                        {{ displayDateTime(game.start_time) }}
                    </div>

                </div>

            </transition-group>

        </section>

        <transition name="fade">
            <section v-if="showReport">
                <div id="player_game_chart"></div>
            </section>
        </transition>

        <transition name="fade">
            <section v-if="showReport">
                <player-game-report :player="player" :game_ids="reportGames"></player-game-report>
            <section>
        </transition>

    </div>

</template>

<script>


    import ListTransition from './ListTransition'
    import PlayerMixins from './PlayerMixins'
    import ChartMixins from './ChartMixins'
    import Helpers from './Helpers'

    export default {

        mixins: [ListTransition, PlayerMixins, ChartMixins, Helpers],

        data: function () {
            return {
                reportGames: [],
                showReport: false,
                player: {}
            }
        },

        beforeMount() {
            let player_id = this.$route.params.id;
            this.loadPlayer(player_id);
        },

        mounted() {
            
            let player_id = this.$route.params.id;
            var vue = this;
            vue.loadPlayerGames(player_id);

            window.socket.on('App\\Events\\GamesRefresh', function (data) {
                vue.loadPlayerGames(player_id);
            });

            window.socket.on('App\\Events\\PlayerGameStatsUpdated', function (data) {
                vue.generateReport();
            });

        },

        beforeDestroy() {
            window.socket.removeListener('App\\Events\\GamesRefresh');
            window.socket.removeListener('App\\Events\\PlayerGameStatsUpdated');
        },

        methods: {

            generateReport: function() {
                this.showReport = true;
                this.drawPlayerChart(this.player.id, this.reportGames);
            },

            toggleGameSelect: function(e) {
                let val = parseInt(e.target.value);
                
                if (_.includes(this.reportGames, val)) {
                    let index = _.findIndex(this.reportGames, function(o) { return o == val; });
                    this.reportGames.splice(index, 1);
                    window.socket.emit('leave-room', 'game.' + val);
                } else {
                    this.reportGames.push(val);
                    window.socket.emit('join-room', 'game.' + val);
                }

                if (this.reportGames.length > 0) {
                    this.generateReport();
                } else {
                    this.showReport = false;
                }
            },

            gameCheck: function(game_id) {
                return _.includes(this.reportGames, game_id);
            }
        }

    };
</script>
