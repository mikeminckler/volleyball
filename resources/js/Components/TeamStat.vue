<script setup>

import { ref } from 'vue'
import Score from '@/Components/Score.vue'

const props = defineProps({
    team: { type: Object, required: true },
    stat: { type: Object, required: true },
    game: { type: Object, required: true },
});

const score = ref();

const getScore = () => {
    axios.post(route('teams.stat-score', { id: props.team.id }), { game: props.game, stat: props.stat }).then(response => {
        score.value = response.data.score;
    });
}

getScore();

Echo.private('game.' + props.game.id)
    .listen('UserStatCreated', (data) => {
        if (data.user_stat.game_id === props.game.id && data.user_stat.stat_id === props.stat.id) {
            getScore();
        }
    })
    .listen('UserStatDeleted', (data) => {
        if (data.game.id === props.game.id && data.stat.id === props.stat.id) {
            getScore();
        }
    });

</script>

<template>
    <Score :score="score" v-if="score"></Score>
</template>
