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

    </div>

</template>

<script>

    import UserMixins from './UserMixins'

    export default {

        data: function () {
            return {
                team: {
                    id: '',
                    team_name: '',
                }
            }
        },

        mixins: [UserMixins],

        watch: {
            '$route': 'loadInfo'
        },

        methods: {

            loadTeam: function() {

                var vue = this;
                let team_id = vue.$route.params.id;

                if (team_id != 'create') {

                    vue.$http.post('/api/teams/load/' + team_id).then( function(response) {
                        vue.team = response.data;
                    });

                }
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
        }

    }
</script>
