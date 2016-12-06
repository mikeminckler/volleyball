import Helpers from './Helpers'

export default {

    data: function () {
        return {
            team: {
                id: '',
                team_name: '',
                initials: '',
                players: [],
                games: []
            },
        }
    },

    mixins: [Helpers],

    methods: {

        loadTeam: function(team_id) {

            var vue = this;

            if (_.toNumber(team_id)) {

                vue.$http.post('/api/teams/load/' + team_id).then( function(response) {
                    vue.team = response.data;
                });
            }
        },

        loadActiveTeam: function() {

            var vue = this;

            vue.$http.post('/api/teams/load/' + vue.$store.state.activeTeam.id).then( function(response) {
                vue.$store.dispatch('setActiveTeam', response.data);
            });
        },

        loadPlayers: function(team_id) {

            var vue = this;
            if (_.toNumber(team_id)) {

                vue.$http.post('/api/teams/players/' + team_id).then( function(response) {
                    vue.team.players = response.data;
                });
            }
        },

        loadTeamGames: function(team_id) {
            var vue = this;
            if (_.toNumber(team_id)) {

                vue.$http.post('/api/teams/games/' + team_id).then( function(response) {
                    vue.team.games = response.data;
                });
            }
        
        }

    }

}
