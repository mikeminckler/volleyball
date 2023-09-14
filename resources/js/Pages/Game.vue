<script setup>

import { router } from '@inertiajs/vue3';
import { ref, watch } from 'vue'
import { useDates } from '@/Composables/UseDates.js'
const { displayShortDateTime } = useDates();

import AutoComplete from '@/Components/AutoComplete.vue';

const props = defineProps({
    game: { type: Object, required: true },
});

const addPlayer = ref();

const createUser = (name) => {
    axios.post(route('users.create'), { name: name, json: true }).then(response => {
        addPlayer.value = response.data.user;
    });
};

watch(() => addPlayer.value, () => {
    router.post(route('teams.add-player', { id: props.game.team1.id }), { user: addPlayer.value, game_id: props.game.id });
});

</script>

<template>
    <div class="">
        <div class="flex justify-between border-b border-gray-400">
            <div class="">{{ game.team1.name }} vs <span class="font-semibold">{{ game.team2.name }}</span></div>
            <div class="date">{{ displayShortDateTime(game.created_at) }}</div>
        </div>

        <div class="">
            <div class="flex" v-for="user in game.team1.users">
                <div class="">{{ user.nickname ??= user.name }}</div>

                <div class="">
                    STATS
                </div>

                <div class="flex">
                    <div class="w-4 border grid place-items-center"><FaIcon icon="fa-caret-up"></FaIcon></div>
                    <div class="w-4 border grid place-items-center"><FaIcon icon="fa-caret-down"></FaIcon></div>
                </div>
            </div>
        </div>

        <div class="">
            <AutoComplete v-model="addPlayer" model="users" placeholder="Add Player" @create="createUser($event)"></AutoComplete>
        </div>

    </div>
</template>
