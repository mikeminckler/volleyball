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
            
            <section class="header">
                <div class="h2">Players</div>
            </section>
            <team-players-list :team="team"></team-players-list>

            <section v-if="userCanManageTeam(team.id)">

                <div class="form-block">
                    <div class="form-label">
                        <label for="terms" class="label">Add Players</label>
                    </div>
                    <div class="form-input">
                        <autocomplete object="users" name="" clear="true" :afterSearching="'addPlayerToTeam(' + team.id + ')'"></autocomplete>
                    </div>
                </div>

            </section>

            <section v-else>
                <p>You do not have access to manage this team's players.</p>
            </section>

            <section v-if="userIsAdmin()">

                <div class="h2">Stats Settings</div>

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

            <section v-else>
                <div class="h2">Stats</div>
                <p>You do not have access to manage this team's stats.</p>
            </section>

        </section>

    </div>

</template>

<script>

    import User from '@/Mixins/User'
    import Team from '@/Mixins/Team'
    import Stat from '@/Mixins/Stat'
    import Helpers from '@/Mixins/Helpers'

    export default {

        data: function() {
            return {
                statSettings: ['score_high', 'score_low', 'target_high', 'target_mid', 'target_low']
            }
        },

        mixins: [User, Helpers, Team, Stat],

        methods: {

            submit: function(e) {

                $('input.input-error').removeClass('input-error');

                let post_data = {
                    'id': this.team.id,
                    'team_name': this.team.team_name,
                    'initials': this.team.initials
                };

                this.$http.post(e.target.action, post_data).then( response => {

                    this.$store.dispatch('addFeedback', {'type': 'success', 'message': 'Saved Team'});
                    this.$router.push('/teams');

                }, error => {

                    if (error.response.status == 422) {

                        this.$lodash.each(error.response.data.errors, (errors, field) => {
                            $('input[name="' + field + '"]').addClass('input-error');
                            this.$lodash.each(errors, error => {
                                this.$store.dispatch('addFeedback', {'type': 'error', 'message': error, 'input': field});
                            });
                        });

                    }

                    if (error.response.status == 500) {
                        this.$store.dispatch('addFeedback', {'type': 'error', 'message': 'There was a server error'});
                    }

                });
            },
        },

        beforeMount() {

            let team_id = this.$route.params.id;

            if (team_id == 'manage-team') {
                team_id = this.$store.state.activeTeam.id;
            }

            this.loadTeam(team_id);
            this.loadStats();
        },

        mounted() {

            let team_id = this.$route.params.id;
            if (team_id == 'manage-team') {
                team_id = this.$store.state.activeTeam.id;
            }
            if (this.$lodash.toNumber(team_id)) {
                window.socket.emit('join-room', 'team.' + team_id);
            }

            // we should only listen for this teams update events
            window.socket.on('App\\Events\\TeamUpdated', data => {
                this.hideLoading();
                this.$store.dispatch('addFeedback', {'type': 'success', 'message': data.message});
                this.loadTeam(team_id);
            });

        },

        beforeDestroy() {

            window.socket.removeListener('App\\Events\\TeamUpdated');

            if (this.$lodash.toNumber(this.team.id)) {
                window.socket.emit('leave-room', 'team.' + this.team.id);
            }

        }

    }
</script>
