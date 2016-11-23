<template>

    <div class="content">

        <section class="header">
            <div class="h1">{{ team.team_name }} Games</div>
            <div v-if="userHasRole('admin')"><router-link class="button" to="/games/create">Create Game</router-link></div>
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
                        <router-link :to="{path: '/games/' + game.id}">{{ game.team1_name }} vs {{ game.team2_name }}</router-link>
                    </div>

                    <div class="column">
                        <router-link :to="{path: '/games/stats/' + game.id}">Stats</router-link>
                    </div>

                    <div class="column">
                        {{ game.score }}
                    </div>

                    <div class="column">
                        {{ displayDateTime(game.start_time) }}
                    </div>

                    <div class="column">
                        <a @click.prevent="remove" class="delete fa fa-times icon" :href="'/api/games/delete/' + game.id"></a>
                    </div>
                </div>
            </transition-group>

        </section>

        <section>
            <transition name="fade">
                <div v-show="reportGames.length > 0">
                    <button @click.prevent="generateReport">Generate Report</button>
                </div>
            </transition>
        </section>
        
        <section>

            <div id="team_game_chart"></div>

        </section>

       
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
                reportGames: []
            }
        },

        mixins: [Helpers, UserMixins, TeamMixins, ListTransition, ChartMixins],

        methods: {

            generateReport: function() {
            
                this.drawTeamChart(this.team.id, this.reportGames);
            },

            toggleGameSelect: function(e) {
                let val = parseInt(e.target.value);
                
                if (_.includes(this.reportGames, val)) {
                    let index = _.findIndex(this.reportGames, function(o) { return o == val; });
                    this.reportGames.splice(index, 1);
                } else {
                    this.reportGames.push(val);
                }
            },

            gameCheck: function(game_id) {
                return _.includes(this.reportGames, game_id);
            }
        },

        beforeMount() {
            let team_id = this.$route.params.id;
            this.loadTeam(team_id);
        },

        mounted() {
            
            var vue = this;

            window.socket.on('App\\Events\\GamesRefresh', function (data) {
                vue.loadTeamGames();
            });

        },

        beforeDestroy() {
            window.socket.removeListener('App\\Events\\GamesRefresh');
        }

    }

</script>
