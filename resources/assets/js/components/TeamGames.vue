<template>

        <section class="header">
            <div class="h1">{{ team.team_name }} Games</div>
            <div v-if="userHasRole('admin')"><router-link class="button" to="/games/create">Create Game</router-link></div>
        </section>

</template>

<script>

    import Helpers from './Helpers'
    import UserMixins from './UserMixins'

    export default {

        data: function () {
            return {
                team: []
            }
        },

        mixins: [Helpers, UserMixins],


        methods: {

            loadTeam: function() {

                var vue = this;
                let team_id = vue.$route.params.id;

                if (vue.isNumeric(team_id)) {

                    vue.$http.post('/api/teams/load/' + team_id).then( function(response) {
                        vue.team = response.data;
                    });

                }
            },

        },

        beforeMount() {
            this.loadTeam();
        },

    }

</script>
