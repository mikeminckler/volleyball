<template>

    <div class="content">

        <h1>Users</h1>

        <div class="section">

            <div class="row" v-for="user in users">
                <div class="column">
                    <router-link :to="{path: '/users/' + user.id}">{{ user.first_name + ' ' + user.last_name }}</router-link>
                </div>
                <div class="column">
                    {{ user.email }}
                </div>
            </div>

        </div>

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
            this.$http.post('/api/users').then((response) => {
                this.users = response.data;
            });
        }

    }
</script>
