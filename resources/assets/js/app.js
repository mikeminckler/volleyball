
require('./bootstrap');

window.io = require('socket.io-client');
var socket = io('http://localhost:3000'); 

// Vue Components

const Login = Vue.component('login', require('./components/Login.vue'));

const router = new VueRouter({
    routes: [
        { path: '/api/login', component: Login }
    ]
});

const store = new Vuex.Store({
    state: {

        user: {
            authenticated: false,
            token: '',
            first_name: '',
            last_name: '',
            email: '',
            id: ''
        },

        feedback: []
    },

    computed: {
        user_name () {
            return state.user.first_name + ' ' + state.user.last_name;
        }
    },

    mutations: {
        setToken (state, token) {
            state.user.authenticated = true;
            state.user.token = token;
        },

        removeToken (state) {
            state.user.authenticated = false;
            state.user.token = '';
        },

        addFeedback (state, item) {
            state.feedback.push(item);
        },

        userInfo (state, info) {
            state.user.first_name = info.first_name;
            state.user.last_name = info.last_name;
            state.user.email = info.email;
            state.user.id = info.id;
        }
    },

    actions: {
        setToken({ commit, state }, token) {

            commit('setToken', token);
            socket.emit('authenticate', {token: token});

        },
        
        removeToken({ commit, state }) {

            commit('removeToken');

            socket.emit('disconnect');
            socket.emit('public-message', state.user.email + ' has logged OUT');
        },

        userInfo({ commit, state }, info) {
            commit('userInfo', info);
            socket.emit('public-message', state.user.email + ' has logged IN');
        }
    }
});

Vue.http.headers.common['Authorization'] = store.state.user.token;

const app = new Vue({
    el: '#app',
    router,
    store,

    methods: {
        logout: function(e){
            this.$http.post(e.target.action).then((response) => {

                // call the action for the store update
                this.$store.dispatch('removeToken');

                this.$router.push('/api/login');

                this.$store.commit('addFeedback', {'type': 'success', 'message': 'Logged Out'});

            }, (response) => {

            });

        }
    },

    created: function () {
        if (!this.$store.state.user.authenticated) {
            this.$router.push('/api/login');
        }

        socket.on('public-message', function (data) {
            this.$store.commit('addFeedback', {'type': 'info', 'message': data});
        }.bind(this));
    }

});

/** 
 * A little Jquery to set css vars for windows size
 * may be able to ditch all this if we dont need 
 * window size
 */

var supportsTouch = 'ontouchstart' in window || navigator.msMaxTouchPoints;

$(function(){

    setWidth();

	$(window).resize(function() {
		if (supportsTouch) {
			window.addEventListener('orientationchange', orientation_change, false);
		} else {
            setWidth();
		}
	});

});

function orientation_change() {
	setWidth();
}

function setWidth() {
    document.documentElement.style.setProperty('--screen-width', $(window).width() + 'px');
}
