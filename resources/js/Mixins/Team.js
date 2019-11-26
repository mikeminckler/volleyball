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

            if (this.$lodash.toNumber(team_id)) {

                this.$http.post('/teams/load/' + team_id).then( response => {
                    this.team = response.data;
                });
            }
        },

        loadActiveTeam: function() {

            this.$http.post('/teams/load/' + this.$store.state.activeTeam.id).then( response => {
                this.$store.dispatch('setActiveTeam', response.data);
            });
        },

        loadPlayers: function(team_id) {

            if (this.$lodash.toNumber(team_id)) {

                this.$http.post('/teams/players/' + team_id).then( response => {
                    this.team.players = response.data;
                });
            }
        },

        loadTeamGames: function(team_id) {
           
            if (this.$lodash.toNumber(team_id)) {

                this.$http.post('/teams/games/' + team_id).then( response => {
                    this.team.games = response.data;
                });
            }
        
        }

    }

}
