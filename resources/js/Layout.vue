<template>

    <div class="flex flex-col min-h-screen" dusk="app">

        <header class="flex shadow bg-indigo-600 text-white p-2 md:p-4 md:h-16 items-center" dusk="header">
            <div class="ml-2">
                <inertia-link class="text-white font-semibold tracking-wide" href="/login" dusk="login" v-if="!user">Login</inertia-link>
                <inertia-link class="text-white font-semibold tracking-wide" href="/logout" method="post" dusk="logout" v-if="user">Logout</inertia-link>
            </div>
        </header>

        <main class="flex-1 flex flex-col bg-gray-300 text-gray-700">
              <slot />
        </main>

        <footer class="md:flex items-center bg-indigo-600 p-4 text-gray-200 text-sm relative z-20">
            
        </footer>

        <feedback dusk="feedback"
            :errors="$page.errors" 
            :error="$page.error" 
            :success="$page.success"
        ></feedback>

    </div>

</template>

<script>

    export default  {

        components: {
            'feedback': () => import(/* webpackChunkName: "feedback" */ '@/Components/Feedback'),
        },

        mixins: [],

        data() {
            return {
                check: null,
                activity: _.throttle( function() {
                    this.setActivity();
                }, 5000), 
            }
        },

        computed: {
            user() {
                return this.$store.state.user;
            },
            connected() {
                return this.$store.state.wsState === 'connected';
            },
            csrf_token() {
                return this.$page.csrf_token;
            },
        },

        watch: {
            user() {
                if (this.user) {
                    if (this.user.id) {
                        this.check = setInterval(this.loginCheck, 60000); // every minute
                    } else {
                        clearInterval(this.check);
                    }
                } else {
                    clearInterval(this.check);
                }
            },
            csrf_token() {
                this.$echo.connector.pusher.config.auth.headers['X-CSRF-TOKEN'] = this.$page.csrf_token;
            }
        },

        mounted() {

            this.$echo.connector.pusher.config.auth.headers['X-CSRF-TOKEN'] = this.$page.csrf_token;

            if (!this.user && this.$page.auth.user) {
                this.$store.dispatch('setUser', this.$page.auth.user);
            }

            if (this.user && !this.$page.auth.user) {
                this.$store.dispatch('setUser', this.$page.auth.user);
            }

            const listener = event => {
                this.activity();
            };

            document.addEventListener('mousemove', listener);
            document.addEventListener('keyup', listener);
            document.addEventListener('click', listener);

            this.$once('hook:destroyed', () => {
                document.removeEventListener('mousemove', listener);
                document.removeEventListener('keyup', listener);
                document.removeEventListener('click', listener);
            });

        },

        methods: {

            setActivity: function() {
                if (this.$store.state.activity == false) {
                    this.$store.dispatch('setActivity', true);
                }
            },
        
            loginCheck: function() {

                if (this.$store.state.activity) {

                    this.$store.dispatch('setActivity', false);

                    this.$http.post('/session/update').then( response => {

                    }, error => {
                        //this.timeout();
                    });

                } else {

                    this.$http.post('/session/check').then( response => {

                    }, error => {
                        //this.timeout();
                    });

                }
            },

            timeout: function() {
                window.location.href = '/session/timeout';
            },

            reload() {
                window.location.reload();
            }
        
        },
    }

</script>
