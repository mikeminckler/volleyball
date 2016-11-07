
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

/**
 * We may need to catch timeout errors here
 
axios.interceptors.response.use(function (response) {
    // Do something with response data
    return response;
}, function (error) {
    // Do something with response error
    return Promise.reject(error);
});

*/
