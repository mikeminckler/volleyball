<script setup>

import { router } from '@inertiajs/vue3';
import { ref, watch, onBeforeUnmount } from 'vue'
import { debounce } from 'lodash'
import { useDates } from '@/Composables/UseDates.js'
const { displayShortDateTime } = useDates();

import { storeToRefs } from 'pinia';
import { useGlobalStore } from '@/Stores/GlobalStore.js';
const { showUndo } = storeToRefs(useGlobalStore());

const props = defineProps({
    game: { type: Object, required: true },
});

import UserStat from '@/Components/UserStat.vue';
import Score from '@/Components/Score.vue';
import GoogleChart from '@/Components/GoogleChart.vue';

const updateGame = debounce(function() {
    axios.post(route('games.update', { id: props.game.id }), props.game);
}, 10);


Echo.private('game.' + props.game.id)
    .listen('GameUpdated', (data) => {
        if (data.game.id === props.game.id) {
            router.reload({ only: ['game'], preserveScroll: true });
            //router.get(route('games.view', { id: props.game.id }));
        }
    });

onBeforeUnmount(() => {
    Echo.leave('game.' + props.game.id);
});

</script>

<template>

    <Head :title="'vs ' + game.team2.name" />

    <div class="">

        <div class="md:flex justify-between border-b border-gray-400 py-1 items-baseline">
            <div class="">{{ game.team1.name }} vs <span class="font-semibold">{{ game.team2.name }}</span></div>
            <div class="flex-1 md:flex justify-center">
                <input class="w-64 text-center py-1 px-2 border border-gray-300 rounded focus:outline-none focus:border-gray-400" 
                    @blur="updateGame()"
                    v-model="game.notes" 
                    placeholder="Scores" />
            </div>
            <div class="date">{{ displayShortDateTime(game.created_at) }}</div>
        </div>

        <div class="grid md:grid-cols-[auto_auto_auto_auto_auto] mt-4">

            <div class="contents row">
                <div class="cell">
                    <div class="button text-sm w-6 h-6 opacity-70 ml-2" @click="showUndo = !showUndo">
                        <FaIcon icon="fa-solid fa-rotate-left"></FaIcon>
                    </div>
                </div>
                <div class="cell" v-for="stat in $page.props.stats">
                    <div class="flex">
                        <div class="">{{ stat.name }}</div>
                        <Score :games="[game]" type="team" :item="game.team1" :stat="stat"></Score>
                    </div>
                </div>
            </div>

            <div class="contents row" v-for="user in game.team1.users">
                <div class="cell">{{ user.nickname ??= user.name }}</div>
                <div class="cell" v-for="stat in $page.props.stats">
                    <div class="md:hidden text-xs">{{ stat.name }}</div>
                    <UserStat :user="user" :stat="stat" :game="game"></UserStat>
                </div>
            </div>

        </div>

        <div class="">
            <GoogleChart :game="game"></GoogleChart>
        </div>

    </div>
</template>
