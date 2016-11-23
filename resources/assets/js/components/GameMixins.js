import Helpers from './Helpers'

export default {

    mixins: [Helpers],
    
    methods: {

        loadGame: function(game_id) {

            var vue = this;

            if (_.toFinite(game_id)) {

                vue.$http.post('/api/games/load/' + game_id).then( function(response) {
                    vue.game = response.data;
                });

            }
        },


    }

}
