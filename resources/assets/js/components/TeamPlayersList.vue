<template>

    <div class="team-players-list">

            <transition-group 
                name="list" 
                tag="div"
                v-bind:css="false"
                v-on:before-enter="beforeEnter"
                v-on:enter="enter"
                v-on:leave="leave"
            >
                <div class="row" 
                    v-for="(player, index) in team.players"
                    :key="player.id"
                    :data-index="index"
                >

                    <div class="column">{{ player.full_name }}</div>
                    <div class="column">
                        <router-link :to="{path: '/players/stats/' + player.id}">Stats</router-link>
                    </div>
                    <div class="column" v-if="userCanManageTeam(team.id)">
                        <a @click.prevent="removePlayer" 
                            :data-player-id="player.id" 
                            class="delete fa fa-times icon" 
                            :href="'/api/teams/delete-player/' + team.id"
                        >
                        </a>
                    </div>

                </div>
            </transition-group>

    </div>
</template>

<script>

    import ListTransition from './ListTransition'
    import Helpers from './Helpers'
    import UserMixins from './UserMixins'

    export default {
        components: {},

        mixins: [ListTransition, Helpers, UserMixins],

        data: function () {
            return {
                //
            }
        },

        props: ['team'],

        computed: {
            //
        },

        created () {
            //
        },

        mounted () {

        },

        beforeDestroy() {

        },

        methods: {
            
            removePlayer: function(e) {

                var vue = this;
                vue.showLoading();
            
                let player_id = e.target.dataset.playerId;
                let post_data = {
                    'player_id': player_id
                }
                
                vue.$http.post(e.target.href, post_data).then( function(response) {

                }, function (error) {
                
                });
             
            },
        }
    };
</script>
