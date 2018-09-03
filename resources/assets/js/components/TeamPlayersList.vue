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
                    <div @click.prevent="removePlayer(player.id, team.id)">
                        <i class="fas fa-times"></i>
                    </div>
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
            
            removePlayer: function(playerId, teamId) {

                this.showLoading();
            
                let post_data = {
                    'player_id': playerId
                }
                
                this.$http.post('/api/teams/delete-player/' + teamId, post_data).then( response => {

                    console.log(response);

                }, function (error) {
                
                });
             
            },
        }
    };
</script>
