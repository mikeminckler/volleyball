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

        <div class="tab-header">
            <div class="tab-button" :class="{ active: tabs.tab1 }" data-tab="tab1" @click="showTab">Summary</div>
            <div class="tab-button" :class="{ active: tabs.tab2 }" data-tab="tab2" @click="showTab">{{ game.team1_name }}</div>
            <div class="tab-button" :class="{ active: tabs.tab3 }" data-tab="tab3" @click="showTab">{{ game.team2_name }}</div>
        </div>

        <div class="tab-content" v-show="tabs.tab1">
            <section>
                Game Summary
            </section>
        </div>

        <div class="tab-content" v-show="tabs.tab2">
            <section>
                <team-game-stats :team_id="game.team1.id" v-if="game.team1.id && tabs.tab2" :game="game"></team-game-stats>
            <section>
            <section>
                <team-game-chart :team_id="game.team1.id" v-if="game.team1.id && tabs.tab2" :game="game"></team-game-chart>
            <section>
        </div>

        <div class="tab-content" v-show="tabs.tab3">
            <section>
                <team-game-stats :team_id="game.team2.id" v-if="game.team2.id && tabs.tab3" :game="game"></team-game-stats>
            <section>
            <section>
                <team-game-chart :team_id="game.team2.id" v-if="game.team2.id && tabs.tab3" :game="game"></team-game-chart>
            <section>
        </div>

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


                var vue = this;
                let active_tab = e.target.dataset.tab;

                _.each(vue.tabs, function(tab, key) { 
                    if (key == active_tab) {
                        vue.tabs[key] = true;
                    } else {
                        vue.tabs[key] = false;
                    }
                });

            },

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
