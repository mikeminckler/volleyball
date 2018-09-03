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
            </div>
        </section>

        <div class="tab-header">
            <div class="tab-button" :class="{ active: tabs.tab1 }" data-tab="tab1" @click="showTab">Summary</div>
            <div class="tab-button" :class="{ active: tabs.tab2 }" data-tab="tab2" @click="showTab">{{ game.team1_name }}</div>
            <div class="tab-button" :class="{ active: tabs.tab3 }" data-tab="tab3" @click="showTab">{{ game.team2_name }}</div>
        </div>

        <div class="tab-content" v-show="tabs.tab1">
            <section>
                <div class="game-summary">

                    <div class="game-summary-team">

                        <div class="h3">{{ game.team1.team_name }}</div>
                        <section v-if="game.team1.id">
                            <team-players-stats-report :team="game.team1" :game_ids="[game.id]"></team-players-stats-report>
                        </section>

                    </div>

                    <div class="game-summary-team">

                        <div class="h3">{{ game.team2.team_name }}</div>
                        <section v-if="game.team2.id">
                            <team-players-stats-report :team="game.team2" :game_ids="[game.id]"></team-players-stats-report>
                        </section>

                    </div>

                </div>
            </section>
        </div>

        <div class="tab-content" v-show="tabs.tab2">
            <section>
                <team-game-stats :team_id="game.team1.id" v-if="game.team1.id && tabs.tab2" :game="game" :controls="userCanTakeStats(game.team1.id)"></team-game-stats>
            </section>
            <section>
                <team-game-chart :team="game.team1" v-if="game.team1.id && tabs.tab2" :game="game"></team-game-chart>
            </section>
            <section>
                <team-game-report :team="game.team1" v-if="game.team1.id && tabs.tab2" :game_ids="[game.id]"></team-game-report>
            </section>
        </div>

        <div class="tab-content" v-show="tabs.tab3">
            <section>
                <team-game-stats :team_id="game.team2.id" v-if="game.team2.id && tabs.tab3" :game="game" :controls="userCanTakeStats(game.team2.id)"></team-game-stats>
            </section>
            <section>
                <team-game-chart :team="game.team2" v-if="game.team2.id && tabs.tab3" :game="game"></team-game-chart>
            </section>
            <section>
                <team-game-report :team="game.team2" v-if="game.team2.id && tabs.tab3" :game_ids="[game.id]"></team-game-report>
            </section>
        </div>

    </div>
</template>

<script>

    import GameMixins from './GameMixins'
    import UserMixins from './UserMixins'

    export default {

        mixins: [GameMixins, UserMixins],

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
                },
                tabs: {
                    tab1: true,
                    tab2: false,
                    tab3: false
                }
            }
        },

        props: {
            //
        },

        methods: {

            showTab: function(e) {

                let active_tab = e.target.dataset.tab;

                this.$lodash.each(this.tabs, (tab, key) => { 
                    if (key == active_tab) {
                        this.tabs[key] = true;
                    } else {
                        this.tabs[key] = false;
                    }
                });

            },

            addSet: function() {

                this.$http.post('/api/games/add-set/' + this.game.id).then( response => {
                    this.$store.dispatch('addFeedback', {'type': 'success', 'message': 'Added New Set'});
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
