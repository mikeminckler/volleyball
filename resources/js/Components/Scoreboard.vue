<template>

    <div class="scoreboard">

        <div class="score">
            <div class="score-initials score-left">{{ game.team1.initials }}</div>
            <div class="score-dash"></div>
            <div class="score-initials score-right">{{ game.team2.initials }}</div>
        </div>

        <div class="score score-numbers">

            <div class="score-controls" v-if="controls">
                <button class="remove-point" @click.prevent="removePoint(game.team1.id)">-</button>
                <button class="add-point" @click.prevent="addPoint(game.team1.id)">+</button>
            </div>

            <div class="score-number score-left">{{ currentSet.score.team1.score }}</div>
            <div class="score-dash">-</div>
            <div class="score-number score-right">{{ currentSet.score.team2.score }}</div>

            <div class="score-controls" v-if="controls">
                <button class="add-point" @click.prevent="addPoint(game.team2.id)">+</button>
                <button class="remove-point" @click.prevent="removePoint(game.team2.id)">-</button>
            </div>

        </div>


        <div class="score">
            <div v-for="gameSet in otherSets" class="score-sets">
                {{ gameSet.score.team1.score }}-{{ gameSet.score.team2.score }}
            </div>
        </div>

    </div>

</template>

<script>

    import Game from '@/Mixins/Game'

    export default {

        mixins: [Game],

        data: function () {
            return {
                game: {
                    team1: {
                        id: '',
                        initials: ''
                    },
                    team2: {
                        id: '',
                        initials: ''
                    }
                },
                gameSets: [
                    {
                        score: {
                            team1: {
                                score: 0
                            },
                            team2: {
                                score: 0
                            }
                        }
                    }
                ]
            }
        },

        props: ['game_id', 'controls'],

        computed: {
            currentSet: function() {
                return this.$lodash.last(this.gameSets);
            },
            otherSets: function() {
                if (this.gameSets.length > 1) {
                    return this.$lodash.initial(this.gameSets);
                } else {
                    return [];
                }
            }
        },

        created () {
            //
        },

        methods: {

            loadSets: function() {
                
                this.$http.post('/api/games/sets/' + this.game_id).then( response => {
                    this.gameSets = response.data;
                });

            },

            addPoint: function(team_id) {

                let team = this.$lodash.find(this.currentSet.score, function(t) {
                    return t.id == team_id;
                });
                team.score ++;

                let post_data = {
                    'team_id': team_id
                }

                this.$http.post('/api/games/add-point/' + this.game_id, post_data).then( response => {
                    
                });

            },

            removePoint: function(team_id) {

                let team = this.$lodash.find(this.currentSet.score, function(t) {
                    return t.id == team_id;
                });
                team.score --;

                let post_data = {
                    'team_id': team_id
                }

                this.$http.post('/api/games/remove-point/' + this.game_id, post_data).then( response => {
                
                });

            }



        },

        mounted() {
        
            window.socket.emit('join-room', 'game.' + this.game_id);

            window.socket.on('App\\Events\\GameUpdated', data => {
                this.hideLoading();
                this.$store.dispatch('addFeedback', {'type': 'success', 'message': data.message});
                this.loadGame(this.game_id);
                this.loadSets();
            });

        },

        beforeDestroy() {

            window.socket.removeListener('App\\Events\\GameUpdated');
            window.socket.emit('leave-room', 'game.' + this.game_id);

        },

        beforeMount () {
            this.loadGame(this.game_id);
            this.loadSets();
        },

    };
</script>
