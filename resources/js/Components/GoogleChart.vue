<script setup>

import { ref, onBeforeUnmount } from 'vue';

const loaded = ref(false);

google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(loaded.value = true);

const props = defineProps({
    game: { type: Object },
});

const drawChart = () => {
    axios.post(route('games.chart', { id : props.game.id })).then(response => {

        const data = google.visualization.arrayToDataTable(response.data.data);
        const chart = new google.visualization.LineChart(document.getElementById('chart'));

        const options = {
            title: 'vs ' + props.game.team2.name,
            height: 500,
            curveType: 'function',
            legend: { position: 'bottom' },
            backgroundColor: 'transparent',
            //hAxis: { ticks: response.data.ticks },
            series: {
                0: { lineWidth: 4, color: '#000000' }
            }
        };

        chart.draw(data, options);

    });
}

drawChart();

Echo.private('game.' + props.game.id)
    .listen('UserStatCreated', (data) => {
        if (data.user_stat.game_id === props.game.id) {
            drawChart();
        }
    })
    .listen('UserStatDeleted', (data) => {
        if (data.game.id === props.game.id) {
            drawChart();
        }
    });

onBeforeUnmount(() => {
    Echo.leave('game.' + props.game.id);
});

</script>


<template>
    <div id="chart"></div>
</template>
