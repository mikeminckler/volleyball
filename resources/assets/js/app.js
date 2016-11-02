
require('./bootstrap');

window.io = require('socket.io-client');
//var socket = io('http://localhost:3000', {query: 'jwt=' + store.state.user.token}); 
var socket = io('http://localhost:3000');

const router = new VueRouter({
    routes: [
        { path: '/login', component: require('./components/Login.vue') },
        { path: '/home', component: require('./components/Home.vue') },
        { path: '/users', component: require('./components/Users.vue') },
        { path: '/users/:id', component: require('./components/User.vue') },
        //{ path: '/not-found', component: require('./components/404.vue') },
        //{ path: '/*', redirect: '/not-found' }
    ]
});

router.beforeEach((to, from, next) => {
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
            id: ''
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
            state.feedback = state.feedback.filter(function(item) {
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
            state.feedback = state.feedback.filter(function(item) {
                return item.type != 'error';
            });
        },

        userInfo (state, info) {
            //state.user.name = info.first_name + ' ' + info.last_name;
            state.user.first_name = info.first_name;
            state.user.last_name = info.last_name;
            state.user.email = info.email;
            state.user.id = info.id;
        },

        menu (state, menu) {
            state.menu = menu;
        }

    },

    actions: {
        setToken({ commit, state }, token) {

            commit('setToken', token);

            // connect incase we had logged out before
            socket.connect();

            // authenticate our token
            socket.emit('authenticate', {token: token});

            // add our listeners
            socket.on('auth.info', function (data) {
                app.$store.dispatch('addFeedback', {'type': 'info', 'message': data});
            }.bind(app));

        },
        
        userInfo({ commit, state }, info) {
            commit('userInfo', info);
            socket.emit('auth.info', store.getters.user_name + ' has connected');
        },

        removeToken({ commit, state }) {
            commit('removeToken');
            socket.emit('auth.info', store.getters.user_name + ' has disconnected');
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
        }

    }
});

// Send the jwt token when we talk to laravel
Vue.http.headers.common['Authorization'] = store.state.user.token;

const app = new Vue({
    el: '#app',
    router,
    store,

    methods: {
        logout: function(e){
            this.$http.post(e.target.action).then((response) => {

                // call the action for the store update
                this.$store.dispatch('removeToken').then(() => {
                    socket.removeListener('auth.info');
                    socket.close();
                });

                this.$router.push('/login');

                this.$store.dispatch('addFeedback', {'type': 'success', 'message': 'Logged Out'});

            }, (response) => {
                // we have timed out or our token is invalid so lets go to the login page
                
                this.$router.push('/login');
            });

        }
    },

    created: function () {

        if (!this.$store.state.user.authenticated) {
            this.$router.push('/login');
        } else {
            this.$router.push('/home');
        }

        socket.on('public.info', function (data) {
            this.$store.dispatch('addFeedback', {'type': 'info', 'message': data});
        }.bind(this));

        socket.on('App\\Events\\UserUpdated', function (data) {
            this.$store.dispatch('addFeedback', {'type': 'announcement', 'message': data.message});
        }.bind(this));

        socket.on('App\\Events\\AuthAnnouncement', function (data) {
            this.$store.dispatch('addFeedback', {'type': 'announcement', 'message': data.message});
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
