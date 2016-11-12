<template>

    <div class="content">

        <section class="header">
            <div class="h1">Users</div>
            <div v-if="userHasRole('admin')"><router-link class="button" to="/users/create">Create User</router-link></div>
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
                <div class="row" 
                    v-for="(user, index) in users"
                    :key="user.id"
                    :data-index="index"
                >
                    <div class="column">
                        <router-link :to="{path: '/users/' + user.id}">{{ user.first_name + ' ' + user.last_name }}</router-link>
                    </div>
                    <div class="column">{{ user.email }}</div>
                    <div class="column">
                        <a @click.prevent="remove" class="delete fa fa-times icon" :href="'/api/users/delete/' + user.id"></a>
                    </div>
                </div>
            </transition-group>

        </section>

    </div>

</template>

<script>

    import UserMixins from './UserMixins'
    import ListTransition from './ListTransition'

    export default {

        data: function () {
            return {
                users: []
            }
        },

        mixins: [UserMixins, ListTransition],

        methods: {

            loadUsers: function() {

                var vue = this;
                vue.$http.post('/api/users').then( function(response) {
                    vue.users = response.data;
                });
            
            },

            remove: function(e) {

                var vue = this;
                
                vue.$http.post(e.target.href).then( function(response) {

                    vue.$store.dispatch('addFeedback', {'type': 'success', 'message': 'User Deleted'});

                    vue.loadUsers();

                }, function (error) {
                
                });
            }
        },

        beforeMount() {
            this.loadUsers();
        },

        mounted() {
            
            var vue = this;

            window.socket.on('App\\Events\\UsersRefresh', function (data) {
                vue.loadUsers();
            });

        }


    }
</script>
