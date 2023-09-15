<script setup>

import { ref, computed } from 'vue';
import Score from '@/Components/Score.vue';

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
    axios.post(route('users.create-stat',  { id: props.user.id }), { game: props.game, stat: props.stat, score: value });
}

const undo = () => {
    axios.post(route('users.undo-stat',  { id: props.user.id }), { game: props.game, stat: props.stat });
}

</script>

<template>

<div class="flex items-center">
    
    <div class="button text w-7 h-7 ml-1 first:ml-0" v-for="button in buttons" @click="createStat(button)">
        <FaIcon icon="fa-plus" v-if="buttons.length === 1"></FaIcon>
        <div class="" v-else>{{ button }}</div>
    </div>

    <Score type="user" :item="user" :games="[game]" :stat="stat"></Score>

    <div class="button text-sm w-6 h-6 opacity-70 ml-2" @click="undo()">
        <FaIcon icon="fa-solid fa-rotate-left"></FaIcon>
    </div>
</div>

</template>
