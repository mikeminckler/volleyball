
const _ = require('lodash');

window.$ = window.jQuery = require('jquery');
require('./libs/jquery-ui');
require('./libs/timepicker');

const Vue = require('vue');
const VueRouter = require('vue-router');
const Vuex = require('vuex');

Vue.use(VueRouter);
Vue.use(Vuex);

const axios = require('axios');
//const fullCalendar = require('fullcalendar');
const moment = require('moment');

const io = require('socket.io-client');
window.socket = io('http://localhost:3000');

Vue.prototype.$http = axios;

Vue.component('feedback', require('./components/Feedback.vue'));
Vue.component('app-menu', require('./components/Menu.vue'));
Vue.component('autocomplete', require('./components/AutoComplete.vue'));
Vue.component('scoreboard', require('./components/Scoreboard.vue'));
Vue.component('room-list', require('./components/RoomList.vue'));
Vue.component('team-game-stats', require('./components/TeamGameStats.vue'));
Vue.component('team-game-chart', require('./components/TeamGameChart.vue'));
Vue.component('team-game-report', require('./components/TeamGameReport.vue'));
Vue.component('team-stat-setting', require('./components/TeamStatSetting.vue'));
Vue.component('team-players-stats-report', require('./components/TeamPlayersStatsReport.vue'));
Vue.component('team-players-list', require('./components/TeamPlayersList.vue'));
Vue.component('player-game-stat', require('./components/PlayerGameStat.vue'));
Vue.component('player-game-report', require('./components/PlayerGameReport.vue'));

const router = new VueRouter({
    mode: 'history',
    routes: [
        { path: '/login', component: require('./components/Login.vue') },
        { path: '/home', component: require('./components/Home.vue') },
        { path: '/select-team', component: require('./components/SelectTeam.vue') },

        { path: '/my-account', component: require('./components/User.vue') },
        { path: '/users', component: require('./components/Users.vue') },
        { path: '/users/:id', component: require('./components/User.vue') },
        { path: '/users/create', component: require('./components/User.vue') },

        { path: '/teams', component: require('./components/Teams.vue') },
        { path: '/teams/:id', component: require('./components/Team.vue') },
        { path: '/teams/create', component: require('./components/Team.vue') },
        { path: '/teams/games/:id', component: require('./components/TeamGames.vue') },
        { path: '/teams/players/:id', component: require('./components/TeamPlayers.vue') },

        { path: '/games', component: require('./components/Games.vue') },
        { path: '/games/:id', component: require('./components/Game.vue') },
        { path: '/games/create', component: require('./components/Game.vue') },
        { path: '/games/stats/:id', component: require('./components/GameStats.vue') },

        { path: '/players/stats/:id', component: require('./components/PlayerStatsReport.vue') },

        { path: '/stats', component: require('./components/Stats.vue') },
        { path: '/stats/:id', component: require('./components/Stat.vue') },
        { path: '/stats/create', component: require('./components/Stat.vue') },

        //{ path: '/not-found', component: require('./components/404.vue') },
        //{ path: '/*', redirect: '/not-found' }
    ]
});

