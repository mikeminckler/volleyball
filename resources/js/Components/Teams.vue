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

    export default {

        data: function () {
            return {
                teams: []
            }
        },

        mixins: [UserMixins],

        methods: {

            loadTeams: function() {

                this.$http.post('/api/teams').then( response => {
                    this.teams = response.data;
                });
            
            },

            remove: function(e) {

                this.$http.post(e.target.href).then( response => {

                    this.$store.dispatch('addFeedback', {'type': 'success', 'message': 'Team Deleted'});
                    this.loadTeams();

                }, function (error) {
                
                });
            }
        },

        beforeMount() {
            this.loadTeams();
        },

        mounted() {
            
            window.socket.on('App\\Events\\TeamsRefresh', data => {
                this.loadTeams();
            });

        },

        beforeDestroy() {
            window.socket.removeListener('App\\Events\\TeamsRefresh');
        }


    }
</script>
