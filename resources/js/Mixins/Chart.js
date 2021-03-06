export default {

    methods: {

        drawTeamChart: function(team_id, games, players = undefined) {

            let post_data = {
                'team_id': team_id,
                'games': games,
                'players': players
            }

            this.$http.post('/api/charts/team-games', post_data).then( response => {
                
                var data = google.visualization.arrayToDataTable(response.data.chart);
                var chart = new google.visualization.LineChart(document.getElementById('team_game_chart'));

                var options = {
                    height: 325,
                    title: 'Team Stats',
                    curveType: 'function',
                    legend: { position: 'bottom' },
                    backgroundColor: 'transparent',
                    hAxis: { ticks: response.data.ticks },
                    series: {
                        0: {lineWidth: 4, color: '#000000'}
                    }
                };

                chart.draw(data, options);

            });

       },

        drawPlayerChart: function(player_id, games) {

            let post_data = {
                'players': [player_id],
                'games': games
            }

            this.$http.post('/api/charts/player-games', post_data).then( response => {
                
                var data = google.visualization.arrayToDataTable(response.data.chart);
                var chart = new google.visualization.LineChart(document.getElementById('player_game_chart'));

                var options = {
                    height: 325,
                    title: 'Player Stats',
                    curveType: 'function',
                    legend: { position: 'bottom' },
                    backgroundColor: 'transparent',
                    hAxis: { ticks: response.data.ticks },
                    series: {
                        0: {lineWidth: 4, color: '#000000'}
                    }
                };

                chart.draw(data, options);

            });

        }

    }

}
