<template>

    <div class="content">

        <room-list :room="'game.' + game.id"></room-list>

        <section>
            <div class="stats-header">
                <div class="h3 stats-header-item">{{ game.team1.team_name }}  vs {{ game.team2.team_name }}</div>
                <div class="stats-header-item">
                    <scoreboard :game_id="game.id" controls="true"></scoreboard>
                </div>   
                <div class="stats-header-item stats-header-right">
                    <button class="add-set" @click.prevent="addSet">New Set</button>
                </div>
        </section>

        <section>

            <team-game-stats :team_id="game.team1.id"></team-game-stats>

        </section>


    </div>
</template>

<script>

    import GameMixins from './GameMixins'

    export default {

        mixins: [GameMixins],

        data: function () {
            return {
                game: {
                    id: this.$route.params.id,
                    team1: {
                        id: '',
                        team_name: '',
                        initials: ''
                    },
                    team2: {
                        id: '',
                        team_name: '',
                        initials: ''
                    },
                    start_time: ''
                }
            }
        },

        props: {
            //
        },

        methods: {
            addSet: function() {

                var vue = this;

                vue.$http.post('/api/games/add-set/' + this.game.id).then( function(response) {
                    
                });
                
            }
        },

        created () {
            //
        },

        mounted () {
            //
        },

        beforeMount() {
            this.loadGame(this.$route.params.id);
        },

    };
</script>
