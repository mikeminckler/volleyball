import Helpers from './Helpers'

export default {

    mixins: [Helpers],
    
    methods: {

        loadGame: function(game_id) {

            if (this.$lodash.toFinite(game_id)) {

                this.$http.post('/api/games/load/' + game_id).then( response => {
                    this.game = response.data;
                });

            }
        },


    }

}
