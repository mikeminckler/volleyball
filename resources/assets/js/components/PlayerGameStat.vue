<template>

    <div class="player-game-stat" :class="buttonCountClass"> 
        
        <div class="stat-buttons" v-for="value in buttons" v-if="controls">
            <button class="stat" @click.prevent="takeStat(value)">{{ value }}</button>
        </div>
        <div class="stat-score-container" :class="statClass">
            <div class="stat-score">{{ score.score }}</div>
            <div class="stat-attempts" v-if="score.attempts">({{ score.attempts }})</div>
        </div>

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
                buttons: [],
                changed: ''
            }
        },

        props: ['player', 'team', 'game', 'stat', 'controls'],

        computed: {
            player_updated_at: function() {
                return this.player.updated_at;
            },
            stat_updated_at: function() {
                return this.stat.updated_at;
            },
            statClass: function() {
                return (this.changed ? ' changed' : '');
            },
            buttonCountClass: function() {
                return 'buttons buttons-' + this.buttons.length;
            }
        },

        mounted () {
            this.getScore();
        },

        beforeMount() {
            this.getButtons();
        },

        watch: {
            'player_updated_at': 'updateScore'
        },

        methods: {

            updateScore: function() {

                if (this.player_updated_at == this.stat.updated_at) {
                    this.changed = true;
                    this.getScore();                

                    setTimeout( () => {
                       this.changed = false;
                    }, 250);
                }

            },

            getButtons: function() {

                let score_low = this.$lodash.toFinite(this.stat.pivot.score_low);
                let score_high = this.$lodash.toFinite(this.stat.pivot.score_high);

                if ( this.$lodash.isNumber(score_low) && this.$lodash.isNumber(score_high)) {

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

                let post_data = {
                    'game_id': this.game.id,
                    'team_id': this.team.id,
                    'stat_id': this.stat.id
                }
                
                this.$http.post('/api/players/get-game-stat-score/' + this.player.id, post_data).then( response => {
                    this.score = response.data;
                }, function (error) {
                
                });
            
            },

            takeStat: function(value) {

                let post_data = {
                    'game_id': this.game.id,
                    'stat_id': this.stat.id,
                    'team_id': this.team.id,
                    'score': value
                }

                this.$http.post('/api/players/add-game-stat-score/' + this.player.id, post_data).then( response => {
                    this.score = response.data;
                }, function (error) {
                
                });

            }

        }
    };
</script>
