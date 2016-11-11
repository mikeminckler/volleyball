<template>

    <div class="search">
        <input id="terms" class="input" name="terms" v-model="terms" :class="(id ? 'selected' : '')">

        <div class="results">
            <transition-group 
                name="autocomplete" 
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
    import Velocity from 'velocity-animate'

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

                    /**
                     * after we have finished the search, call a function
                     * if one is defined in the props from the tag
                     */

                    if ( _.isFunction(this[this.afterSearching])) {
                        this[this.afterSearching]();
                    }

                }, 250 // length to wait before running the function
            ),

            select: function(el) {

                this.id = el.target.id;

                if (el.target.dataset.label != this.terms) {
                    this.clicked = true;
                }

                this.terms = el.target.dataset.label;
                this.results = [];

            },

            /**
             *  The following are all methods for after
             *  we have search results. Adding to lists ans such
             */

            
            /**
             * customizing the transition states
             */

            beforeEnter: function (el) {
                el.style.opacity = 0
                el.style.height = 0
            },

            enter: function (el, done) {
                var delay = el.dataset.index * 25
                var dur = 400 - (el.dataset.index * 20);
                if (dur < 25) {
                    dur = 25;
                }
                setTimeout(function () {
                Velocity(
                    el,
                        { opacity: 1, height: '30px' },
                        { complete: done, duration: dur }
                    )
                }, delay)
            },

            leave: function (el, done) {
                var delay = el.dataset.index * 25
                var dur = 200 - (el.dataset.index * 10);
                if (dur < 25) {
                    dur = 25;
                }
                setTimeout(function () {
                Velocity(
                    el,
                        { opacity: 0, height: 0 },
                        { complete: done, duration: dur }
                    )
                }, delay)
            }

        }

    }
</script>
