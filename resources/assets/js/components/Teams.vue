<template>

    <div class="content">

        <section class="header">
            <div class="h1">Teams</div>
            <div v-if="userHasRole('admin')"><router-link class="button" to="/teams/create">Create Team</router-link></div>
        </section>

        <section>

            <div class="row" v-for="team in teams">
                <div class="column">
                    <router-link :to="{path: '/teams/' + team.id}">{{ team.team_name }}</router-link>
                </div>
                <div class="column">
                    <a @click.prevent="remove" class="delete fa fa-times icon" :href="'/api/teams/delete/' + team.id"></a>
                </div>
            </div>

        </section>

    </div>

</template>

<script>

    import UserMixins from './UserMixins'

    export default {

        data: function () {
            return {
                teams: []
            }
        },

        mixins: [UserMixins],

        methods: {

            loadTeams: function() {

                var vue = this;
                vue.$http.post('/api/teams').then( function(response) {
                    vue.teams = response.data;
                });
            
            },

            remove: function(e) {

                var vue = this;
                
                vue.$http.post(e.target.href).then( function(response) {

                    vue.$store.dispatch('addFeedback', {'type': 'success', 'message': 'Team Deleted'});

                    vue.loadTeams();

                }, function (error) {
                
                });
            }
        },

        beforeMount() {
            this.loadTeams();
        },

        mounted() {
            
            var vue = this;

            window.socket.on('App\\Events\\TeamsRefresh', function (data) {
                vue.loadTeams();
            });

        }


    }
</script>
