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

            if (this.$lodash.toNumber(stat_id)) {

                this.$http.post('/api/stats/load/' + stat_id).then( response => {
                    this.stat = response.data;
                });
            }
        },

        loadStats: function() {

            this.$http.post('/api/stats').then( response => {
                this.stats = response.data;
            });
        
        },

    }

};
