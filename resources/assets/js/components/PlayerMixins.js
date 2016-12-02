export default {

    data: function () {
        return {
            player: {
                id: '',
                full_name: '',
                teams: [],
                games: []
            },
        }
    },

    mixins: [],

    methods: {

        loadPlayer: function(player_id) {

            var vue = this;

            if (_.toNumber(player_id)) {

                vue.$http.post('/api/players/load/' + player_id).then( function(response) {
                    vue.player = response.data;
                });
            }
        },

        loadPlayerTeams: function(player_id) {

            var vue = this;
            if (_.toNumber(player_id)) {

                vue.$http.post('/api/players/teams/' + player_id).then( function(response) {
                    vue.player.teams = response.data;
                });
            }
        },

        loadPlayerGames: function(player_id) {
            var vue = this;
            if (_.toNumber(player_id)) {

                vue.$http.post('/api/players/games/' + player_id).then( function(response) {
                    vue.player.games = response.data;
                });
            }
        
        }

    }

}
