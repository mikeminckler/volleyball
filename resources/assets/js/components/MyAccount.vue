<template>

<transition name="fade">
    <div class="section">

        <form class="my-account" role="form" method="POST" action="/api/save-my-account" @submit.prevent="submit">

        <div class="form-block">
            <div class="form-label">
                <label for="first_name" class="label">First Name</label>
            </div>
            <div class="form-input">
                <input id="first_name" class="input" name="first_name" v-model="$store.state.user.first_name" required autofocus>
            </div>
        </div>

        <div class="form-block">
            <div class="form-label">
                <label for="last_name" class="label">Last Name</label>
            </div>
            <div class="form-input">
                <input id="last_name" class="input" name="last_name" v-model="$store.state.user.last_name" required autofocus>
            </div>
        </div>

        <div class="form-block">
            <div class="form-label">
                <label for="email" class="label">Email</label>
            </div>
            <div class="form-input">
                <input id="email" class="input" name="email" v-model="$store.state.user.email" required autofocus>
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
                <button type="submit" class="">Save My Info</button>
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
                password: ''
            }
        },

        methods: {
            submit: function(e) {
                let post_data = {
                    'first_name': this.$store.state.user.first_name,
                    'last_name': this.$store.state.user.last_name,
                    'email': this.$store.state.user.email,
                    'password': this.password
                };
                this.$http.post(e.target.action, post_data).then((response) => {

                    this.$store.dispatch('addFeedback', {'type': 'success', 'message': 'Your information has been saved'});
                    this.$router.push('/home');

                }, (response) => {

                    this.$store.dispatch('addFeedback', {'type': 'error', 'message': 'There was a problem saving your info'});

                });
            }
        }

    }
</script>
