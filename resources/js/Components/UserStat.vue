<script setup>

import { ref, computed } from 'vue';
import Score from '@/Components/Score.vue';
const clicked = ref();

import { storeToRefs } from 'pinia';
import { useGlobalStore } from '@/Stores/GlobalStore.js';
const { showUndo } = storeToRefs(useGlobalStore());

const props = defineProps({
    user: { type: Object, required: true },
    stat: { type: Object, required: true },
    game: { type: Object },
});

const buttons = computed(() => {
    let items = [];

    if (props.stat.low_score == props.stat.high_score) {
        items.push(props.stat.low_score);
    } else if (props.stat.low_score > props.stat.high_score) {
        for (let i = props.stat.high_score; i <= props.stat.low_score; i++) {
            items.push(i);
        }
    } else {
        for (let i = props.stat.high_score; i >= props.stat.low_score; i--) {
            items.push(i);
        }
    }

    return items;
});

const createStat = (value) => {
    clicked.value = 'stat-' + value;
    axios.post(route('users.create-stat',  { id: props.user.id }), { game: props.game, stat: props.stat, score: value }).then(response => {
        setTimeout( () => clicked.value = null, 250);
    });
}

const undo = () => {
    clicked.value = 'undo';
    axios.post(route('users.undo-stat',  { id: props.user.id }), { game: props.game, stat: props.stat }).then(response => {
        setTimeout( () => clicked.value = null, 250);
    });
}

</script>

<template>

<div class="flex items-center">

    <div class="button text w-6 h-6 ml-0.5 first:ml-0" v-for="button in buttons" @click="createStat(button)" :class="clicked === 'stat-' + button ? 'clicked' : ''">
        <FaIcon icon="fa-plus" v-if="buttons.length === 1"></FaIcon>

        <div class="text-green-600" v-else-if="stat.low_score === -1 && stat.high_score === 1 && button === 1">
            <FaIcon icon="fa-check"></FaIcon>
        </div>

        <div class="text-xs text-gray-500" v-else-if="stat.low_score === -1 && stat.high_score === 1 && button === 0">
            <FaIcon icon="fa-circle"></FaIcon>
        </div>

        <div class="text-red-600" v-else-if="stat.low_score === -1 && stat.high_score === 1 && button === -1">
            <FaIcon icon="fa-times"></FaIcon>
        </div>

        <div class="" v-else>{{ button }}</div>
    </div>

    <Score type="user" :item="user" :games="[game]" :stat="stat"></Score>

    <div class="button text-sm w-6 h-6 opacity-70 ml-2" @click="undo()" :class="clicked === 'undo' ? 'clicked' : ''" v-if="showUndo">
        <FaIcon icon="fa-solid fa-rotate-left"></FaIcon>
    </div>
</div>

</template>
