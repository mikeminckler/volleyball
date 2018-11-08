<template>

    <div class="court">

        <div class="score-title">Court {{ court.id }}</div>

        <div class="score score-numbers">

            <div class="score-controls">
                <button class="remove" @click.prevent="removePoint(court.team1.id)">-</button>
                <button class="add" @click.prevent="addPoint(court.team1.id)">+</button>
            </div>

            <div class="scoreboard-info">
                <div><input v-model="court.team1.name" class="scoreboard-team-name scoreboard-team-name-left" @change="setTeamName(court.team1.id)"></div>

                <div class="score-number score-left">{{ court.team1.points }}</div>
                <div class="score-dash"></div>
                <div class="score-number score-right">{{ court.team2.points }}</div>

                <div><input v-model="court.team2.name" class="scoreboard-team-name coreboard-team-name-right" @change="setTeamName(court.team2.id)"></div>
            </div>

            <div class="score-controls">
                <button class="add" @click.prevent="addPoint(court.team2.id)">+</button>
                <button class="remove" @click.prevent="removePoint(court.team2.id)">-</button>
            </div>

        </div>

        <div class="score score-sets">

            <div class="score-controls">
                <button class="remove" @click.prevent="removeSet(court.team1.id)">-</button>
                <button class="add" @click.prevent="addSet(court.team1.id)">+</button>
            </div>

            <div class="scoreboard-info sets">
                <div class="score-set score-left">{{ court.team1.sets }}</div>
                <div class="score-dash lower">-</div>
                <div class="score-set score-right">{{ court.team2.sets }}</div>
            </div>

            <div class="score-controls">
                <button class="add" @click.prevent="addSet(court.team2.id)">+</button>
                <button class="remove" @click.prevent="removeSet(court.team2.id)">-</button>
            </div>

        </div>

        <div class="scoreboard-reset">
            <div class="button scoreboard-reset-button" @click="resetScores()">Reset</div>
        </div>

    </div>

</template>

<script>

    export default {

        data: function () {
            return {
                court: {
                    id: 0,
                    team1: {
                        id: 1,
                        points: 0,
                        sets: 0,
                        name: '',
                    },
                    team2: {
                        id: 2,
                        points: 0,
                        sets: 0,
                        name: '',
                    },
                }
            }
        },

        props: ['id'],

        methods: {


            loadCourt: function() {
            
                let post_data = {
                    'court_id': this.id
                }

                this.$http.post('/api/courts/load', post_data).then( response => {
                    this.court = response.data.court;
                });
            },

            addPoint: function(teamId) {

                let post_data = {
                    'court_id': this.court.id,
                    'team_id': teamId,
                }

                this.$http.post('/api/courts/add-point', post_data).then( response => {

                });

                this.court['team' + teamId]['points'] ++;

            },

            removePoint: function(teamId) {

                let post_data = {
                    'court_id': this.court.id,
                    'team_id': teamId,
                }

                this.$http.post('/api/courts/remove-point', post_data).then( response => {

                });

                if (this.court['team' + teamId]['points'] > 0) {
                    this.court['team' + teamId]['points'] --;
                }

            },

            addSet: function(teamId) {

                let post_data = {
                    'court_id': this.court.id,
                    'team_id': teamId,
                }

                this.$http.post('/api/courts/add-set', post_data).then( response => {

                });

                this.court['team' + teamId]['sets'] ++;

            },

            removeSet: function(teamId) {

                let post_data = {
                    'court_id': this.court.id,
                    'team_id': teamId,
                }

                this.$http.post('/api/courts/remove-set', post_data).then( response => {

                });

                if (this.court['team' + teamId]['sets'] > 0) {
                    this.court['team' + teamId]['sets'] --;
                }

            },

            setTeamName: _.debounce(
                function(teamId) {

                let team = this.$lodash.find(this.court, {'id': teamId});

                let post_data = {
                    'court_id': this.court.id,
                    'team_id': teamId,
                    'team_name': team.name
                }

                this.$http.post('/api/courts/set-team-name', post_data).then( response => {

                });

            }, 1000),

            resetScores: function() {
            
                let post_data = {
                    'court_id': this.court.id,
                }

                this.$http.post('/api/courts/reset-scores', post_data).then( response => {
                   
                });

            },


        },

        mounted() {

            this.loadCourt();

            window.socket.on('App\\Events\\CourtUpdated', data => {
                if (data.court_id == this.court.id) {
                    this.loadCourt();
                }
            });

        },

        beforeDestroy() {

            window.socket.removeListener('App\\Events\\CourtUpdated');

        },

    };
</script>
