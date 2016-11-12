export default {
    
    methods: {
        userHasRole(role) {
            
            if (_.includes(this.$store.state.user.roles, role)) {
                return true;
            } else {
                return false;
            }
           
        },

        userCanManageTeam(team_id) {
            return true;
        }
    }

}
