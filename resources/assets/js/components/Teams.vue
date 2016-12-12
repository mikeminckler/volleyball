<template>

    <div class="content">

        <section class="header">
            <div class="h1">Teams</div>
            <div v-if="userHasRole(['admin'])"><router-link class="button button-icon create" to="/teams/create">Create Team</router-link></div>
        </section>

        <section>

            <transition-group 
                name="list" 
                tag="div"
                v-bind:css="false"
                v-on:before-enter="beforeEnter"
                v-on:enter="enter"
                v-on:leave="leave"
            >
            <div class="row" 
                v-for="(team, index) in teams"
                :key="team.id"
                :data-index="index"
            >
                    <div class="column">
                        <router-link :to="{path: '/teams/' + team.id}">{{ team.team_name }}</router-link>
                    </div>

                    <div class="column">
                        <router-link :to="{path: '/teams/games/' + team.id}">Games</router-link>
                    </div>

                    <div class="column">
                        <router-link :to="{path: '/teams/players/' + team.id}">Players</router-link>
                    </div>

                    <div class="column">
                        <a @click.prevent="remove" class="delete fa fa-times icon" :href="'/api/teams/delete/' + team.id"></a>
                    </div>
                </div>
            </transition-group>

        </section>

    </div>

</template>

<script>

    import UserMixins from './UserMixins'
    import ListTransition from './ListTransition'

    export default {

        data: function () {
            return {
                teams: []
            }
        },

        mixins: [UserMixins, ListTransition],

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

        },

        beforeDestroy() {
            window.socket.removeListener('App\\Events\\TeamsRefresh');
        }


    }
</script>
