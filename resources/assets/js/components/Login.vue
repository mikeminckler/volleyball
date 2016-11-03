<template>

    <div class="login-container">
        <div class="login">

            <form class="login" role="form" method="POST" action="/api/login" @submit.prevent="login">

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
            login: function(e) {
                let post_data = {
                    'email':    this.login.email,
                    'password': this.login.password,
                    'remember': this.login.remember
                };

                var self = this;

                self.$http.post(e.target.action, post_data).then( function(response) {

                    // Set authenticated and the JWT Token 
                    self.$store.dispatch('setToken', response.data.token);

                    window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + response.data.token;

                    self.$store.dispatch('addFeedback', {'type': 'success', 'message': 'Logged In'});

                    self.$http.post('/api/users/my-info').then( function(response) {
                        self.$store.dispatch('userInfo', response.data); 
                    }, function(error) {
                        self.$store.dispatch('addFeedback', {'type': 'error', 'message': 'There was an error loading your info'});
                    });

                    self.$http.post('/api/menu').then( function(response) {
                        self.$store.dispatch('menu', response.data); 
                    }, function(error) {
                        self.$store.dispatch('addFeedback', {'type': 'error', 'message': 'There was an error loading the menu'});
                    });

                    // load the home page now that we are logged in
                    if (self.$store.state.intended != '/login' && self.$store.state.intended.length > 0) {
                        self.$router.push(self.$store.state.intended);
                        self.$store.state.intended = '';
                    } else {
                        self.$router.push('/home');
                    }

                }, function(error) {

                    // login failed provide the feedback
                    self.$store.dispatch('addFeedback', {'type': 'error', 'message': error.response.data.error});

                    // HACK 
                    $('input.input-password').val('').focus();

                });


            }
        },

        mounted() {
            //console.log('Login ready.')
        }

    }
</script>
