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

        <transition-group 
            name="list" 
            tag="div"
            v-bind:css="false"
            v-on:before-enter="beforeEnter"
            v-on:enter="enter"
            v-on:leave="leave"
        >
            <div class="row game-report-stat" 
                v-for="(stat, index) in stats"
                :key="stat.name"
                :data-index="index"
            >
                <div class="column">{{ stat.name }}</div>
                <div class="column">{{ stat.score.attempts > 0 ? stat.score.score : '-' }}</div>
                <div class="column">{{ stat.score.attempts > 0 ? stat.score.attempts : stat.score.score }}</div>
                <div class="column">{{ stat.highs }}</div>
                <div class="column">{{ stat.lows }}</div>
            </div>
        </transition-group>

    </div>

</template>

<script>


    export default {

        mixins: [],

        data: function () {
            return {
                stats: {}
            }
        },

        props: ['team', 'game_ids', 'playerFilter'],

        watch: {
            'game_ids': 'loadTeamGameReport',
            'playerFilter': 'loadTeamGameReport'
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
            window.socket.on('App\\Events\\PlayerGameStatsUpdated', data => {
                this.loadTeamGameReport();
            });
        },

        methods: {
            loadTeamGameReport: function() {

                let post_data = {
                    'game_ids': this.game_ids,
                    'players': this.playerFilter
                }
                
                this.$http.post('/api/teams/game-report/' + this.team.id, post_data).then( response => {
                    this.stats = response.data;
                });
            }
        }
    };
</script>
