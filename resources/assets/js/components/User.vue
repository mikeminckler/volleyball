<template>

    <div class="content">
        <section>

            <div class="h1">{{ user.id ? 'Edit' : 'Create' }} User {{ user.first_name + ' ' + user.last_name }}</div>

        </section>
        <section>

            <form role="form" method="POST" :action="'/api' + $route.path" @submit.prevent="submit">

            <div class="form-block">
                <div class="form-label">
                    <label for="first_name" class="label">First Name</label>
                </div>
                <div class="form-input">
                    <input id="first_name" class="input" name="first_name" v-model="user.first_name" required autofocus>
                </div>
            </div>

            <div class="form-block">
                <div class="form-label">
                    <label for="last_name" class="label">Last Name</label>
                </div>
                <div class="form-input">
                    <input id="last_name" class="input" name="last_name" v-model="user.last_name" required autofocus>
                </div>
            </div>

            <div class="form-block">
                <div class="form-label">
                    <label for="email" class="label">Email</label>
                </div>
                <div class="form-input">
                    <input id="email" class="input" name="email" v-model="user.email" required autofocus>
                </div>
            </div>

            <transition name="fade">
                <div v-show="show_password">

                    <div class="form-block">
                        <div class="form-label">
                            <label for="password" class="label">New Password</label>
                        </div>
                        <div class="form-input">
                            <div v-show="show_password">
                                <input id="password" type="password" class="input input-password" name="password" v-model="password" placeholder="New password" >
                            </div>
                        </div>
                    </div>

                    <div class="form-block">
                        <div class="form-label">
                            <label for="password_confirmation" class="label">Confirm Password</label>
                        </div>
                        <div class="form-input">
                            <input id="password_confirmation" type="password" class="input input-password" name="password_confirmation" v-model="password_confirmation" placeholder="Confirm password">
                        </div>
                    </div>

                </div>
            </transition>

            <div class="form-block">
                <div class="form-label">
                    <div class="button" @click="toggleShowPassword">Update Password</div>
                </div>
                <div class="form-input">
                    <button type="submit" class="">Save User</button>
                </div>
            </div>

            </form>

        </section>
    </div>

</template>

<script>
    export default {

        data: function () {
            return {
                user: {
                    id: '',
                    first_name: '',
                    last_name: '',
                    email: ''
                },
                password: '',
                password_confirmation: '',
                show_password: false
            }
        },

        watch: {
            '$route': 'loadInfo'
        },

        methods: {
            loadInfo: function() {
                let user_id = this.$route.params.id;
                if (!user_id) {
                    user_id = this.$store.state.user.id;
                }
                if (user_id != 'create') {
                    this.$http.post('/api/users/load/' + user_id).then((response) => {
                        this.user = response.data;
                    });
                }
            },

            submit: function(e) {

                let post_data = {
                    'id': this.user.id,
                    'first_name': this.user.first_name,
                    'last_name': this.user.last_name,
                    'email': this.user.email,
                    'password': this.password,
                    'password_confirmation': this.password_confirmation
                };

                this.$http.post(e.target.action, post_data).then((response) => {

                    this.$store.dispatch('addFeedback', {'type': 'success', 'message': 'Saved User'});
                    this.$router.push('/users');

                }, (response) => {

                    // this needs to go into a function 
                    if (response.status == 422) {
                        for(let input in response.data) {

                            // we need to show feedback on the form itself
                            //$("input[name='" + input + "']").addClass('input-error');

                            for (let error in response.data[input]) {
                                this.$store.dispatch('addFeedback', {'type': 'error', 'message': response.data[input][error], 'input': input});
                            }
                        }
                    }

                    if (response.status == 500) {
                        this.$store.dispatch('addFeedback', {'type': 'error', 'message': 'There was a server error'});
                    }

                });
            },

            toggleShowPassword: function(e) {
                this.password = '';
                this.password_confirmation = '';
                this.show_password = !this.show_password;
            }

        },

        beforeMount() {
            this.loadInfo();
        },

        mounted() {
            
        }

    }
</script>
