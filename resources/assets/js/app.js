
window._ = require('lodash');
window.$ = window.jQuery = require('jquery');


window.Vue = require('vue');
window.VueRouter = require('vue-router');
window.Vue.use(window.VueRouter);
window.Vuex = require('vuex');
window.axios = require('axios');

window.io = require('socket.io-client');
window.socket = io('http://localhost:3000');

Vue.prototype.$http = window.axios;

Vue.component('feedback', require('./components/Feedback.vue'));
Vue.component('app-menu', require('./components/Menu.vue'));
Vue.component('autocomplete', require('./components/AutoComplete.vue'));

const router = new VueRouter({
    routes: [
        { path: '/login', component: require('./components/Login.vue') },
        { path: '/home', component: require('./components/Home.vue') },
        { path: '/users', component: require('./components/Users.vue') },
        { path: '/users/:id', component: require('./components/User.vue') },
        { path: '/my-account', component: require('./components/User.vue') },
        { path: '/users/create', component: require('./components/User.vue') },
        //{ path: '/not-found', component: require('./components/404.vue') },
        //{ path: '/*', redirect: '/not-found' }
    ]
});

router.beforeEach( function(to, from, next) {
    if (to.path != '/login') {
        if (store.state.user.authenticated) {
            store.dispatch('clearErrorsFeedback');
            next();
        } else {
            if (to.path != '/') {
                store.dispatch('addFeedback', {'type': 'error', 'message': 'Please login to acces that page'});
            }
            store.state.intended = to.path;
            next('/login');
        }
    } else {
        if (store.state.user.authenticated) {
            next('/home');
        } else {
            next();
        }
    }
});

const store = new Vuex.Store({
    state: {

        user: {
            authenticated: false,
            token: '',
            first_name: '',
            last_name: '',
            email: '',
            id: '',
            roles: []
        },

        feedback: [],
        intended: '',
        menu: []
    },

    getters: {
        user_name: state => {
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

        userInfo (state, info) {
            state.user.first_name = info.first_name;
            state.user.last_name = info.last_name;
            state.user.email = info.email;
            state.user.id = info.id;
        },

        userRoles (state, info) {
            state.user.roles = info;
        },

        menu (state, menu) {
            state.menu = menu;
        }

    },

    actions: {
        setToken({ commit, state }, token) {

            commit('setToken', token);

            // connect incase we had logged out before
            window.socket.connect();

            // authenticate our token
            window.socket.emit('authenticate', {token: token});

            // add our listeners
            window.socket.on('auth.info', function (data) {
                app.$store.dispatch('addFeedback', {'type': 'info', 'message': data});
            }.bind(app));

        },

        userRoles({ commit, state }, info) {
            commit('userRoles', info);
            app.$store.dispatch('addFeedback', {'type': 'info', 'message': 'Your groups have been loaded'});
        },
        
        userInfo({ commit, state }, info) {
            commit('userInfo', info);
        },

        removeToken({ commit, state }) {
            commit('removeToken');
        },

        addFeedback({ commit, state }, feedback) {
            
            let item = {
                type: feedback.type,
                message: feedback.message,
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

        menu({ commit, state }, menu) {
            commit('menu', menu);
            app.$store.dispatch('addFeedback', {'type': 'info', 'message': 'Your menu is ready'});
        }

    }
});

const app = new Vue({
    el: '#app',
    router,
    store,

    methods: {

        logout: function(e){
            var vue = this;
            vue.$http.post(e.target.action).then( function(response) {

                window.socket.emit('auth.info', store.getters.user_name + ' has disconnected');

                // call the action for the store update
                vue.$store.dispatch('removeToken').then( function() {
                    window.socket.removeListener('auth.info');
                    window.socket.close();
                });

                vue.$router.push('/login');

                vue.$store.dispatch('addFeedback', {'type': 'success', 'message': 'Logged Out'});

            }, function(error) {
                // we have timed out or our token is invalid so lets go to the login page
                vue.$router.push('/login');
            });

        }

    },

    created: function () {

        var vue = this;

        if (!vue.$store.state.user.authenticated) {
            vue.$router.push('/login');
        } else {
            vue.$router.push('/home');
        }

        window.socket.on('public.info', function (data) {
            vue.$store.dispatch('addFeedback', {'type': 'info', 'message': data});
        });

        /**
         *  Socket Events
         *  we use the laravel class as the event to listen for
         */

        window.socket.on('App\\Events\\UserUpdated', function (data) {

            vue.$store.dispatch('addFeedback', {'type': 'announcement', 'message': data.message});

            vue.$http.post('/api/users/my-info').then( function(response) {
                vue.$store.dispatch('userInfo', response.data); 
            }, function(error) {
            
            });

        });

        window.socket.on('App\\Events\\UserRolesUpdated', function (data) {

            vue.$store.dispatch('addFeedback', {'type': 'announcement', 'message': data.message});

            vue.$http.post('/api/users/my-roles').then( function(response) {
                vue.$store.dispatch('userRoles', response.data); 
            }, function(error) {
                vue.$store.dispatch('addFeedback', {'type': 'error', 'message': 'There was an error loading your groups'});
            });

        });

        window.socket.on('App\\Events\\AuthAnnouncement', function (data) {
            vue.$store.dispatch('addFeedback', {'type': 'announcement', 'message': data.message});
        });

        window.socket.on('App\\Events\\UserCreated', function (data) {
            vue.$store.dispatch('addFeedback', {'type': 'announcement', 'message': data.message});
        });

        window.socket.on('App\\Events\\UserRemoved', function (data) {
            vue.$store.dispatch('addFeedback', {'type': 'announcement', 'message': data.message});
        });

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
