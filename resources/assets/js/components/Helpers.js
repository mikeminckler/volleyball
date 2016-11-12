export default {

    methods: {

        isNumeric: function(n) {
              return !isNaN(parseFloat(n)) && isFinite(n);
        }

    }

}
