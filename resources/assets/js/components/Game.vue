<template>

    <div class="content">

        <section>
            <div class="h1">{{ game.id ? 'Edit' : 'Create' }} Game</div>
        </section>

        <section>

            <form role="form" method="POST" :action="'/api' + $route.path" @submit.prevent.stop="">

            <div class="form-block">
                <div class="form-label">
                    <label for="team1_id" class="label">Team 1 (H)</label>
                </div>
                <div class="form-input">
                    <autocomplete object="teams" name="team1_id" afterSearching="" 
                        :oldid="game.team1.id ? game.team1.id : $store.state.activeTeam.id" 
                        :text="game.team1.id ? game.team1.team_name : $store.state.activeTeam.team_name"
                    >
                    </autocomplete>
                </div>
            </div>

            <div class="form-block">
                <div class="form-label">
                    <label for="team2_id" class="label">Team 2 (A)</label>
                </div>
                <div class="form-input">
                    <autocomplete object="teams" name="team2_id" afterSearching="" 
                        :oldid="game.team2.id" 
                        :text="game.team2.team_name"
                        canAdd="addTeam"
                    >
                    </autocomplete>
                </div>
            </div>

            <div class="form-block">
                <div class="form-label">
                    <label for="start_time" class="label">Start Date & Time</label>
                </div>
                <div class="form-input">
                    <input type="text" class="datetimepicker" placeholder="" name="start_time" id="start_time" v-model="game.start_time" />
                </div>
            </div>


            <div class="form-block">
                <div class="form-label">
                </div>
                <div class="form-input">
                    <div class="submit button" @click="submit">Save Game</div>
                </div>
            </div>

            </form>

        </section>

    </div>

</template>

<script>

    import UserMixins from './UserMixins'
    import GameMixins from './GameMixins'
    import ListTransition from './ListTransition'
    import Helpers from './Helpers'

    export default {

        data: function () {
            let date = new Date();
            return {
                game: {
                    team1: {
                        id: ''
                    },
                    team2: {
                        id: ''
                    },
                    start_time: date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate() + ' ' + date.getHours() + ':' + ("0" + date.getMinutes()).substr(-2)
                }
            }
        },

        mixins: [UserMixins, ListTransition, Helpers, GameMixins],

        methods: {

            submit: function(e) {

                $('input.input-error').removeClass('input-error');

                let post_data = {
                    'team1_id': document.getElementById("team1_id").value,
                    'team2_id': document.getElementById("team2_id").value,
                    'start_time': this.game.start_time
                };

                this.$http.post('/api' + this.$route.path, post_data).then( response => {

                    this.$store.dispatch('addFeedback', {'type': 'success', 'message': 'Saved Game'});
                    this.$router.push('/games');

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
            this.loadGame(this.$route.params.id);
        },


    }
</script>
