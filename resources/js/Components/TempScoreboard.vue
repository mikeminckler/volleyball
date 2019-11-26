<template>

    <div class="temp-scoreboard">
        <court v-for="court in courts" 
            :key="court.id"
            :id="court.id" 
        ></court>
    </div>

</template>

<script>

    export default {

        data: function () {
            return {
                courts: []
            }
        },

        props: ['numberOfCourts'],

        methods: {

            createCourts: function() {

                var courts;

                for (courts = 0; courts < this.numberOfCourts; courts++) { 

                    let court = {
                        id: (courts + 1),
                    }

                    this.courts.push(court);

                }

            },

        },

        mounted() {

            this.createCourts();

        },

    };
</script>
