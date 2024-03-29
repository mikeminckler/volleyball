<script setup>

import { router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { useDates } from '@/Composables/UseDates.js'
const { displayShortDateTime, daysOld } = useDates();

import AutoComplete from '@/Components/AutoComplete.vue';
import Score from '@/Components/Score.vue';
import GoogleChart from '@/Components/GoogleChart.vue';

const props = defineProps({
    teams: { type: Array },
    currentTeam: { type: Object },
    games: { type: Object },
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

const removePlayer = (player) => {
    if (confirm('Are you sure you want to remove that player?')) {
        router.post(route('teams.remove-player', { id: props.currentTeam.id }), { user: player });
    }
}

const sortPlayer = (data) => {
    router.post(route('teams.sort-player', { id: props.currentTeam.id }), { user: data.user, direction: data.direction });
}

const selectedGames = ref(props.currentTeam ? props.currentTeam.games.filter(game => {
    return daysOld(game.created_at) < 6;
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

import { storeToRefs } from 'pinia';
import { useGlobalStore } from '@/Stores/GlobalStore.js';
const { selectedPlayers } = storeToRefs(useGlobalStore());

const togglePlayer = (player) => {
    const index = selectedPlayers.value.findIndex(p => {
        return p.id === player.id 
    });
        
    if (index >= 0) {
        selectedPlayers.value.splice(index, 1);
    } else {
        selectedPlayers.value.push(player);
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

    <div class="grid md:grid-cols-[auto_auto] text-sm md:text-base mt-4" v-if="currentTeam">

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
                <div class="flex items-center row leading-none" v-for="game in games.data">
                    <div class="text-xl cursor-pointer opacity-50" @click="toggleGame(game)">
                        <FaIcon icon="far fa-circle" v-if="!selectedGames.find( g => g.id === game.id)"></FaIcon>
                        <FaIcon icon="far fa-circle-check" v-else></FaIcon>
                    </div>
                    <div class="">
                        <Link class="ml-2" :href="route('games.view', {id : game.id })">{{ game.team2.name }} - <span class="text-sm">{{ displayShortDateTime(game.created_at) }}</span></Link>
                        <div class="ml-2 text-sm opacity-75">{{ game.notes }}</div>
                    </div>
                </div>
                <div class="flex w-full justify-center mt-2">
                    <div class="" v-for="link in games.links">
                        <Link class="mx-1" :href="link.url" v-if="link.url"><span v-html="link.label"></span></Link>
                        <div class="opacity-50 mx-1" v-else><span v-html="link.label"></span></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="md:ml-4">

            <h1>Players</h1>

            <div class="mt-4 grid grid-cols-[auto_auto_auto_auto_auto] md:grid-cols-[auto_auto_auto_auto_auto_auto]">
                
                <div class="contents row">
                    <div class="cell"></div>
                    <div class="cell" v-for="stat in $page.props.stats">
                        <div class="text-xs md:text-base">{{ stat.name }}</div>
                    </div>
                    <div class="cell hidden md:block"></div>
                </div>

                <div class="contents row" v-for="player in currentTeam.users" :key="'player-' + player.id">
                    <div class="cell">
                        <Link :href="route('users.edit', { id: player.id })">{{ player.nickname ??= player.name }}</Link>
                    </div>

                    <div class="cell" v-for="stat in $page.props.stats" :key="stat.name + '-' + selectedGames.length">
                        <Score :games="selectedGames" type="user" :item="player" :stat="stat" v-if="selectedGames.length"></Score>
                    </div>

                    <div class="hidden md:flex cell">
                        <div class="button" @click="togglePlayer(player)"><FaIcon icon="fa-chart-line"></FaIcon></div>
                        <div class="button" @click="sortPlayer({ user: player, direction: 'up'})"><FaIcon icon="fa-caret-up"></FaIcon></div>
                        <div class="button ml-1" @click="sortPlayer({ user: player, direction: 'down'})"><FaIcon icon="fa-caret-down"></FaIcon></div>
                        <div class="button ml-1" @click="removePlayer(player)"><FaIcon icon="fas fa-times"></FaIcon></div>
                    </div>
                </div>

                <div class="contents row" v-if="selectedGames.length">
                    <div class="cell"></div>
                    <div class="cell" v-for="stat in $page.props.stats" :key="'team-' + stat.name + '-' + selectedGames.length">
                        <Score :games="selectedGames" type="team" :item="currentTeam" :stat="stat"></Score>
                    </div>
                    <div class="cell hidden md:block"></div>
                </div>
            </div>

            <AutoComplete class="mt-4" v-model="addPlayer" model="users" placeholder="Add Player" @create="createUser($event)"></AutoComplete>
        </div>

    </div>

    <div class="" :key="'team-' + currentTeam?.id" v-if="currentTeam">
        <GoogleChart :games="selectedGames"></GoogleChart>
    </div>


</template>
