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

</script>

<template>

    <Head title="Home"></Head>

    <div class="contents" v-if="!currentTeam">
        <h1>Choose A Team</h1>

        <div class="mt-4">
            <div class="row" v-for="team in teams" @click="selectTeam(team)">{{ team.name }}</div>
        </div>
    </div>

    <div class="contents" v-if="currentTeam">

        <div class="flex">
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

</template>
