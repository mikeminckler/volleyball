<template>

    <div class="team-players-stats-report">

        <div class="h3">Players</div>

        <div class="row">
            <div class="column"></div>
            <div class="column" v-for="stat in team.stats">{{ stat.stat_name }}</div>
        </div>

        <transition-group 
            name="list" 
            tag="div"
            v-bind:css="false"
            v-on:before-enter="beforeEnter"
            v-on:enter="enter"
            v-on:leave="leave"
        >

            <div class="row" 
                v-for="(player, index) in players"
                :key="player.id"
                :data-index="index"
            >

                <div class="column">{{ player.full_name }}</div>
                <div class="column player-game-stat" v-for="stat in player.stats">
                    <div class="stat-score">{{ stat.score.score }}</div>
                    <div class="stat-attempts" v-if="stat.score.attempts">({{ stat.score.attempts }})</div>
                </div>

            </div>

        </transition-group>

    </div>

</template>

<script>

    import ListTransition from './ListTransition'

    export default {

        mixins: [ListTransition],

        data: function () {
            return {
                players: {}
            }
        },

        props: ['team', 'game_ids'],

        watch: {
            'game_ids': 'loadTeamStatsReport'
        },

        computed: {
            //
        },

        created () {
            //
        },

        beforeMount () {
            this.loadTeamStatsReport();
        },

        beforeDestroy() {
            window.socket.removeListener('App\\Events\\PlayerGameStatsUpdated');
        },

        mounted () {
            var vue = this;
            window.socket.on('App\\Events\\PlayerGameStatsUpdated', function (data) {
                vue.loadTeamStatsReport();
            });
        },

        methods: {

            loadTeamStatsReport: function() {
                var vue = this;

                let post_data = {
                    'game_ids': this.game_ids
                }
                
                vue.$http.post('/api/teams/players-stats-report/' + this.team.id, post_data).then( function(response) {
                    vue.players = response.data;
                });
            }
        }
    };
</script>
