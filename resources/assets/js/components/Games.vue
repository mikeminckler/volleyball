<template>

    <div class="content">

        <section class="header">
            <div class="h1">Games</div>
            <div v-if="userHasRole('admin')"><router-link class="button" to="/games/create">Create Game</router-link></div>
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
                v-for="(game, index) in games"
                :key="game.id"
                :data-index="index"
            >
                    <div class="column">
                        <router-link :to="{path: '/games/' + game.id}">{{ game.team1.team_name }} vs {{ game.team2.team_name }}</router-link>
                    </div>

                    <div class="column">
                        <router-link :to="{path: '/games/stats/' + game.id}">Stats</router-link>
                    </div>

                    <div class="column">
                        {{ game.score }}
                    </div>

                    <div class="column">
                        {{ displayDateTime(game.start_time) }}
                    </div>

                    <div class="column">
                        <a @click.prevent="remove" class="delete fa fa-times icon" :href="'/api/games/delete/' + game.id"></a>
                    </div>
                </div>
            </transition-group>

        </section>

    </div>

</template>


<script>

    import UserMixins from './UserMixins'
    import ListTransition from './ListTransition'
    import Helpers from './Helpers'

    export default {

        data: function () {
            return {
                games: []
            }
        },
    
        mixins: [UserMixins, ListTransition, Helpers],

        methods: {
                    
            loadGames: function() {

                var vue = this;
                vue.$http.post('/api/games').then( function(response) {
                    vue.games = response.data;
                });
            
            },

            remove: function(e) {

                var vue = this;
                
                vue.$http.post(e.target.href).then( function(response) {

                    vue.$store.dispatch('addFeedback', {'type': 'success', 'message': 'Game Deleted'});

                }, function (error) {
                
                });
            }

        },

        beforeMount() {
            this.loadGames();
        },
        
        mounted() {
            
            var vue = this;

            window.socket.on('App\\Events\\GamesRefresh', function (data) {
                vue.loadGames();
            });

        }
    }

</script>
