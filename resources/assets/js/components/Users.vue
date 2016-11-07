<template>

    <div class="content">

        <section class="header">
            <div class="h1">Users</div>
            <div><router-link class="button" to="/users/create">Create User</router-link></div>
        </section>

        <section>

            <div class="row" v-for="user in users">
                <div class="column">
                    <router-link :to="{path: '/users/' + user.id}">{{ user.first_name + ' ' + user.last_name }}</router-link>
                </div>
                <div class="column">{{ user.email }}</div>
                <div class="column">
                    <a @click.prevent="remove" class="delete fa fa-times icon" :href="'/api/users/delete/' + user.id"></a>
                </div>
            </div>

        </section>

    </div>

</template>

<script>
    export default {

        data: function () {
            return {
                users: []
            }
        },

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
        }

    }
</script>
