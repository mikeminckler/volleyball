import Vue from 'vue';
//import VueRouter from 'vue-router';
import Vuex from 'vuex';

Vue.config.productionTip = false;

//Vue.use(VueRouter);
Vue.use(Vuex);

import lodash from "lodash";
Object.defineProperty(Vue.prototype, "$lodash", { value: lodash });

import axios from "axios";
Object.defineProperty(Vue.prototype, "$http", { value: axios });

import moment from "moment";
Object.defineProperty(Vue.prototype, "$moment", { value: moment });

import Echo from "laravel-echo"
window.Pusher = require('pusher-js');
Object.defineProperty(Vue.prototype, "$echo", { value: new Echo({
        broadcaster: 'pusher',
        key: process.env.MIX_PUSHER_APP_KEY,
        wsHost: process.env.MIX_WEBSOCKET_HOST,
        wsPort: 6001,
        disableStats: true,
        //encrypted: true,
    })
});

axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

Object.defineProperty(Vue.prototype, "$eventer", { value: new Vue() });

/*
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
*/

/*
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
*/

const store = new Vuex.Store({
    state: {
        activity: false,
        user: null,
        feedback: [],
        intended: '',
        menu: [],
        wsState: '',
        activeTeam: {}
    },

    getters: {
    },

    mutations: {

        setActiveTeam (state, team) {
            state.activeTeam = team;
        },

        removeActiveTeam (state) {
            state.activeTeam = {};
        },

        addFeedback (state, item) {
            state.feedback.push(item);
        },

        clearFeedback (state) {
            state.feedback = state.feedback.filter( function(item) {
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

        setWsState (state, wsState) {
            state.wsState = wsState;
        },

        setUser (state, user) {
            state.user = user;
        },

        removeUser (state) {
            state.user = {};
        },

        setMenu (state, menu) {
            state.menu = menu;
        },

        setActivity (state, boolean) {
            state.activity = boolean;
        },

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

        setUser({ commit, state }, user) {
            commit('setUser', user);
        },

        removeUser({commit, state}) {
            commit('removeUser');
        },

        addFeedback({ commit, state }, feedback) {
            
            var expire;

            if (feedback.message) {

                if (parseInt(feedback.expire) > 0) {
                    expire = feedback.expire;
                }

                if (expire == undefined) {
                    expire = new Date().getTime() + 4900;
                }
                
                let item = {
                    type: feedback.type,
                    message: feedback.message,
                    link: feedback.link,
                    expire: expire,
                    input: feedback.input,
                    assert: feedback.type + '|' + feedback.message,
                    key: [...Array(30)].map(() => Math.random().toString(36)[3]).join(''),
                };
                commit('addFeedback', item);

                // clear out any info and successes automatically
                
                setTimeout(function() {
                   store.dispatch('clearFeedback');
                }, 5000);

            }
           
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
        },

        setWsState({ commit, state }, wsState) {
            commit('setWsState', wsState);
        },

        setActivity({ commit, state }, boolean) {
            commit('setActivity', boolean);
        },


    }
});

/*
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
*/

import { InertiaApp } from '@inertiajs/inertia-vue'
Vue.use(InertiaApp)

const app = new Vue({
    el: "#app",
    store,

    render: h => h(InertiaApp, {
        props: {
          initialPage: JSON.parse(document.getElementById('app').dataset.page),
          resolveComponent: name => import(`@/Pages/${name}`).then(module => module.default),
        },
    }),

    mounted() {

        this.$store.dispatch('setWsState', this.$echo.connector.pusher.connection.state);
        this.$echo.connector.pusher.connection.bind('state_change', states => {
            this.$store.dispatch('setWsState', states.current);
        });
        
    },

});

google.charts.load('current', {'packages':['corechart']});
