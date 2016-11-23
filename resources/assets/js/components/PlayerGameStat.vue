<template>

    <div class="player-game-stat">
        
        <div class="stat-buttons" v-for="value in buttons">
            <button class="stat" @click.prevent="takeStat(value)">{{ value }}</button>
        </div>
        <div class="stat-score">{{ score.score }}</div>
        <div class="stat-attempts" v-if="score.attempts">({{ score.attempts }})</div>

    </div>

</template>

<script>

    import Helpers from './Helpers'

    export default {

        mixins: [Helpers],

        data: function () {
            return {
                score: {
                    score: 0,
                    attempts: 0
                },
                buttons: []
            }
        },

        props: ['player', 'team', 'game', 'stat'],

        computed: {
            updated_at: function() {
                return this.player.updated_at;
            }
        },

        mounted () {
            this.getScore();
        },

        beforeMount() {
            this.getButtons();
        },

        watch: {
            'updated_at': 'getScore'
        },

        methods: {

            getButtons: function() {

                var vue = this;

                let score_low = _.toFinite(this.stat.pivot.score_low);
                let score_high = _.toFinite(this.stat.pivot.score_high);

                if ( _.isNumber(score_low) && _.isNumber(score_high)) {

                    if (score_low == score_high) {

                        this.buttons.push(score_low);

                    } else if (score_low > score_high) {

                        for (let i = score_high; i <= score_low; i++) {
                            this.buttons.push(i);
                        }

                    } else {

                        for (let i = score_high; i >= score_low; i--) {
                            this.buttons.push(i);
                        }

                    }

                }
                
            },

            getScore: function() {

                var vue = this;
                let post_data = {
                    'game_id': this.game.id,
                    'team_id': this.team.id,
                    'stat_id': this.stat.id
                }
                
                vue.$http.post('/api/players/get-game-stat-score/' + this.player.id, post_data).then( function(response) {
                    vue.score = response.data;
                }, function (error) {
                
                });
            
            },

            takeStat: function(value) {

                var vue = this;
                let post_data = {
                    'game_id': this.game.id,
                    'stat_id': this.stat.id,
                    'team_id': this.team.id,
                    'score': value
                }

                vue.$http.post('/api/players/add-game-stat-score/' + this.player.id, post_data).then( function(response) {

                    vue.score = response.data;

                }, function (error) {
                
                });

            }

        }
    };
</script>
