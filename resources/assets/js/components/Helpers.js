import moment from 'moment'

export default {

    methods: {

        showLoading: function() {
            document.getElementById('loading').style.opacity = 1;
        },

        hideLoading: function() {
            document.getElementById('loading').style.opacity = 0;
        },

        displayDateTime(value) {
            return moment(value).format('h:mma ddd MMM Do, YYYY');
        },

        upperCase(value) {
            return this.$lodash.capitalize(value);
        }

    }

}
