<template>

    <div class="login-container">
        <div class="login">

            <form class="login" role="form" method="POST" action="/api/login" @submit.prevent="attempt">

                <div class="login-logo fa fa-book"></div>

            <div class="form-block">
                <div class="form-label">
                    <label for="email" class="label">E-Mail</label>
                </div>
                <div class="form-input">
                    <input id="email" class="input" name="email" v-model="login.email" required autofocus>
                </div>
            </div>

            <div class="form-block">
                <div class="form-label">
                    <label for="password" class="label">Password</label>
                </div>
                <div class="form-input">
                    <input id="password" type="password" class="input input-password" name="password" v-model="login.password" required>
                </div>
            </div>

            <div class="form-block">
                <div class="form-label"></div>
                <div class="form-input">
                    <button type="submit" class="login">
                        Login
                    </button>
                </div>
            </div>

            <div class="form-block">
                <div class="form-label"></div>
                <div class="form-input">
                    <label>
                        <input type="checkbox" name="remember" v-model="login.remember"> Remember Me
                    </label>
                </div>
            </div>

            <div class="form-block">
                <div class="form-label"></div>
                <div class="form-input">
                    <a class="forgot-password" href="/password/reset">Forgot Your Password?</a>
                </div>
            </div>

            </form>

        </div>
    </div>

</template>

<script>

    import axios from 'axios'

    export default {

        data: function () {
            return {
                login:{
                    email: '',
                    password: '',
                    remember: ''
                }
            }
        },

        methods: {
            
            loadMenu: function() {

                var vue = this;
                vue.$http.post('/api/menu').then( function(response) {
                    vue.$store.dispatch('menu', response.data); 
                }, function(error) {
                    vue.$store.dispatch('addFeedback', {'type': 'error', 'message': 'There was an error loading the menu'});
                });

            },

            attempt: function(e) {
                let post_data = {
                    'email':    this.login.email,
                    'password': this.login.password,
                    'remember': this.login.remember
                };

                var vue = this;

                vue.$http.post(e.target.action, post_data).then( function(response) {

                    // Set authenticated and the JWT Token 
                    vue.$store.dispatch('setToken', response.data.token);

                    axios.defaults.headers.common['Authorization'] = 'Bearer ' + response.data.token;

                    vue.$store.dispatch('addFeedback', {'type': 'success', 'message': 'Logged In'});

                    vue.$http.post('/api/users/my-info').then( function(response) {
                        vue.$store.dispatch('userInfo', response.data);
                        socket.emit('auth.info', vue.$store.getters.user_name + ' has connected');
                    }, function(error) {
                        vue.$store.dispatch('addFeedback', {'type': 'error', 'message': 'There was an error loading your info'});
                    });

                    /*
                    vue.$http.post('/api/users/my-roles').then( function(response) {

                        vue.$store.dispatch('userRoles', response.data); 

                    }, function(error) {
                        vue.$store.dispatch('addFeedback', {'type': 'error', 'message': 'There was an error loading your groups'});
                    });
                    */

                    vue.loadMenu();

                    // load the home page now that we are logged in
                    if (vue.$store.state.intended != '/login' && vue.$store.state.intended.length > 0) {
                        vue.$router.push(vue.$store.state.intended);
                        vue.$store.state.intended = '';
                    } else {
                        vue.$router.push('/home');
                    }

                }, function(error) {

                    // login failed provide the feedback
                    vue.$store.dispatch('addFeedback', {'type': 'error', 'message': error.response.data.error});

                    $('input.input-password').val('').focus();

                });


            }
        },

        mounted() {
            //console.log('Login ready.')
        }

    }
</script>
