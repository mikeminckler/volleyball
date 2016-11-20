import Helpers from './Helpers'

export default {
    components: {},

    data: function () {
        return {
            stat: {
                id: '',
                stat_name: ''
            },
            stats: {}
        }
    },

    mixins: [Helpers],

    methods: {
        
        loadStat: function(stat_id) {

            var vue = this;

            if (vue.isNumeric(stat_id)) {

                vue.$http.post('/api/stats/load/' + stat_id).then( function(response) {
                    vue.stat = response.data;
                });
            }
        },

        loadStats: function() {

            var vue = this;
            vue.$http.post('/api/stats').then( function(response) {
                vue.stats = response.data;
            });
        
        },

    }

};
