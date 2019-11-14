window.$ = window.jQuery = require('jquery');
require('./libs/jquery-ui');
require('./libs/timepicker');

var Vue = require('vue');
import VueRouter from 'vue-router';
import Vuex from 'vuex';

Vue.use(VueRouter);
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

import Feedback from './components/Feedback.vue';
Vue.component('feedback', Feedback);
import Menu from './components/Menu.vue';
Vue.component('app-menu', Menu);
import AutoComplete from './components/AutoComplete.vue';
Vue.component('autocomplete', AutoComplete);
import Scoreboard from './components/Scoreboard.vue';
Vue.component('scoreboard', Scoreboard);
import RoomList from './components/RoomList.vue';
Vue.component('room-list', RoomList);
import TeamGameStats from './components/TeamGameStats.vue';
Vue.component('team-game-stats', TeamGameStats);
import TeamGameChart from './components/TeamGameChart.vue';
Vue.component('team-game-chart', TeamGameChart);
import TeamGameReport from './components/TeamGameReport.vue';
Vue.component('team-game-report', TeamGameReport);
import TeamGamesList from './components/TeamGamesList.vue';
Vue.component('team-games-list', TeamGamesList);
import TeamStatSetting from './components/TeamStatSetting.vue';
Vue.component('team-stat-setting', TeamStatSetting);
import TeamPlayersStatsReport from './components/TeamPlayersStatsReport.vue';
Vue.component('team-players-stats-report', TeamPlayersStatsReport);
import TeamPlayersList from './components/TeamPlayersList.vue';
Vue.component('team-players-list', TeamPlayersList);
import PlayerGameStat from './components/PlayerGameStat.vue';
Vue.component('player-game-stat', PlayerGameStat);
import PlayerGameReport from './components/PlayerGameReport.vue';
Vue.component('player-game-report', PlayerGameReport);

import DateTimePicker from './components/DateTimePicker.vue';
Vue.component('datetimepicker', DateTimePicker);


import Login from './components/Login.vue';
import Home from './components/Home.vue';
import SelectTeam from './components/SelectTeam.vue';
import User from './components/User.vue';
import Users from './components/Users.vue';
import Teams from './components/Teams.vue';
import Team from './components/Team.vue';
import TeamGames from './components/TeamGames.vue';
import TeamPlayers from './components/TeamPlayers.vue';
import Games from './components/Games.vue';
import Game from './components/Game.vue';
import GameStats from './components/GameStats.vue';
import PlayerStatsReport from './components/PlayerStatsReport.vue';
import Stats from './components/Stats.vue';
import Stat from './components/Stat.vue';

var router = new VueRouter({
    mode: 'history',
    routes: [
        { path: '/login', component: Login},
        { path: '/home', component: Home },
        { path: '/select-team', component: SelectTeam },

        { path: '/my-account', component: User },
        { path: '/users', component: Users },
        { path: '/users/:id', component: User },
        { path: '/users/create', component: User },

        { path: '/teams', component: Teams },
        { path: '/teams/:id', component: Team },
        { path: '/teams/create', component: Team },
        { path: '/teams/manage-team', component: Team },
        { path: '/teams/games/:id', component: TeamGames },
        { path: '/teams/players/:id', component: TeamPlayers },

        { path: '/games', component: Games },
        { path: '/games/:id', component: Game },
        { path: '/games/create', component: Game },
        { path: '/games/stats/:id', component: GameStats },

        { path: '/players/stats/:id', component: PlayerStatsReport },

        { path: '/stats', component: Stats },
        { path: '/stats/:id', component: Stat },
        { path: '/stats/create', component: Stat },

        //{ path: '/not-found', component: require('./components/404.vue') },
        //{ path: '/*', redirect: '/not-found' }
    ]
});

