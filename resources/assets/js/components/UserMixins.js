export default {
    
    methods: {

        userHasRole(role_array, team_id = undefined) {

            var vue = this;
            if (team_id == undefined) {
                team_id = vue.$store.state.activeTeam.id;
            }

            let roles = _.filter(vue.$store.state.user.roles, function (r) {
                return r.team.id == team_id;
            });

            let check = _.some(roles, function (r) {
                return _.includes(role_array, r.role_name);
            });

            if (check) {
                return true;
            } else {
                return false;
            }
           
        },

        userCanManageTeam(team_id) {
            return this.userHasRole(['admin', 'coach', 'manager'], team_id);
        },

        userCanTakeStats(team_id) {
            return this.userHasRole(['admin', 'coach', 'manager'], team_id);
        }
    }

}
