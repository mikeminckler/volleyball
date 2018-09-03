<template>

    <div class="content">

        <section class="header">
            <div class="h1">Users</div>
            <div v-if="userHasRole(['admin'])"><router-link class="button button-icon create" to="/users/create">Create User</router-link></div>
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
                        <div @click.prevent="remove(user.id)" class="delete icon">
                            <i class="fas fa-times"></i>
                        </div>
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

                this.$http.post('/api/users').then( response => {
                    this.users = response.data;
                });
            
            },

            remove: function(userId) {

                this.$http.post('/api/users/delete/' + userId).then( response => {
                    this.$store.dispatch('addFeedback', {'type': 'success', 'message': 'User Deleted'});
                    this.loadUsers();
                }, function (error) {
                
                });
            }
        },

        beforeMount() {
            this.loadUsers();
        },

        mounted() {
            
            window.socket.on('App\\Events\\UsersRefresh', data => {
                this.loadUsers();
            });

        },


        beforeDestroy() {
            window.socket.removeListener('App\\Events\\UsersRefresh');
        }


    }
</script>
