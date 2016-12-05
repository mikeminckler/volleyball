export default {
    
    methods: {
        userHasRole(role) {

            let check = _.some(this.$store.state.user.roles, function (r) {
                return r.role_name == role;
            });

            if (check) {
                return true;
            } else {
                return false;
            }
           
        },

        userCanManageTeam(team_id) {
            return true;
        },

        userCanTakeStats(team_id) {
            return true;
        }
    }

}
