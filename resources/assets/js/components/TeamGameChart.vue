<template>

    <div class="team-game-chart">

        <div id="team_game_chart"></div>

    </div>

</template>

<script>

    export default {

        mixins: [],

        data: function () {
            return {
                //
            }
        },

        props: ['team_id', 'game'],

        computed: {
            //
        },

        created () {
            //
        },

        mounted () {

            var vue = this;

            vue.drawChart();

            window.socket.on('App\\Events\\TeamGameChartUpdated', function (data) {
                vue.drawChart();
            });

        },

        beforeDestroy() {
        
            window.socket.removeListener('App\\Events\\TeamGameChartUpdated');
        },

        methods: {
            drawChart: function() {


                var vue = this;
                let post_data = {
                    'team_id': this.team_id,
                    'games': [this.game.id]
                }

                vue.$http.post('/api/charts/team-games', post_data).then( function(response) {
                    
                    var data = google.visualization.arrayToDataTable(response.data);
                    var chart = new google.visualization.LineChart(document.getElementById('team_game_chart'));

                    var options = {
                        title: 'Team Stats',
                        curveType: 'function',
                        legend: { position: 'bottom' },
                        backgroundColor: 'transparent'
                    };

                    chart.draw(data, options);

                });

            }
        }
    };
</script>
