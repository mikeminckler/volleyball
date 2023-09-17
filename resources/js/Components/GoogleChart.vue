<script setup>

import { ref, onBeforeUnmount, watch } from 'vue';

import { storeToRefs } from 'pinia';
import { useGlobalStore } from '@/Stores/GlobalStore.js';
const { selectedPlayers } = storeToRefs(useGlobalStore());

const loaded = ref(false);

google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(loaded.value = true);

const props = defineProps({
    games: { type: Array, required: true },
});

const drawChart = () => {
    axios.post(route('games.chart'), { games: props.games, players: selectedPlayers.value }).then(response => {

        const data = google.visualization.arrayToDataTable(response.data.data);
        const chart = new google.visualization.LineChart(document.getElementById('chart'));

        const options = {
            //title: 'vs ' + props.games.map(g => g.team2.name)
            height: 300,
            curveType: 'function',
            legend: { position: 'bottom' },
            backgroundColor: 'transparent',
            series: {
                0: { lineWidth: 4, color: '#000000' }
            },
            chartArea:{left:0,top:16,width:'100%',height:'100%'},
            hAxis: { textPosition: 'none' },
            legend: { position: 'in' },
        };

        chart.draw(data, options);

    });
}

drawChart();

watch(() => selectedPlayers.value.length, () => drawChart());
watch(() => props.games.length, () => drawChart());

props.games.forEach(game => {

    Echo.private('game.' + game.id)
        .listen('UserStatCreated', (data) => {
            if (data.user_stat.game_id === game.id) {
                drawChart();
            }
        })
        .listen('UserStatDeleted', (data) => {
            if (data.game.id === game.id) {
                drawChart();
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
    <div id="chart"></div>
</template>
