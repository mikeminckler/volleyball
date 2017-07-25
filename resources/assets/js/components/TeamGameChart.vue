<template>

    <div class="team-game-chart">

        <div id="team_game_chart"></div>

    </div>

</template>

<script>

    import ChartMixins from './ChartMixins'

    export default {

        mixins: [ChartMixins],

        props: ['team', 'game'],

        mounted () {

            var vue = this;

            vue.drawTeamChart(this.team.id, [this.game.id]);

            window.socket.on('App\\Events\\PlayerGameStatsUpdated', function (data) {
                vue.drawTeamChart(vue.team.id, [vue.game.id]);
            });

        },

        beforeDestroy() {
            window.socket.removeListener('App\\Events\\PlayerGameStatsUpdated');
        }

    };

</script>
