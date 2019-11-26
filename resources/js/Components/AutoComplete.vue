<template>

    <div class="search">

        <input :id="'search_' + name" 
                class="input" 
                name="terms" 
                v-model="terms" 
                :class="[( value ? 'existing' : ''), ( id ? 'selected' : '')]" 
                autocomplete="off" 
                @keyup.enter="selectCurrent" 
                @keyup.up="selectPrev" 
                @keyup.down="selectNext"
                @blur="results = []"
        >
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
                    :data-value="result.value"
                    :data-index="index"
                    :data-add="result.add"
                    @click="selectItem" 
                    :class="{ selected: result.selected }"
                >
                {{ result.label }}
                </div>
            </transition-group>
        </div>
       
    </div>

</template>

<script>

    import Helpers from '@/Mixins/Helpers'

    export default {

        props: ['object', 'afterSearching', 'clear', 'name', 'required', 'oldid', 'text', 'canAdd'],

        data: function () {
            return {
                terms: this.text,
                id: '',
                clicked: false,
                results: [],
                add: ''
            }
        },

        computed: {
            value: function() {
                return (! this.id && this.oldid ? this.oldid : this.id);
            }
        },

        mixins: [Helpers],

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
            },
            id: 'select',
            clear: 'clearFields',
        },

        methods: {

            selectNext: function(e) {

                if (this.results.length > 0) {
                    let current = this.$lodash.findIndex(this.results, function(result) {
                        return result.selected;
                    });

                    this.$lodash.forEach(this.results, function(result) {
                        result.selected = false;
                    });

                    if (current != -1) {
                        if (this.results[(current + 1)] != undefined) {
                            this.results[(current + 1)].selected = true;
                        } else {
                            if (this.results.length > 1) {
                                this.results[0].selected = true;
                            }
                        }
                    } else {
                        this.results[0].selected = true;
                    }
                }

            },

            selectPrev: function(e) {

                if (this.results.length > 0) {
                    let current = this.$lodash.findIndex(this.results, function(result) {
                        return result.selected;
                    });

                    this.$lodash.forEach(this.results, function(result) {
                        result.selected = false;
                    });

                    if (current != -1) {
                        if (this.results[(current - 1)] != undefined) {
                            this.results[(current - 1)].selected = true;
                        } else {
                            if (this.results.length > 1) {
                                this.results[(this.results.length - 1)].selected = true;
                            }
                        }
                    } else {
                        this.results[(this.results.length - 1)].selected = true;
                    }
                }
            
            },

            selectCurrent: function(e) {

                let current = this.$lodash.findIndex(this.results, function(result) {
                    return result.selected;
                });

                if (current != -1) {
                    this.clicked = true;
                    this.terms = this.results[current].value;
                    this.id = this.results[current].id;
                }

            },

            search: _.debounce(
                function () {

                    let post_data = {
                        'terms': this.terms
                    }

                    this.$http.post('/api/search/' + this.object, post_data).then( response => {
                        let res = response.data;
                        if (this.canAdd) {
                            res.push({
                                id: 0,
                                value: this.terms,
                                label: 'Add ' + this.terms,
                                selected: false,
                                add: this.canAdd
                            });
                        } 
                        this.results = response.data;
                    }, error => {
                    
                    });


                }, 250 // length to wait before running the function
            ),

            selectItem: function(el) {

                if (el.target.dataset.value != this.terms) {
                    this.clicked = true;
                }

                this.terms = el.target.dataset.value;
                this.id = el.target.id;
                this.add = el.target.dataset.add;

            },

            select: function() {

                this.results = [];

                /**
                 * after we have selected someone from the search, 
                 * call a function if one is defined in the 
                 * props from the tag
                 */

                if (this.add != undefined) {
                    let addFunction = this.add;

                    if ( this.$lodash.isFunction(this[addFunction])) {
                        this[addFunction]();
                        this.add = '';
                    }

                }

                let postFunction = this.afterSearching;
                let postOptions = '';
                if (postFunction.indexOf('(')) {
                    postOptions = postFunction.substring(postFunction.indexOf('(') + 1, postFunction.indexOf(')'));
                    postFunction = postFunction.substring(0, postFunction.indexOf('('));
                }

                if ( this.$lodash.isFunction(this[postFunction])) {
                    this[postFunction](postOptions);
                }

                this.clearFields();

            },

            clearFields: function() {
                if (this.clear) {
                    this.terms = '';
                    this.id = '';
                    this.add = '';
                }
            },

            /**
             *  The following are all methods for after
             *  we have search results. Adding to lists ans such
             */

            addPlayerToTeam: function(team_id) {

                if (this.id) {
                    
                    let post_data = {
                        'user_id': this.id,
                        'team_id': team_id
                    }

                    this.showLoading();

                    this.$http.post('/api/teams/add-player/' + team_id, post_data).then( response => {
                        
                    }, function (error) {
                    
                    });
                }

            },

            addTeam: function() {

                let post_data = {
                    'team_name': this.terms
                }

                this.$http.post('/api/teams/create', post_data).then( response => {

                    this.clicked = true;
                    this.terms = response.data.team_name;
                    this.id = response.data.id;

                }, function (error) {
                
                });

            }
            

        },

    }
</script>
