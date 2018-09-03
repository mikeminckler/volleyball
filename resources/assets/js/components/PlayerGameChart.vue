<template>

    <div class="player-game-chart">

        <div id="player_game_chart"></div>

    </div>

</template>

<script>

    import ChartMixins from './ChartMixins'

    export default {

        mixins: [ChartMixins],

        props: ['player', 'game'],

        mounted () {

            this.drawPlayerChart(this.player.id, [this.game.id]);

            window.socket.on('App\\Events\\PlayerGameStatsUpdated', data => {
                this.drawPlayerChart(this.player.id, [this.game.id]);
            });

        },

        beforeDestroy() {
            window.socket.removeListener('App\\Events\\PlayerGameStatsUpdated');
        }

    };
</script>
