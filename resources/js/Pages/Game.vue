<script setup>

import { router } from '@inertiajs/vue3';
import { ref, watch } from 'vue'
import { useDates } from '@/Composables/UseDates.js'
const { displayShortDateTime } = useDates();

const props = defineProps({
    game: { type: Object, required: true },
    stats: { type: Array, required: true },
});

import UserStat from '@/Components/UserStat.vue';
import TeamStat from '@/Components/TeamStat.vue';

</script>

<template>
    <div class="">

        <div class="flex justify-between border-b border-gray-400">
            <div class="">{{ game.team1.name }} vs <span class="font-semibold">{{ game.team2.name }}</span></div>
            <div class="date">{{ displayShortDateTime(game.created_at) }}</div>
        </div>

        <div class="grid grid-cols-[auto_auto_auto_auto_auto] mt-4">

            <div class="contents row">
                <div class="cell"></div>
                <div class="cell" v-for="stat in stats">
                    <div class="flex">
                        <div class="">{{ stat.name }}</div>
                        <TeamStat :game="game" :stat="stat" :team="game.team1"></TeamStat>
                    </div>
                </div>
            </div>

            <div class="contents row" v-for="user in game.team1.users">
                <div class="cell">{{ user.nickname ??= user.name }}</div>
                <div class="cell" v-for="stat in stats">
                    <UserStat :user="user" :stat="stat" :game="game"></UserStat>
                </div>
            </div>

        </div>

    </div>
</template>
