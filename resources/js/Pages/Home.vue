<script setup>

import { router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { useDates } from '@/Composables/UseDates.js'
const { displayShortDateTime, daysOld } = useDates();

import AutoComplete from '@/Components/AutoComplete.vue';
import Score from '@/Components/Score.vue';

const props = defineProps({
    teams: { type: Array },
    currentTeam: { type: Object },
});

const showCreateGame = ref(false);
const opponent = ref(null);

const selectTeam = (team) => {
    router.post(route('teams.select'), { team_id: team.id });
}

const createTeam = (name) => {
    axios.post(route('teams.create'), { name: name, json: true }).then(response => {
        opponent.value = response.data.team;
    });
};

watch(() => opponent.value, () => {
    router.post(route('games.create'), { team1: props.currentTeam, team2: opponent.value });
});

const addPlayer = ref();

const createUser = (name) => {
    axios.post(route('users.create'), { name: name, json: true }).then(response => {
        addPlayer.value = response.data.user;
    });
};

watch(() => addPlayer.value, () => {
    router.post(route('teams.add-player', { id: props.currentTeam.id }), { user: addPlayer.value });
});

const sortPlayer = (data) => {
    router.post(route('teams.sort-player', { id: props.currentTeam.id }), { user: data.user, direction: data.direction });
}

const selectedGames = ref(props.currentTeam ? props.currentTeam.games.filter(game => {
    return daysOld(game.created_at) < 7;
}) : []);

const toggleGame = (game) => {
    const index = selectedGames.value.findIndex(g => {
        return game.id === g.id 
    });
        
    if (index >= 0) {
        selectedGames.value.splice(index, 1);
    } else {
        selectedGames.value.push(game);
    }
}

</script>

<template>

    <Head title="Home"></Head>

    <div class="contents" v-if="!currentTeam">
        <h1>Choose A Team</h1>

        <div class="mt-4">
            <div class="row cursor-pointer" v-for="team in teams" @click="selectTeam(team)">{{ team.name }}</div>
        </div>
    </div>

    <div class="grid grid-cols-[auto_auto]" v-if="currentTeam">

        <div class="">
            <h1>Games</h1>

            <div class="mt-4">
                <div class="button" @click="showCreateGame = !showCreateGame">
                    <FaIcon icon="fas fa-plus">New Game</FaIcon>
                </div>
                <div class="" v-if="showCreateGame">
                    <AutoComplete v-model="opponent" model="teams" :focus="true" @create="createTeam($event)" placeholder="Search or Add Team"></AutoComplete>
                </div>
            </div>

            <div class="mt-4">
                <div class="flex" v-for="game in currentTeam.games">
                    <div class="text-xl cursor-pointer opacity-50" @click="toggleGame(game)">
                        <FaIcon icon="far fa-circle" v-if="!selectedGames.find( g => g.id === game.id)"></FaIcon>
                        <FaIcon icon="far fa-circle-check" v-else></FaIcon>
                    </div>
                    <Link class="ml-2" :href="route('games.view', {id : game.id })">{{ game.team2.name }} - {{ displayShortDateTime(game.created_at) }}</Link>
                </div>
            </div>
        </div>

        <div class="ml-8">

            <h1>Players</h1>

            <div class="mt-4 grid grid-cols-6">
                
                <div class="contents row">
                    <div class="cell"></div>
                    <div class="cell" v-for="stat in $page.props.stats">
                        <div class="">{{ stat.name }}</div>
                    </div>
                    <div class="cell"></div>
                </div>

                <div class="contents row" v-for="player in currentTeam.users" :key="'player-' + player.id">
                    <div class="cell">{{ player.name }}</div>

                    <div class="cell" v-for="stat in $page.props.stats" :key="stat.name + '-' + selectedGames.length">
                        <Score :games="selectedGames" type="user" :item="player" :stat="stat" v-if="selectedGames.length"></Score>
                    </div>

                    <div class="flex cell">
                        <div class="button" @click="sortPlayer({ user: player, direction: 'up'})"><FaIcon icon="fa-caret-up"></FaIcon></div>
                        <div class="button ml-1" @click="sortPlayer({ user: player, direction: 'down'})"><FaIcon icon="fa-caret-down"></FaIcon></div>
                    </div>
                </div>

                <div class="contents row" v-if="selectedGames.length">
                    <div class="cell"></div>
                    <div class="cell" v-for="stat in $page.props.stats" :key="'team-' + stat.name + '-' + selectedGames.length">
                        <Score :games="selectedGames" type="team" :item="currentTeam" :stat="stat"></Score>
                    </div>
                    <div class="cell"></div>
                </div>
            </div>

            <AutoComplete class="mt-4" v-model="addPlayer" model="users" placeholder="Add Player" @create="createUser($event)"></AutoComplete>
        </div>

    </div>


</template>
