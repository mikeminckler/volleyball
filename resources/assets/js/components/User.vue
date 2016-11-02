<template>

<transition name="fade">
    <div class="section">

        <form class="my-account" role="form" method="POST" :action="'/api/users/store/' + user.id" @submit.prevent="submit">

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

        <div class="form-block">
            <div class="form-label">
                <label for="password" class="label">Update Password</label>
            </div>
            <div class="form-input">
                <input id="password" type="password" class="input input-password" name="password" v-model="password" >
            </div>
        </div>

        <div class="form-block">
            <div class="form-label"></div>
            <div class="form-input">
                <button type="submit" class="">Save User</button>
            </div>
        </div>

        </form>

    </div>
</transition>

</template>

<script>
    export default {

        data: function () {
            return {
                user: {},
                password: ''
            }
        },

        methods: {
            loadInfo: function() {
                this.$http.post('/api/users/show/' + this.$route.params.id).then((response) => {
                    this.user = response.data;
                });
            },

            submit: function(e) {

                let post_data = {
                    'id': this.user.id,
                    'first_name': this.user.first_name,
                    'last_name': this.user.last_name,
                    'email': this.user.email,
                    'password': this.password
                };

                this.$http.post(e.target.action, post_data).then((response) => {

                    this.$store.dispatch('addFeedback', {'type': 'success', 'message': 'Saved User'});
                    this.$router.push('/users');

                }, (response) => {

                    this.$store.dispatch('addFeedback', {'type': 'error', 'message': 'There was a problem saving your info'});

                });
            }

        },

        beforeMount() {
            this.loadInfo();
        }

    }
</script>
