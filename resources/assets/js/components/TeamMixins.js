import Helpers from './Helpers'

export default {

    data: function () {
        return {
            team: {
                id: '',
                team_name: '',
                players: {}
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

        loadPlayers: function(team_id) {

            var vue = this;
            if (_.toNumber(team_id)) {

                vue.$http.post('/api/teams/players/' + team_id).then( function(response) {
                    vue.team.players = response.data;
                });
            }
        },

    }

}
