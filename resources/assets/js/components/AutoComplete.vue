<template>

    <div class="search">
        <input id="terms" class="input" name="terms" v-model="terms">

        <div class="results">
            <transition-group name="feedback" tag="div">
                <div class="results-item" 
                    v-for="(result, index) in results" 
                    :key="result" 
                    :id="result.id"
                    :data-label="result.label"
                    @click="select" 
                >
                    {{ result.label }}
                </div>
            </transition-group>
        </div>
    </div>

</template>

<script>
    export default {

        props: ['object', 'afterSearching'],

        data: function () {
            return {
                terms: '',
                id: '',
                clicked: false,
                results: []
            }
        },

        watch: {
            terms: function (terms) {
                if (!this.clicked) {
                    this.search();
                } else {
                    this.clicked = false;
                }
            }
        },

        methods: {

            search: _.debounce(
                function () {

                    var vue = this;

                    let post_data = {
                        'terms': this.terms
                    }

                    vue.$http.post('/api/search/' + this.object, post_data).then( function(response) {
                        vue.results = response.data;
                    }, function (error) {
                    
                    });

                    /**
                     * after we have finished the search, call a function
                     * if one is defined in the props from the tag
                     */

                    if ( _.isFunction(this[this.afterSearching])) {
                        this[this.afterSearching]();
                    }

                }, 500 // length to wait before running the function
            ),

            select: function(e) {

                this.id = e.target.id;

                if (e.target.dataset.label != this.terms) {
                    this.clicked = true;
                }

                this.terms = e.target.dataset.label;
                this.results = [];

            },

            /**
             *  The following are all methods for after
             *  we have search results. Adding to lists ans such
             */

            

        }

    }
</script>
