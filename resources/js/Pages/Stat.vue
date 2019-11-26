<template>

    <div class="content">

        <section>
            <div class="h1">{{ stat.id ? 'Edit' : 'Create' }} Stat {{ stat.stat_name }}</div>
        </section>

        <section>

            <form role="form" method="POST" :action="'/api' + $route.path" @submit.prevent="submit">

            <div class="form-block">
                <div class="form-label">
                    <label for="stat_name" class="label">Stat Name</label>
                </div>
                <div class="form-input">
                    <input id="stat_name" class="input" name="stat_name" v-model="stat.stat_name" required autofocus>
                </div>
            </div>

            <div class="form-block">
                <div class="form-label">
                </div>
                <div class="form-input">
                    <button type="submit" class="">Save Stat</button>
                </div>
            </div>

            </form>

        </section>

    </div>

</template>

<script>

    import Stat from '@/Mixins/Stat'

    export default {

        mixins: [Stat],

        data: function () {
            return {
            }
        },

        beforeMount() {
            let stat_id = this.$route.params.id;
            this.loadStat(stat_id);
        },

        methods: {
            //

            submit: function(e) {

                $('input.input-error').removeClass('input-error');

                let post_data = {
                    'id': this.stat.id,
                    'stat_name': this.stat.stat_name
                };

                this.$http.post(e.target.action, post_data).then( response => {

                    this.$store.dispatch('addFeedback', {'type': 'success', 'message': 'Saved Stat'});
                    this.$router.push('/stats');

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
        }
    };
</script>
