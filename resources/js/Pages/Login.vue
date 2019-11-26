<template>

    <div class="flex flex-1 justify-center items-center">

        <div class="">
            <div class="input">
                <div class="label"><label for="email" class="label">E-Mail</label></div>
                <input id="email" class="input" name="email" v-model="email" required autofocus>
            </div>

            <div class="input">
                <div class="label"><label for="password" class="label">Password</label></div>
                <input id="password" type="password" class="input input-password" name="password" v-model="password" required>
            </div>

            <div class="input">
                <div class="button" @click="login">Login</div>
            </div>

            <div class="input">
                <label>
                    <input type="checkbox" name="remember" v-model="remember"> Remember Me
                </label>
            </div>

            <inertia-link class="forgot-password" href="/password/reset">Forgot Your Password?</inertia-link>

        </div>
    </div>

</template>

<script>

    import Layout from '@/Layout';

    export default {

        layout: Layout,

        data: function () {
            return {
                email: '',
                password: '',
                remember: ''
            }
        },


        mounted() {
            if (this.$page.cookies.login_email) {
                this.email = this.$page.cookies.login_email;
                this.remember = true;
            }
        },

        methods: {
            
            login: function() {

                let post_data = {
                    email:    this.email,
                    password: this.password,
                    remember: this.remember,
                };

                this.$http.post('/login', post_data).then( response => {

                    this.$store.dispatch('setUser', response.data.user);
                    this.$store.dispatch('addFeedback', {'type': 'success', 'message': 'Logged In'});
                    this.$inertia.visit('/select-team');

                }, error => {

                    // login failed provide the feedback
                    this.$store.dispatch('addFeedback', {'type': 'error', 'message': error.response.data.error});

                });


            }
        },

    }
</script>
