<template>
 
    <div class="team-game-report">

        <div class="h3">Team Stats Report {{ game_ids.length > 1 ? 'for ' + game_ids.length + ' games' : '' }}</div>
        <div class="row game-report-stat">
            <div class="column"></div>
            <div class="column">Average</div>
            <div class="column">Attempts</div>
            <div class="column">Max's</div>
            <div class="column">Min's</div>
        </div>
        <div class="row game-report-stat" v-for="stat in stats">
            <div class="column">{{ stat.name }}</div>
            <div class="column">{{ stat.score.attempts > 0 ? stat.score.score : '-' }}</div>
            <div class="column">{{ stat.score.attempts > 0 ? stat.score.attempts : stat.score.score }}</div>
            <div class="column">{{ stat.highs }}</div>
            <div class="column">{{ stat.lows }}</div>
        </div>

    </div>

</template>

<script>
    export default {
        components: {},

        mixins: [],

        data: function () {
            return {
                stats: {}
            }
        },

        props: ['team', 'game_ids'],

        watch: {
            'game_ids': 'loadTeamGameReport'
        },

        computed: {
            //
        },

        created () {
            //
        },

        beforeMount () {
            this.loadTeamGameReport();
        },

        beforeDestroy() {
            window.socket.removeListener('App\\Events\\PlayerGameStatsUpdated');
        },

        mounted () {
            var vue = this;
            window.socket.on('App\\Events\\PlayerGameStatsUpdated', function (data) {
                vue.loadTeamGameReport();
            });
        },

        methods: {
            loadTeamGameReport: function() {
                var vue = this;

                let post_data = {
                    'game_ids': this.game_ids
                }
                
                vue.$http.post('/api/teams/game-report/' + this.team.id, post_data).then( function(response) {
                    vue.stats = response.data;
                });
            }
        }
    };
</script>
