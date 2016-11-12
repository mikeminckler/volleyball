<template>

    <div class="content">

        <section>
            <div class="h1">{{ team.id ? 'Edit' : 'Create' }} Team {{ team.team_name }}</div>
        </section>

        <section>

            <form role="form" method="POST" :action="'/api' + $route.path" @submit.prevent="submit">

            <div class="form-block">
                <div class="form-label">
                    <label for="team_name" class="label">Team Name</label>
                </div>
                <div class="form-input">
                    <input id="team_name" class="input" name="team_name" v-model="team.team_name" required autofocus>
                </div>
            </div>

            <div class="form-block">
                <div class="form-label">
                </div>
                <div class="form-input">
                    <button type="submit" class="">Save Team</button>
                </div>
            </div>

        </section>


        <div v-show="team.id" v-if="userCanManageTeam(team.id)">

            <section>
                <div class="h2">Players</h2>
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
                        v-for="(player, index) in players"
                        :key="player.id"
                        :data-index="index"
                    >

                        <div class="column">{{ player.user.first_name + ' ' + player.user.last_name }}</div>
                        <div class="column">
                            <a @click.prevent="removePlayer" 
                                :data-player-id="player.id" 
                                class="delete fa fa-times icon" 
                                :href="'/api/teams/delete-player/' + team.id"
                            >
                            </a>
                        </div>

                    </div>
                </transition-group>

                <div class="form-block">
                    <div class="form-label">
                        <label for="terms" class="label">Add Players</label>
                    </div>
                    <div class="form-input">
                        <autocomplete object="users" clear="true" :afterSearching="'addPlayerToTeam(' + team.id + ')'"></autocomplete>
                    </div>
                </div>

            </section>

        </div>

    </div>

</template>

<script>

    import UserMixins from './UserMixins'
    import ListTransition from './ListTransition'
    import Helpers from './Helpers'

    export default {

        data: function () {
            return {
                team: {
                    id: '',
                    team_name: '',
                },
                players: []
            }
        },

        mixins: [UserMixins, ListTransition, Helpers],

        watch: {
            '$route': 'loadInfo'
        },

        methods: {

            loadTeam: function() {

                var vue = this;
                let team_id = vue.$route.params.id;

                if (vue.isNumeric(team_id)) {

                    vue.$http.post('/api/teams/load/' + team_id).then( function(response) {
                        vue.team = response.data;

                        vue.loadPlayers();

                    });

                }
            },

            loadPlayers: function() {

                var vue = this;

                vue.$http.post('/api/teams/players/' + this.team.id).then( function(response) {
                    vue.players = response.data;
                });
            },

            removePlayer: function(e) {

                var vue = this;
            
                let player_id = e.target.dataset.playerId;
                let post_data = {
                    'player_id': player_id
                }
                
                vue.$http.post(e.target.href, post_data).then( function(response) {

                }, function (error) {
                
                });
             
            },

            submit: function(e) {

                var vue = this;

                $('input.input-error').removeClass('input-error');

                let post_data = {
                    'id': this.team.id,
                    'team_name': this.team.team_name
                };

                vue.$http.post(e.target.action, post_data).then( function(response) {

                    vue.$store.dispatch('addFeedback', {'type': 'success', 'message': 'Saved Team'});
                    vue.$router.push('/teams');

                }, function(error) {

                    // this needs to go into a function 
                    if (error.response.status == 422) {
                        for(let input in error.response.data) {

                            // we need to show feedback on the form itself
                            //$("input[name='" + input + "']").addClass('input-error');

                            //document.getElementById(input).classList.add('input-error');
                            $('input[name="' + input + '"]').addClass('input-error');

                            for (let info in error.response.data[input]) {
                                vue.$store.dispatch('addFeedback', {'type': 'error', 'message': error.response.data[input][info], 'input': input});
                            }
                        }
                    }

                    if (error.response.status == 500) {
                        vue.$store.dispatch('addFeedback', {'type': 'error', 'message': 'There was a server error'});
                    }

                });
            },
        },

        beforeMount() {
            this.loadTeam();
        },

        mounted() {

            var vue = this;

            let team_id = vue.$route.params.id;
            if (vue.isNumeric(team_id)) {
                window.socket.emit('join-room', 'team.' + team_id);
            }

            // we should only listen for this teams update events
            window.socket.on('App\\Events\\TeamUpdated', function (data) {
                vue.$store.dispatch('addFeedback', {'type': 'success', 'message': data.message});
                vue.loadTeam();
            });

            //console.log(window.socket);
        },

        beforeDestroy() {

            window.socket.removeListener('App\\Events\\TeamUpdated');

            if (this.isNumeric(this.team.id)) {
                window.socket.emit('leave-room', 'team.' + this.team.id);
            }

        }

    }
</script>
