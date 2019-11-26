<template>

    <div class="team-game-chart">

        <div id="team_game_chart"></div>

    </div>

</template>

<script>

    import Chart from '@/Mixins/Chart'

    export default {

        mixins: [Chart],

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
