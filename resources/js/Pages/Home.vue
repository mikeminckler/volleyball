<template>

    <div class="content">

        <team-games-list :teamId="team.id" v-if="team.id"></team-games-list>
    
    </div>

</template>

<script>

    import Helpers from '@/Mixins/Helpers'
    import Team from '@/Mixins/Team'
    import Layout from '@/Layout';

    export default {

        layout: Layout,
        mixins: [Helpers, Team],

        components: {
            'team-games-list': () => import(/* webpackChunkName: "team-games-list" */ '@/Components/TeamGamesList'),
        },

        methods: {
        
        },

        mounted() {

            this.team = this.$store.state.activeTeam;

            this.$echo.channel('public')
                .listen('GamesRefresh', data => {
                    this.loadActiveTeam();
                });

            this.$once('hook:destroyed', () => {
                this.$echo.leave('public');
            });

        },

    }
</script>
