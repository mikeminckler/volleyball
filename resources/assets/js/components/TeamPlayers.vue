<template>

    <div class="component">

        <section class="header">
            <div class="h1">{{ team.team_name }} Players</div>
            <div v-if="userCanManageTeam(team.id)">
                <router-link class="button button-icon create" :to="{path: '/teams/' + team.id}">Manage Players</router-link>
            </div>
        </section>

        <section>
            <div class="team-players">
                <team-players-list :team="team" v-if="team"></team-players-list>
            </div>
        </section>

    </div>

</template>

<script>

    import TeamMixins from './TeamMixins'
    import UserMixins from './UserMixins'

    export default {

        mixins: [TeamMixins, UserMixins],

        beforeMount() {
            let team_id = this.$route.params.id;
            this.loadTeam(team_id);
        },

    };

</script>