router.beforeEach( function(to, from, next) {

    var auth = true;

    //if ( to.path == '/login' || lodash.includes(to.path, '/players/stats/')) {
    if ( to.path == '/login' ) {
        auth = false;
    }

    if (auth) {
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

var store = new Vuex.Store({
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
            clearInterval(window.loginCheck);
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

        removeUser (state) {
            state.user = {};
        },

        /*
        userRoles (state, info) {
            state.user.roles = info;
        },
        */

        setMenu (state, menu) {
            state.menu = menu;
        }

    },

    actions: {
        
        setActiveTeam({ commit, state }, team) {

            if (state.activeTeam.id != team.id) {
                if (state.activeTeam.id) {
                    window.socket.emit('leave-room', 'team.' + state.activeTeam.id);
                }
                window.socket.emit('join-room', 'team.' + team.id);
            }
            commit('setActiveTeam', team);
            app.setTeamSessionId(team.id);
            window.localStorage.teamId = team.id;
        },

        removeActiveTeam({ commit, state }) {
            commit('removeActiveTeam');
            window.localStorage.removeItem('teamId');
        },

        setToken({ commit, state }, token) {

            commit('setToken', token);

            // connect incase we had logged out before
            window.socket.connect();

            // authenticate our token
            window.socket.emit('authenticate', {token: token});

            window.localStorage.jwt = token;

            axios.defaults.headers.common['Authorization'] = 'Bearer ' + token;

            window.loginCheck = setInterval(function() { app.loginCheck() }, 60000);
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

        removeUser({commit, state}) {
            commit('removeUser');
        },

        removeToken({ commit, state }) {
            commit('removeToken');
            window.localStorage.removeItem('jwt');
            clearInterval(window.loginCheck);
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

        setMenu({ commit, state }, menu) {
            commit('setMenu', menu);
            app.$store.dispatch('addFeedback', {'type': 'info', 'message': 'Your menu is ready'});
        }

    }
});

import UserMixins from './components/UserMixins'
import Helpers from './components/Helpers'

var app = new Vue({
    el: '#app',
    router,
    store,

    mixins: [UserMixins, Helpers],

	watch: {
	
		userId() {
            if (this.userId > 0) {
                app.loadMenu();
            }
		}
	
	},

	computed: {
		userId() {
			return this.$store.state.user.id;
		}
	},

    methods: {

        selectNewTeam: function() {
            this.$store.dispatch('removeActiveTeam');
            this.$router.push('/select-team');
        },

        setTeamSessionId: function(team_id) {
        
            this.$http.post('/api/set-team/' + team_id).then( response => {
                this.$store.dispatch('addFeedback', {'type': 'success', 'message': response.data.success});
            }, error => {
                this.$store.dispatch('addFeedback', {'type': 'error', 'message': 'Error selecting a team'});
            });

        },

        logout: function(e){

            this.$http.post(e.target.action).then( response => {

                window.socket.emit('auth.info', store.getters.user_name + ' has disconnected');

                this.$store.dispatch('removeUser');
                // call the action for the store update
                this.$store.dispatch('removeToken').then( function() {
                    window.socket.close();
                });

                this.$store.dispatch('addFeedback', {'type': 'success', 'message': 'Logged Out'});
                this.$store.dispatch('removeActiveTeam');
                this.$router.push('/login');

            }, error => {
                // we have timed out or our token is invalid so lets go to the login page
                this.$router.push('/login');
            });

        }, 

        loginCheck: function() {
        
            this.$http.post('/api/login-check').then( response => {

                if (response.data.status == 'timeout') {
                    this.timeout();
                }

            }, error => {

                this.timeout();

            });

        },

        timeout: function() {

            this.$store.dispatch('removeToken').then( () => {
                window.socket.close();
            });

            this.$store.dispatch('addFeedback', {'type': 'error', 'message': 'Your session has timed out'});
            this.$store.dispatch('removeActiveTeam');
            this.$router.push('/login');

        },


		loadMenu: function() {

			this.$http.post('/api/menu').then( response => {
				this.$store.dispatch('setMenu', response.data); 
			}, error => {
				this.$store.dispatch('addFeedback', {'type': 'error', 'message': 'There was an error loading the menu'});
			});

		},

    },

    mounted() {
        this.$router.push('/login');
    },

    created: function () {

        window.socket.on('public.info', data => {
            this.$store.dispatch('addFeedback', {'type': 'info', 'message': data});
        });

        window.socket.on('auth.info', data => {
            this.$store.dispatch('addFeedback', {'type': 'info', 'message': data});
        });

        /**
         *  Socket Events
         *  we use the laravel class as the event to listen for
         */


        // User Events

        window.socket.on('App\\Events\\UserUpdated', data => {

            this.$store.dispatch('addFeedback', {'type': 'announcement', 'message': data.message});

            this.$http.post('/api/users/my-info').then( response => {
                this.$store.dispatch('userInfo', response.data); 
            }, error => {
            
            });

        });

        window.socket.on('App\\Events\\AuthAnnouncement', data => {
            this.$store.dispatch('addFeedback', {'type': 'announcement', 'message': data.message});
        });

        window.socket.on('App\\Events\\UserCreated', data => {
            this.$store.dispatch('addFeedback', {'type': 'announcement', 'message': data.message});
        });

        window.socket.on('App\\Events\\UserRemoved', data => {
            this.$store.dispatch('addFeedback', {'type': 'announcement', 'message': data.message});
        });


        // Team Events
        
        window.socket.on('App\\Events\\TeamCreated', data => {
            this.$store.dispatch('addFeedback', {'type': 'announcement', 'message': data.message});
        });

        window.socket.on('App\\Events\\TeamRemoved', data => {
            this.$store.dispatch('addFeedback', {'type': 'announcement', 'message': data.message});
        });


        // Game Events

        window.socket.on('App\\Events\\GameCreated', data => {
            this.$store.dispatch('addFeedback', {'type': 'announcement', 'message': data.message, 'link': data.link});
        });

        window.socket.on('App\\Events\\GameRemoved', data => {
            this.$store.dispatch('addFeedback', {'type': 'announcement', 'message': data.message});
        });

    }

});

google.charts.load('current', {'packages':['corechart']});

var supportsTouch = 'ontouchstart' in window || navigator.msMaxTouchPoints;

$(function(){

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

});

