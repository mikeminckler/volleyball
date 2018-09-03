export default {
    
    methods: {

        userHasRole(role_array, team_id = undefined) {

            if (team_id == undefined) {
                team_id = this.$store.state.activeTeam.id;
            }

            let roles = this.$lodash.filter(this.$store.state.user.roles, function (r) {
                return r.team.id == team_id;
            });

            let check = this.$lodash.some(roles, r => {
                return this.$lodash.includes(role_array, r.role_name);
            });

            if (!check) {
                check = this.userIsAdmin();
            }

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
        },

        userIsAdmin() {
        
            let check = this.$lodash.some(this.$store.state.user.roles, r => {
                return this.$lodash.includes(['admin'], r.role_name);
            });

            if (check) {
                return true;
            } else {
                return false;
            }

        }

    }

}