router.beforeEach( function(to, from, next) {
    if (to.path != '/login') {
        if (store.state.user.authenticated) {

            store.dispatch('clearErrorsFeedback');

            if (to.path == '/select-team') {
                next();
            } else if (!store.state.activeTeam.id) {
                next('/select-team');
            } else { 
                next();
            }

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
        menu: [],
        activeTeam: {}
    },

    getters: {
        user_name: state => {
            return state.user.first_name + ' ' + state.user.last_name;
        }
    },

    mutations: {

        setActiveTeam (state, team) {
            state.activeTeam = team;
        },

        removeActiveTeam (state) {
            state.activeTeam = {};
        },

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
            state.user.roles = info.roles;
        },

        /*
        userRoles (state, info) {
            state.user.roles = info;
        },
        */

        menu (state, menu) {
            state.menu = menu;
        }

    },

    actions: {
        
        setActiveTeam({ commit, state }, team) {
            commit('setActiveTeam', team);
        },

        removeActiveTeam({ commit, state }) {
            commit('removeActiveTeam');
        },

        setToken({ commit, state }, token) {

            commit('setToken', token);

            // connect incase we had logged out before
            window.socket.connect();

            // authenticate our token
            window.socket.emit('authenticate', {token: token});
        },

        userRoles({ commit, state }, info) {
            commit('userRoles', info);
            app.$store.dispatch('addFeedback', {'type': 'info', 'message': 'Your groups have been loaded'});
            if (app.userHasRole('admin')) {
                window.socket.emit('join-room', 'auth.info');
            }
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

        menu({ commit, state }, menu) {
            commit('menu', menu);
            app.$store.dispatch('addFeedback', {'type': 'info', 'message': 'Your menu is ready'});
        }

    }
});

import UserMixins from './components/UserMixins'
import Helpers from './components/Helpers'

const app = new Vue({
    el: '#app',
    router,
    store,

    mixins: [UserMixins, Helpers],

    methods: {

        logout: function(e){
            var vue = this;
            vue.$http.post(e.target.action).then( function(response) {

                window.socket.emit('auth.info', store.getters.user_name + ' has disconnected');

                // call the action for the store update
                vue.$store.dispatch('removeToken').then( function() {
                    //window.socket.removeListener('auth.info');
                    window.socket.close();
                });

                vue.$router.push('/login');

                vue.$store.dispatch('addFeedback', {'type': 'success', 'message': 'Logged Out'});
                vue.$store.dispatch('removeActiveTeam');

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

        window.socket.on('auth.info', function (data) {
            app.$store.dispatch('addFeedback', {'type': 'info', 'message': data});
        }.bind(app));

        /**
         *  Socket Events
         *  we use the laravel class as the event to listen for
         */


        // User Events

        window.socket.on('App\\Events\\UserUpdated', function (data) {

            vue.$store.dispatch('addFeedback', {'type': 'announcement', 'message': data.message});

            vue.$http.post('/api/users/my-info').then( function(response) {
                vue.$store.dispatch('userInfo', response.data); 
            }, function(error) {
            
            });

        });

        /*
        window.socket.on('App\\Events\\UserRolesUpdated', function (data) {

            vue.$store.dispatch('addFeedback', {'type': 'announcement', 'message': data.message});

            vue.$http.post('/api/users/my-roles').then( function(response) {
                vue.$store.dispatch('userRoles', response.data); 
            }, function(error) {
                vue.$store.dispatch('addFeedback', {'type': 'error', 'message': 'There was an error loading your groups'});
            });

        });
        */

        window.socket.on('App\\Events\\AuthAnnouncement', function (data) {
            vue.$store.dispatch('addFeedback', {'type': 'announcement', 'message': data.message});
        });

        window.socket.on('App\\Events\\UserCreated', function (data) {
            vue.$store.dispatch('addFeedback', {'type': 'announcement', 'message': data.message});
        });

        window.socket.on('App\\Events\\UserRemoved', function (data) {
            vue.$store.dispatch('addFeedback', {'type': 'announcement', 'message': data.message});
        });


        // Team Events
        
        window.socket.on('App\\Events\\TeamCreated', function (data) {
            vue.$store.dispatch('addFeedback', {'type': 'announcement', 'message': data.message});
        });

        window.socket.on('App\\Events\\TeamRemoved', function (data) {
            vue.$store.dispatch('addFeedback', {'type': 'announcement', 'message': data.message});
        });


        // Game Events

        window.socket.on('App\\Events\\GameCreated', function (data) {
            vue.$store.dispatch('addFeedback', {'type': 'announcement', 'message': data.message, 'link': data.link});
        });

        window.socket.on('App\\Events\\GameRemoved', function (data) {
            vue.$store.dispatch('addFeedback', {'type': 'announcement', 'message': data.message});
        });

        // Room Events
        window.socket.on('room-info', function (data) {
            vue.$store.dispatch('addFeedback', {'type': 'announcement', 'message': data});
        });
    }

});

/*
window.axios.interceptors.request.use(function (config) {
    // Do something before request is sent
    app.showLoading();
    return config;
  }, function (error) {
    // Do something with request error
    return Promise.reject(error);
  });

window.axios.interceptors.response.use(function (response) {
    // Do something with response data
    app.hideLoading();
    return response;
  }, function (error) {
    // Do something with response error
    return Promise.reject(error);
  });
*/

google.charts.load('current', {'packages':['corechart']});

var supportsTouch = 'ontouchstart' in window || navigator.msMaxTouchPoints;

$(function(){

    /**
     * Date Time Picker using jQuery UI
     */

	$(document).on('focus', 'input.datepicker', function(){
		$(this).datepicker({
			dateFormat: 'yy-mm-dd',
			showAnim: 'slideDown',
            changeMonth: true,
            changeYear: true,
		});
	});


	$(document).on('focus', 'input.datetimepicker', function(){

		$(this).datetimepicker({
			dateFormat: 'yy-mm-dd',
			showAnim: 'slideDown',
			stepMinute: 5,
            changeMonth: true,
            changeYear: true,
		});
	});

	$(document).on('focus', 'input.timepicker', function() {
		$(this).timepicker({
			showAnim: 'slideDown',
			stepMinute: 5
		});
	});

    /** 
     * A little Jquery to set css vars for windows size
     * may be able to ditch all this if we dont need 
     * window size
     */

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
