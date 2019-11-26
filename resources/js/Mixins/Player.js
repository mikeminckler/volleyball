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

            if (this.$lodash.toNumber(player_id)) {

                this.$http.post('/api/players/load/' + player_id).then( response => {
                    this.player = response.data;
                });
            }
        },

        loadPlayerTeams: function(player_id) {

            if (this.$lodash.toNumber(player_id)) {

                this.$http.post('/api/players/teams/' + player_id).then( response => {
                    this.player.teams = response.data;
                });
            }
        },

        loadPlayerGames: function(player_id) {
           
            if (this.$lodash.toNumber(player_id)) {

                this.$http.post('/api/players/games/' + player_id).then( response => {
                    this.player.games = response.data;
                });
            }
        
        }

    }

}
