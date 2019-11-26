<template>

    <div class="content">

        <section class="header">
            <div class="h1">Games</div>
            <div v-if="userHasRole(['admin'])"><router-link class="button button-icon create" to="/games/create">Create Game</router-link></div>
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
                        <router-link :to="{path: '/games/' + game.id}">{{ game.team1_name }} vs {{ game.team2_name }}</router-link>
                    </div>

                    <div class="column w-100">
                        <router-link :to="{path: '/games/stats/' + game.id}">Stats</router-link>
                    </div>

                    <div class="column">
                        {{ game.score }}
                    </div>

                    <div class="column">
                        {{ displayDateTime(game.start_time) }}
                    </div>

                    <div class="column icon">
                        <a @click.prevent="remove" class="delete fa fa-times icon" :href="'/api/games/delete/' + game.id"></a>
                    </div>
                </div>
            </transition-group>

        </section>

    </div>

</template>


<script>

    import User from '@/Mixins/User'
    import Helpers from '@/Mixins/Helpers'

    export default {

        data: function () {
            return {
                games: []
            }
        },
    
        mixins: [User, Helpers],

        methods: {
                    
            loadGames: function() {

                this.$http.post('/api/games').then( response => {
                    this.games = response.data;
                });
            
            },

            remove: function(e) {

                this.$http.post(e.target.href).then( response => {
                    this.$store.dispatch('addFeedback', {'type': 'success', 'message': 'Game Deleted'});
                }, function (error) {
                
                });
            }

        },

        beforeMount() {
            this.loadGames();
        },
        
        mounted() {
            window.socket.on('App\\Events\\GamesRefresh', data => {
                this.loadGames();
            });
        },

        beforeDestroy() {
            window.socket.removeListener('App\\Events\\GamesRefresh');
        }
    }

</script>
