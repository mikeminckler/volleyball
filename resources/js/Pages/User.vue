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
                    <input id="first_name" class="input" name="first_name" v-model="user.first_name" required autofocus >
                </div>
            </div>

            <div class="form-block">
                <div class="form-label">
                    <label for="common_name" class="label">Nickname</label>
                </div>
                <div class="form-input">
                    <input id="common_name" class="input" name="common_name" v-model="user.common_name" >
                </div>
            </div>

            <div class="form-block">
                <div class="form-label">
                    <label for="last_name" class="label">Last Name</label>
                </div>
                <div class="form-input">
                    <input id="last_name" class="input" name="last_name" v-model="user.last_name" required >
                </div>
            </div>

            <div class="form-block">
                <div class="form-label">
                    <label for="email" class="label">Email</label>
                </div>
                <div class="form-input">
                    <input id="email" class="input" name="email" v-model="user.email" required >
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

        <div v-show="user.id" v-if="userHasRole(['admin'])">

            <section>
                <div class="h2">Teams</div>
            </section>

            <section>

                <transition-group 
                    name="list" 
                    tag="div"
                    v-bind:css="false"
                    v-on:before-enter="beforeEnter"
                    v-on:enter="enter"
                    v-on:leave="leave"
                >
                    <div class="row" v-for="(role, index) in user.roles"
                        :key="role.id"
                        :data-index="index"
                    >
                    
                        <div class="column">{{ role.team.team_name }}</div>
                        <div class="column">{{ role.name }}</div>
                        <div class="column icon">
                            <div class="icon" @click="removeRole(role)">
                                <i class="fas fa-times"></i>
                            </div> 
                        </div>

                    </div>

                </transition-group>

            </section>

            <section>

                <div class="h3">Add Role</div>

                <div class="form-block">
                    <div class="form-label">
                        <label for="add_team_id" class="label">Team</label>
                    </div>
                    <div class="form-input">
                        <autocomplete object="teams" name="add_team_id" afterSearching="" :clear="clear"></autocomplete>
                    </div>
                </div>

                <div class="form-block">
                    <div class="form-label">
                        <label for="add_role_id" class="label">Role</label>
                    </div>
                    <div class="form-input">
                        <autocomplete object="roles" name="add_role_id" afterSearching="" :clear="clear"></autocomplete>
                    </div>
                </div>

                <div class="row">
                </div>

                <div class="form-block">
                    <div class="form-label">
                    </div>
                    <div class="form-input">
                        <div class="submit button" @click="addRoleToUser()">Add Role</div>
                    </div>
                </div>

            </section>

        </div>

    </div>

</template>

<script>

    import User from '@/Mixins/User'
    import Helpers from '@/Mixins/Helpers'

    export default {

        data: function () {
            return {
                user: {
                    id: '',
                    first_name: '',
                    common_name: '',
                    last_name: '',
                    email: '',
                    teams: []
                },
                password: '',
                password_confirmation: '',
                show_password: false,
                clear: false,
                //roles: [],
                //groups: [],
            }
        },

        mixins: [User, Helpers],

        watch: {
            '$route': 'loadInfo'
        },

        methods: {


            removeRole: function(role) {

                let user_id = this.$route.params.id;
                let post_data = {
                    'role_id': role.id,
                    'team_id': role.team.id
                }

                this.$http.post('/api/users/remove-role/' + user_id, post_data).then( response => {

                    this.$store.dispatch('addFeedback', {'type': 'success', 'message': 'Removed group'});
                    this.loadInfo();

                }, error => {
                
                });

            },

            addRoleToUser: function() {

                let user_id = this.$route.params.id;
                let post_data = {
                    'team_id': document.getElementById("add_team_id").value,
                    'role_id': document.getElementById("add_role_id").value
                };

                this.clear = true;   

                this.$http.post('/api/users/save-role/' + user_id, post_data).then( response => {

                    this.$store.dispatch('addFeedback', {'type': 'success', 'message': 'Group saved'});
                    this.loadInfo();
                    this.clear = false;   

                }, error => {
                
                });


            },


            loadInfo: function() {

                let user_id = this.$route.params.id;

                if (!user_id) {
                    user_id = this.$store.state.user.id;
                }

                if (user_id != 'create') {

                    this.$http.post('/api/users/load/' + user_id).then( response => {
                        this.user = response.data;
                    });

                } else {
                    this.show_password = true;
                }
            },


            submit: function(e) {

                $('input.input-error').removeClass('input-error');

                let post_data = {
                    'id': this.user.id,
                    'first_name': this.user.first_name,
                    'common_name': this.user.common_name,
                    'last_name': this.user.last_name,
                    'email': this.user.email,
                    'password': this.password,
                    'password_confirmation': this.password_confirmation
                };

                this.$http.post(e.target.action, post_data).then( response => {

                    this.$store.dispatch('addFeedback', {'type': 'success', 'message': 'Saved User'});
                    this.$router.push('/users/' + response.data.id);

                }, error => {

                    if (error.response.status == 422) {

                        this.$lodash.each(error.response.data.errors, (errors, field) => {
                            $('input[name="' + field + '"]').addClass('input-error');
                            this.$lodash.each(errors, error => {
                                this.$store.dispatch('addFeedback', {'type': 'error', 'message': error, 'input': field});
                            });
                        });

                    }

                    if (error.response.status == 500) {
                        this.$store.dispatch('addFeedback', {'type': 'error', 'message': 'There was a server error'});
                    }

                });
            },

            toggleShowPassword: function(e) {
                this.password = '';
                this.password_confirmation = '';
                this.show_password = !this.show_password;
            },

        },

        beforeMount() {
            this.loadInfo();
        },


    }
</script>
