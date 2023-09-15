<script setup>

import { ref } from 'vue';
import Score from '@/Components/Score.vue'

const props = defineProps({
    type: { type: String, required: true },
    item: { type: Object, required: true },
    stat: { type: Object, required: true },
    games: { type: Array, required: true },
});

const score = ref();

const getScore = () => {
    axios.post(route(props.type + 's.stat-score', { id: props.item.id }), { games: props.games, stat: props.stat }).then(response => {
        score.value = response.data.score;
    });
}

getScore();

props.games.forEach(game => {
        
    Echo.private('game.' + game.id)
        .listen('UserStatCreated', (data) => {
            if (props.type === 'user') {
                if (data.user_stat.game_id === game.id && data.user_stat.user_id === props.item.id && data.user_stat.stat_id === props.stat.id) {
                    getScore();
                }
            } else {
                if (data.user_stat.game_id === game.id && data.user_stat.stat_id === props.stat.id) {
                    getScore();
                }
            }
        })
        .listen('UserStatDeleted', (data) => {
            if (props.type === 'user') {
                if (data.game.id === game.id && data.user.id === props.item.id && data.stat.id === props.stat.id) {
                    getScore();
                }
            } else {
                if (data.game.id === game.id && data.stat.id === props.stat.id) {
                    getScore();
                }
            }
        });
});

</script>

<template>
    <div class="flex items-baseline" v-if="score">
        <div class="pl-2 font-bold">{{ score.score }}</div>
        <div class="pl-1 text-xs opacity-70 font-semibold" v-if="score.attempts > 0">({{ score.attempts }})</div>
    </div>
</template>
