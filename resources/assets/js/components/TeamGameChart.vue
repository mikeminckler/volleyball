<template>

    <div class="team-game-chart">

        <div id="team_game_chart"></div>

    </div>

</template>

<script>

    import ChartMixins from './ChartMixins'

    export default {

        mixins: [ChartMixins],

        props: ['team_id', 'game'],

        mounted () {

            var vue = this;

            vue.drawTeamChart(this.team_id, [this.game.id]);

            window.socket.on('App\\Events\\TeamGameChartUpdated', function (data) {
                vue.drawTeamChart(vue.team.id, [vue.game.id]);
            });

        },

        beforeDestroy() {
            window.socket.removeListener('App\\Events\\TeamGameChartUpdated');
        }

    };

</script>
