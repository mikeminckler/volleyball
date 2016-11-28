<template>

    <div class="content">

        <room-list :room="'team.' + team.id"></room-list>

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
                    <label for="initials" class="label">Initials</label>
                </div>
                <div class="form-input">
                    <input id="initials" class="input" name="initials" v-model="team.initials" required autofocus>
                </div>
            </div>

            <div class="form-block">
                <div class="form-label">
                </div>
                <div class="form-input">
                    <button type="submit" class="">Save Team</button>
                </div>
            </div>

            </form>

        </section>

        <section v-if="team.id">
            <div class="h2">Stats Settings</h2>
        </section>

        <section v-if="team.id">

            <div class="form-block">
                <div class="form-label"></div>
                <div class="form-input">High</div>
                <div class="form-input">Low</div>
                <div class="form-input">Target Low</div>
                <div class="form-input">Target Mid</div>
                <div class="form-input">Target High</div>
            </div>

            <transition-group 
                name="form-list" 
                tag="div"
                v-bind:css="false"
                v-on:before-enter="beforeEnter"
                v-on:enter="enter"
                v-on:leave="leave"
            >

                <div class="form-block" v-for="(stat, index) in stats"
                    :key="stat.id"
                    :data-index="index"
                >

                    <div class="form-label">{{ stat.stat_name }}</div>

                    <div class="form-input" v-for="type in statSettings">
                        <team-stat-setting :type="type" :team="team" :stat="stat"></team-stat-setting>
                    </div>

                </div>

            </transition-group>


        </section>


        <section v-show="team.id" v-if="userCanManageTeam(team.id)">
            <div class="h2">Players</h2>
        </section>

        <section v-show="team.id" v-if="userCanManageTeam(team.id)">

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
                        <a @click.prevent="removePlayer" 
                            :data-player-id="player.id" 
                            class="delete fa fa-times icon" 
                            :href="'/api/teams/delete-player/' + team.id"
                        >
                        </a>
                    </div>

                </div>
            </transition-group>

        </section>

        <section v-if="team.id">

            <div class="form-block">
                <div class="form-label">
                    <label for="terms" class="label">Add Players</label>
                </div>
                <div class="form-input">
                    <autocomplete object="users" name="" clear="true" :afterSearching="'addPlayerToTeam(' + team.id + ')'"></autocomplete>
                </div>
            </div>

        </section>

    </div>

</template>

<script>

    import UserMixins from './UserMixins'
    import TeamMixins from './TeamMixins'
    import StatMixins from './StatMixins'
    import ListTransition from './ListTransition'
    import Helpers from './Helpers'

    export default {

        data: function() {
            return {
                statSettings: ['score_high', 'score_low', 'target_high', 'target_mid', 'target_low']
            }
        },

        mixins: [UserMixins, ListTransition, Helpers, TeamMixins, StatMixins],

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

            submit: function(e) {

                var vue = this;

                $('input.input-error').removeClass('input-error');

                let post_data = {
                    'id': this.team.id,
                    'team_name': this.team.team_name,
                    'initials': this.team.initials
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
            var vue = this;
            let team_id = vue.$route.params.id;
            this.loadTeam(team_id);
            this.loadStats();
        },

        mounted() {

            var vue = this;

            let team_id = vue.$route.params.id;
            if (_.toNumber(team_id)) {
                window.socket.emit('join-room', 'team.' + team_id);
            }

            // we should only listen for this teams update events
            window.socket.on('App\\Events\\TeamUpdated', function (data) {
                vue.hideLoading();
                vue.$store.dispatch('addFeedback', {'type': 'success', 'message': data.message});
                vue.loadTeam(team_id);
            });

            //console.log(window.socket);
        },

        beforeDestroy() {

            window.socket.removeListener('App\\Events\\TeamUpdated');

            if (_.toNumber(this.team.id)) {
                window.socket.emit('leave-room', 'team.' + this.team.id);
            }

        }

    }
</script>
