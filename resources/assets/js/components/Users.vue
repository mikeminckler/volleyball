<template>

    <div class="content">

        <section>

            <div class="h1">Users</div>
            <router-link class="button" to="/users/create">Create User</router-link>

        </section>

        <section>

            <div class="row" v-for="user in users">
                <div class="column">
                    <router-link :to="{path: '/users/' + user.id}">{{ user.first_name + ' ' + user.last_name }}</router-link>
                </div>
                <div class="column">
                    {{ user.email }}
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

        beforeMount() {
            var self = this;
            self.$http.post('/api/users').then( function(response) {
                self.users = response.data;
            });
        }

    }
</script>
