<template>
    <input class="input stat-setting"
        v-bind:value="value"
        v-on:blur="updateValue($event.target.value)"
        v-on:focus="selectAll"
    >
</template>

<script>

    import Helpers from './Helpers'

    export default {

        mixins: [Helpers],

        data: function () {
            return {
                value: '',
                oldValue: ''
            }
        },

        props: ['type', 'team', 'stat'],

        computed: {
        },

        created () {
        },

        mounted () {
            this.getValue();
        },

        methods: {

            updateValue: function(value) {
                
                if (value != this.oldValue) {
                
                    let post_data = {
                        'stat_id': this.stat.id,
                        'value': value,
                        'type': this.type
                    }
                    
                    this.$http.post('/api/teams/set-stat/' + this.team.id, post_data).then( response => {
                        this.oldValue = response.data;
                        this.value = response.data;
                    }, function (error) {
                    
                    });

                    this.oldValue = value;

                }
            },

            getValue: function() {

                let post_data = {
                    'stat_id': this.stat.id,
                    'type': this.type
                }

                this.$http.post('/api/teams/get-stat/' + this.team.id, post_data).then( response => {
                    this.oldValue = response.data;
                    this.value = response.data;
                }, function (error) {
                
                });

            },

            selectAll: function (event) {
                setTimeout(function () {
                    event.target.select()
                }, 0);
            }

        }
    };
</script>
