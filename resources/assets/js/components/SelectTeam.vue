<template>

    <div class="select-team-container">

        <div class="select-team">

            <div class="h3">Select A Team</div>

            <div class="row select-team-item"
                v-for="(team, index) in teams"
                @click="clickTeam(team.id)"
                :id="team.id"
            >
                {{ team.name }}
            </div>

        </div>

    </div>

</template>

<script>

    import TeamMixins from './TeamMixins'

    export default {

        mixins: [TeamMixins],

        data: function () {
            return {
                teams: []
            }
        },

        watch: {
            team() {
                this.setTeam()
            },
            roles() {
                this.showTeams();
            }
        },

        computed: {
            
            roles() {
                return this.$store.state.user.roles;
            }
        
        },

        created() {

            if (window.localStorage.teamId == this.team.id) {
                this.$router.push('/home');
            }

            if (this.roles.length > 0) {
                this.showTeams();
            }

        },

        methods: {

            showTeams: function() {

                this.teams = this.$lodash.uniqBy(
                    this.$lodash.map(this.$store.state.user.roles, function(role) {
                        return {'id': role.team.id, 'name': role.team.team_name};
                    })
                 , 'id');

                if (this.teams.length > 1) {

                    if (window.localStorage.teamId) {
                        this.loadTeam(window.localStorage.teamId);
                    }

                    // wait for click on team

                } else if (this.teams.length == 1) {
                    this.loadTeam(this.$lodash.head(this.teams).id);
                } else {
                    // logout we have no teams
                    this.$store.dispatch('addFeedback', {'type': 'error', 'message': 'You are not assigned to a Team'});
                    this.$router.push('/logout');
                }

            },

            clickTeam: function(id) {
                this.loadTeam(id);
            },

            setTeam: function() {

                this.$store.dispatch('setActiveTeam', this.team);

                // load the home page now that we are logged in
                if (this.$store.state.intended != '/login' && this.$store.state.intended.length > 0) {
                    this.$router.push(this.$store.state.intended);
                    this.$store.state.intended = '';
                } else {
                    this.$router.push('/home');
                }
            }

        }
    };

</script>
