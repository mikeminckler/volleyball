var Vue = require('vue');
import Vuex from 'vuex';
Vue.use(Vuex);

var axios = require('axios');
var moment = require('moment');
var io = require('socket.io-client');

if (window.location.hostname.indexOf('gamestats.brentwood.bc.ca') != -1) {
    window.socket = io('//' + window.location.hostname);
} else {
    window.socket = io('//' + window.location.hostname + ':3000');
}

Vue.prototype.$http = axios;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import lodash from 'lodash';    
Object.defineProperty(Vue.prototype, '$lodash', { value: lodash });

if (window.app.csrfToken) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = window.app.csrfToken;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

import Court from './components/Court.vue';
Vue.component('court', Court);

import Feedback from './components/Feedback.vue';
Vue.component('feedback', Feedback);

import TempScoreboard from './components/TempScoreboard.vue';
Vue.component('temp-scoreboard', TempScoreboard);

var store = new Vuex.Store({
    state: {
        feedback: [],
    },

    mutations: {

        addFeedback (state, item) {
            state.feedback.push(item);
        },

        clearFeedback (state) {
            state.feedback = state.feedback.filter( function(item) {
                //console.log(new Date().getTime() + '::' + item.expire);
                let now = new Date().getTime();
                if (item.expire < now && item.type != 'error') {
                    return false;
                } else {
                    return true;
                }
            });
        },

        clearErrorsFeedback (state) {
            state.feedback = state.feedback.filter( function(item) {
                return item.type != 'error';
            });
        },

    },

    actions: {
        
        addFeedback({ commit, state }, feedback) {
            
            let item = {
                type: feedback.type,
                message: feedback.message,
                link: feedback.link,
                expire: (new Date().getTime() + 4900)
            };
            commit('addFeedback', item);

            // clear out any info and successes automatically
            
            setTimeout(function() {
               app.$store.dispatch('clearFeedback');
            }, 5000);
           
        },

        clearFeedback({ commit }) {
            commit('clearFeedback'); 
        },

        clearErrorsFeedback({ commit }) {
            commit('clearErrorsFeedback');
        },

    }
});

var app = new Vue({
    el: '#app',
    store,

    methods: {

    },

    created: function () {

        let post_data = {
            'email':    'scoreboard@brentwood.ca',
            'password': 'scoreboard',
        };

        this.$http.post('/api/login', post_data).then( response => {

            window.socket.connect();
            window.socket.emit('authenticate', {token: response.data.token});

        }, error => {

        });

        window.socket.emit('join-scoreboard');

        setTimeout( function() { 
            location.reload();
        }, 1800000);

    },

    beforeDestroy() {
        window.socket.close();
    }

});

var supportsTouch = 'ontouchstart' in window || navigator.msMaxTouchPoints;

