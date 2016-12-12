<template>

    <div class="team-games-list">

        <section class="header">
            <div class="h1">{{ team.team_name }} Games</div>
            <div v-if="userHasRole(['admin', 'coach', 'team_manager'])"><router-link class="button button-icon create" to="/games/create">Create Game</router-link></div>
        </section>

        <section v-if="team.games">

            <transition-group 
                name="list" 
                tag="div"
                v-bind:css="false"
                v-on:before-enter="beforeEnter"
                v-on:enter="enter"
                v-on:leave="leave"
            >
                <div class="row" 
                    v-for="(game, index) in team.games"
                    :key="game.id"
                    :data-index="index"
                >
                    <div class="column shrink">
                        <input type="checkbox" :id="game.id" :name="'game-' + game.id" :value="game.id" @click="toggleGameSelect" :checked="gameCheck(game.id)">
                    </div>
                    <div class="column">
                        <router-link v-if="userCanManageTeam(team.id)" :to="{path: '/games/' + game.id}">{{ game.team1_name }} vs {{ game.team2_name }}</router-link>
                        <div v-else>{{ game.team1_name }} vs {{ game.team2_name }}</div>
                    </div>

                    <div class="column" v-if="userCanTakeStats(team.id)">
                        <router-link :to="{path: '/games/stats/' + game.id}" class="button button-icon game-center">Game Center</router-link>
                    </div>

                    <div class="column">
                        {{ game.score }}
                    </div>

                    <div class="column">
                        {{ displayDateTime(game.start_time) }}
                    </div>

                    <div class="column icon">
                        <a @click.prevent="remove" class="delete fa fa-times icon" :href="'/api/games/delete/' + game.id"></a>
                    </div>
                </div>
            </transition-group>

        </section>

        <section>
            <div class="button player-filter" @click="togglePlayers">{{ showPlayers ? 'Hide' : 'Filter' }} Players</div>

            <div class="player-filter">

                <transition-group
                    name="list" 
                    tag="div"
                    v-bind:css="false"
                    v-on:before-enter="beforeEnter"
                    v-on:enter="enter"
                    v-on:leave="leave"
                >

                    <div class="players-list row"
                        v-for="(player, index) in team.players"
                        :key="player.id"
                        :data-index="index"
                        v-if="showPlayers"
                    >

                        <div class="column shrink">
                            <input type="checkbox" :id="player.id" :name="'player-' + player.id" :value="player.id" @click="togglePlayerFilter" :checked="playerCheck(player.id)">
                        </div>
                        <div class="column">{{ player.full_name }}</div>
                        <div class="column">
                            <router-link :to="{path: '/players/stats/' + player.id}">Stats</router-link>
                        </div>

                    </div>

                </transition-group>

            </div>
        </section>


        <transition name="fade">
            <div class="game-summary">

                <div class="game-summary-team">
                    <section v-if="showReport">
                        <team-game-report :team="team" :game_ids="reportGames" :playerFilter="playerFilter"></team-game-chart>
                    <section>
                </div>

                <div class="game-summary-team">
                    <section v-if="showReport">
                        <team-players-stats-report :team="team" :game_ids="reportGames" :playerFilter="playerFilter"></team-players-stats-report>
                    </section>
                </div>

            </div>
        </transition>

        <transition name="fade">
            <section v-if="showReport">
                <div id="team_game_chart"></div>
            </section>
        </transition>

    </div>

</template>

<script>

    import Helpers from './Helpers'
    import UserMixins from './UserMixins'
    import TeamMixins from './TeamMixins'
    import ListTransition from './ListTransition'
    import ChartMixins from './ChartMixins'

    export default {

        data: function () {
            return {
                reportGames: [],
                playerFilter: [],
                showReport: false,
                showPlayers: false
            }
        },

        mixins: [Helpers, UserMixins, TeamMixins, ListTransition, ChartMixins],

        props: ['teamId'],

        methods: {

            togglePlayers: function() {
                this.showPlayers = !this.showPlayers;
                if (!this.showPlayer) {
                    this.playerFilter = [];
                    if (this.reportGames.length > 0) {
                        this.generateReport();
                    } else {
                        this.showReport = false;
                    }
                }
            },

            generateReport: function() {
            
                this.showReport = true;
                this.drawTeamChart(this.team.id, this.reportGames, this.playerFilter);
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
            },

            playerCheck: function(player_id) {
                return _.includes(this.playerFilter, player_id);
            },

            togglePlayerFilter: function(e) {

                let val = parseInt(e.target.value);
                
                if (_.includes(this.playerFilter, val)) {
                    let index = _.findIndex(this.playerFilter, function(o) { return o == val; });
                    this.playerFilter.splice(index, 1);
                } else {
                    this.playerFilter.push(val);
                }

                if (this.reportGames.length > 0) {
                    this.generateReport();
                } else {
                    this.showReport = false;
                }

            },
        },

        mounted() {
            
            var vue = this;

            this.loadTeam(this.teamId);
            
            window.socket.on('App\\Events\\GamesRefresh', function (data) {
                vue.loadTeamGames(vue.team.id);
            });

            window.socket.on('App\\Events\\PlayerGameStatsUpdated', function (data) {
                vue.generateReport();
            });

        },

        beforeDestroy() {
            window.socket.removeListener('App\\Events\\GamesRefresh');
            window.socket.removeListener('App\\Events\\PlayerGameStatsUpdated');
        }

    }

</script>
