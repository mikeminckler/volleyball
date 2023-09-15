<script setup>

import { router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { useDates } from '@/Composables/UseDates.js'
const { displayShortDateTime } = useDates();

import AutoComplete from '@/Components/AutoComplete.vue';

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

</script>

<template>

    <Head title="Home"></Head>

    <div class="contents" v-if="!currentTeam">
        <h1>Choose A Team</h1>

        <div class="mt-4">
            <div class="row cursor-pointer" v-for="team in teams" @click="selectTeam(team)">{{ team.name }}</div>
        </div>
    </div>

    <div class="grid grid-cols-2" v-if="currentTeam">

        <div class="">
            <h1>Games</h1>

            <div class="flex mt-4">
                <div class="button" @click="showCreateGame = !showCreateGame">
                    <FaIcon icon="fas fa-plus">New Game</FaIcon>
                </div>
                <div class="" v-if="showCreateGame">
                    <AutoComplete v-model="opponent" model="teams" :focus="true" @create="createTeam($event)"></AutoComplete>
                </div>
            </div>

            <div class="mt-4">
                <Link class="block" :href="route('games.view', {id : game.id })" v-for="game in currentTeam.games">{{ game.team2.name }} - {{ displayShortDateTime(game.created_at) }}</Link>
            </div>
        </div>

        <div class="">

            <h1>Players</h1>

            <div class="mt-4">
                <div class="flex row" v-for="player in currentTeam.users">
                    <div class="flex-1">{{ player.name }}</div>
                    <div class="button" @click="sortPlayer({ user: player, direction: 'up'})"><FaIcon icon="fa-caret-up"></FaIcon></div>
                    <div class="button ml-1" @click="sortPlayer({ user: player, direction: 'down'})"><FaIcon icon="fa-caret-down"></FaIcon></div>
                </div>
            </div>

            <AutoComplete class="mt-4" v-model="addPlayer" model="users" placeholder="Add Player" @create="createUser($event)"></AutoComplete>
        </div>

    </div>


</template>
