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
                this.$http.post(e.target.action, post_data).then((response) => {

                    // Set authenticated and the JWT Token 
                    this.$store.dispatch('setToken', response.data.token);

                    window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + response.data.token;

                    this.$store.dispatch('addFeedback', {'type': 'success', 'message': 'Logged In'});

                    this.$http.post('/api/users/my-info').then((response) => {
                        this.$store.dispatch('userInfo', response.data); 
                    }, (response) => {
                    
                    });

                    this.$http.post('/api/menu').then((response) => {
                        this.$store.dispatch('menu', response.data); 
                    }, (response) => {
                    
                    });

                    // load the home page now that we are logged in
                    if (this.$store.state.intended != '/login' && this.$store.state.intended.length > 0) {
                        this.$router.push(this.$store.state.intended);
                        this.$store.state.intended = '';
                    } else {
                        this.$router.push('/home');
                    }

                }, (response) => {

                    // login failed provide the feedback
                    this.$store.dispatch('addFeedback', {'type': 'error', 'message': response.data.error});

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
