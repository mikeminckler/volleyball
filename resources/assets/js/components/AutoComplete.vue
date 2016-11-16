<template>

    <div class="search">
        <input id="terms" class="input" name="terms" v-model="terms" :class="[( value ? 'existing' : ''), ( id ? 'selected' : '')]" autocomplete="off" @blur="results = []">
        <input :id="name" class="input" type="hidden" :name="name" :value="value" :required="required">
 
        <div class="results">
            <transition-group 
                name="list" 
                tag="div"
                v-bind:css="false"
                v-on:before-enter="beforeEnter"
                v-on:enter="enter"
                v-on:leave="leave"
            >
                <div class="results-item" 
                    v-for="(result, index) in results" 
                    :key="result.id" 
                    :id="result.id"
                    :data-label="result.label"
                    :data-index="index"
                    @click="select" 
                >
                    {{ result.label }}
                </div>
            </transition-group>
        </div>
    </div>

</template>

<script>

    import ListTransition from './ListTransition'
    import Helpers from './Helpers'

    export default {

        props: ['object', 'afterSearching', 'clear', 'name', 'required', 'oldid', 'text'],

        data: function () {
            return {
                terms: '',
                id: '',
                clicked: false,
                results: [],
            }
        },

        computed: {
            value: function() {
                return (! this.id && this.oldid ? this.oldid : this.id);
            }
        },

        mixins: [ListTransition, Helpers],

        watch: {
            // we need to watch the prop as it gets asynced in
            text: function (text) {
                this.clicked = true;
                this.terms = text;
            },
            terms: function (terms) {
                if (!this.clicked) {
                    this.id = '';
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


                }, 250 // length to wait before running the function
            ),

            select: function(el) {

                this.id = el.target.id;

                if (el.target.dataset.label != this.terms) {
                    this.clicked = true;
                }

                this.terms = el.target.dataset.label;
                this.results = [];

                /**
                 * after we have selected someone from the search, 
                 * call a function if one is defined in the 
                 * props from the tag
                 */

                let postFunction = this.afterSearching;
                let postOptions = '';
                if (postFunction.indexOf('(')) {
                    postOptions = postFunction.substring(postFunction.indexOf('(') + 1, postFunction.indexOf(')'));
                    postFunction = postFunction.substring(0, postFunction.indexOf('('));
                }

                if ( _.isFunction(this[postFunction])) {
                    this[postFunction](postOptions);
                }

                if (this.clear) {
                    this.terms = '';
                    this.id = '';
                }

            },

            /**
             *  The following are all methods for after
             *  we have search results. Adding to lists ans such
             */

            addPlayerToTeam: function(team_id) {

                var vue = this;
                let post_data = {
                    'user_id': this.id,
                    'team_id': team_id
                }

                vue.showLoading();

                vue.$http.post('/api/teams/add-player/' + team_id, post_data).then( function(response) {
                    
                }, function (error) {
                
                });

            },
            

        },

    }
</script>
