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
            this.drawTeamChart(this.team.id, [this.game.id]);

            window.socket.on('App\\Events\\PlayerGameStatsUpdated', data => {
                this.drawTeamChart(this.team.id, [this.game.id]);
            });
        },

        beforeDestroy() {
            window.socket.removeListener('App\\Events\\PlayerGameStatsUpdated');
        }

    };

</script>
