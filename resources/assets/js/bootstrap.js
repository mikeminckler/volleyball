
window._ = require('lodash');

window.$ = window.jQuery = require('jquery');
//require('bootstrap-sass');

window.Vue = require('vue');
//window.VueResource = require('vue-resource');
window.VueRouter = require('vue-router');
window.Vue.use(window.VueRouter);
window.Vuex = require('vuex');
//window.Vue.use(window.Vuex);
window.axios = require('axios');

/*
window.axios.interceptors.request.use(function (config) {
    // Do something before request is sent
    return config;
}, function (error) {
    // Do something with request error
    return Promise.reject(error);
});


Vue.http.interceptors.push((request, next) => {
    request.headers.set('X-CSRF-TOKEN', Laravel.csrfToken);

    next();
});
*/

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from "laravel-echo"

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: 'your-pusher-key'
// });
