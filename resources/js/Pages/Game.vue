<script setup>

import { useDates } from '@/Composables/UseDates.js'
const { displayShortDateTime } = useDates();

const props = defineProps({
    game: { type: Object, required: true },
});

</script>

<template>
    <div class="">
        <div class="flex justify-between border-b border-gray-400">
            <div class="">{{ game.team1.name }} vs <span class="font-semibold">{{ game.team2.name }}</span></div>
            <div class="date">{{ displayShortDateTime(game.created_at) }}</div>
        </div>

        <div class="">
            <div class="" v-for="user in game.team1.users">
                <div class="">{{ user.nickname ??= user.name }}</div>
            </div>
        </div>

    </div>
</template>
