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
                                <input id="password" type="password" 
                                    class="input input-password" 
                                    name="password" 
                                    v-model="password" 
                                    placeholder="New password" 
                                    :required="show_password"
                                >
                            </div>
                        </div>
                    </div>

                    <div class="form-block">
                        <div class="form-label">
                            <label for="password_confirmation" class="label">Confirm Password</label>
                        </div>
                        <div class="form-input">
                            <input id="password_confirmation" type="password" 
                                class="input input-password" 
                                name="password_confirmation" 
                                v-model="password_confirmation" 
                                placeholder="Confirm password"
                                :required="show_password"
                            >
                        </div>
                    </div>

                </div>
            </transition>

            <div class="form-block">
                <div class="form-label">
                    <div class="button" @click="toggleShowPassword" v-if="user.id">Update Password</div>
                </div>
                <div class="form-input">
                    <button type="submit" class="">Save User</button>
                </div>
            </div>

            </form>

        </section>

        <transition name="fade">
            <div v-show="user.id" v-if="userHasRole('admin')">

                <section>
                    <div class="h2">Groups</h2>
                </section>

                <section>
                    <div class="form-block" v-for="role in roles">
                        <div class="form-label">
                            <label :for="role.id">{{ role.role_name }}</label>
                        </div>
                        <div class="form-input">
                            <input type="checkbox" 
                                :id="role.id" 
                                :value="role.id" 
                                @click="toggleRole" 
                                :checked="_.includes(groups, role.id)"
                            >
                        </div>
                    </div>
                </section>

            </div>
        </transition>

    </div>

</template>

<script>

    import UserMixins from './UserMixins'

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
                show_password: false,
                roles: [],
                groups: []
            }
        },

        mixins: [UserMixins],

        watch: {
            '$route': 'loadInfo'
        },

        methods: {

            toggleRole: function(e) {

                var vue = this;
                let val = parseInt(e.target.value);
                let user_id = vue.$route.params.id;
                if (!user_id) {
                    user_id = vue.$store.state.user.id;
                }

                let post_data = {
                    'role_id':  val
                }

                if (_.includes(this.groups, val)) {

                    // remove the role
                    let index = _.findIndex(this.groups, function(o) { return o == val; });
                    this.groups.splice(index, 1);


                    vue.$http.post('/api/users/remove-role/' + user_id, post_data).then( function(response) {

                        vue.$store.dispatch('addFeedback', {'type': 'success', 'message': 'Group saved'});

                    }, function(error) {
                    
                    });


                } else {

                    this.groups.push(val);

                    vue.$http.post('/api/users/save-role/' + user_id, post_data).then( function(response) {

                        vue.$store.dispatch('addFeedback', {'type': 'success', 'message': 'Group saved'});

                    }, function(error) {
                    
                    });

                }
            },

            loadInfo: function() {

                var vue = this;

                let user_id = vue.$route.params.id;
                if (!user_id) {
                    user_id = vue.$store.state.user.id;
                }
                if (user_id != 'create') {

                    vue.$http.post('/api/users/load/' + user_id).then( function(response) {
                        vue.user = response.data;
                    });

                } else {
                    this.show_password = true;
                }
            },

            loadRoles: function() {

                var vue = this;

                vue.$http.post('/api/roles').then( function(response) {
                    vue.roles = response.data;

                    let user_id = vue.$route.params.id;

                    if (!user_id) {
                        user_id = vue.$store.state.user.id;
                    }

                    if (user_id != 'create') {

                        vue.$http.post('/api/users/roles/' + user_id).then( function(response) {
                            vue.groups = response.data;
                        });

                    }

                });

            },

            submit: function(e) {

                var vue = this;

                $('input.input-error').removeClass('input-error');

                let post_data = {
                    'id': this.user.id,
                    'first_name': this.user.first_name,
                    'last_name': this.user.last_name,
                    'email': this.user.email,
                    'password': this.password,
                    'password_confirmation': this.password_confirmation
                };

                vue.$http.post(e.target.action, post_data).then( function(response) {

                    vue.$store.dispatch('addFeedback', {'type': 'success', 'message': 'Saved User'});
                    vue.$router.push('/users');

                }, function(error) {

                    // this needs to go into a function 
                    if (error.response.status == 422) {
                        for(let input in error.response.data) {

                            // we need to show feedback on the form itself
                            //$("input[name='" + input + "']").addClass('input-error');

                            //document.getElementById(input).classList.add('input-error');
                            $('input[name="' + input + '"]').addClass('input-error');

                            for (let info in error.response.data[input]) {
                                vue.$store.dispatch('addFeedback', {'type': 'error', 'message': error.response.data[input][info], 'input': input});
                            }
                        }
                    }

                    if (error.response.status == 500) {
                        vue.$store.dispatch('addFeedback', {'type': 'error', 'message': 'There was a server error'});
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
            this.loadRoles();
            this.loadInfo();
        },

        mounted() {
        }

    }
</script>
