import moment from 'moment'

export default {

    methods: {

        isNumeric: function(n) {
              return !isNaN(parseFloat(n)) && isFinite(n);
        },

        showLoading: function() {
            document.getElementById('loading').style.opacity = 1;
        },

        hideLoading: function() {
            document.getElementById('loading').style.opacity = 0;
        },

        displayDateTime(value) {
            return moment(value).format('ddd MMM Do, YYYY');
        }

    }

}
