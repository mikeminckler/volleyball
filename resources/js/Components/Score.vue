<script setup>

import { ref, computed, onBeforeUnmount } from 'vue';
import Score from '@/Components/Score.vue'
import { useColors } from '@/Composables/UseColors.js'
const { getColor } = useColors();

const props = defineProps({
    type: { type: String, required: true },
    item: { type: Object, required: true },
    stat: { type: Object, required: true },
    games: { type: Array, required: true },
});

const score = ref();
const loading = ref(true);

const getScore = () => {
    loading.value = true;
    axios.post(route(props.type + 's.stat-score', { id: props.item.id }), { games: props.games, stat: props.stat }).then(response => {
        score.value = response.data.score;
        setTimeout( () => loading.value = false, 500);
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

onBeforeUnmount(() => {
    props.games.forEach(game => {
        Echo.leave('game.' + game.id);
    });
});

</script>

<template>
    <div class="score flex items-center h-[28px] overflow-hidden text-xs md:text-base relative -mt-1" v-if="score" :class="loading ? 'loading' : ''">

        <div class="flex relative leading-none items-baseline">
            <div class="pl-1 font-bold">{{ score.score }}</div>
            <div class="text-xs opacity-70 font-semibold" v-if="score.attempts > 0">({{ score.attempts }})</div>
        </div>

        <div class="flex" :class="stat.reverse ? 'flex-row justify-start' : 'flex-row-reverse justify-end'">
            <div class="text-xs ml-0.5 text-center score-total rounded border w-4 font-semibold text-gray-800" 
                :class="getColor(total.chart_score)" 
                v-for="total in score.totals"
            >{{ total.total }}</div>
        </div>

        <div class="flex absolute bottom-0">
            <div class="ml-0.5 w-1 h-1 md:w-1.5 md:h-1.5 pip" v-for="item in score.latest" :class="getColor(item)"></div>
        </div>

    </div>
</template>
