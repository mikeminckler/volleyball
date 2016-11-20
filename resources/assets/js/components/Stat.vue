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

        </section>

    </div>

</template>

<script>

    import StatMixins from './StatMixins'

    export default {

        mixins: [StatMixins],

        data: function () {
            return {
            }
        },

        beforeMount() {
            var vue = this;
            let stat_id = vue.$route.params.id;
            this.loadStat(stat_id);
        },

        methods: {
            //

            submit: function(e) {

                var vue = this;

                $('input.input-error').removeClass('input-error');

                let post_data = {
                    'id': this.stat.id,
                    'stat_name': this.stat.stat_name
                };

                vue.$http.post(e.target.action, post_data).then( function(response) {

                    vue.$store.dispatch('addFeedback', {'type': 'success', 'message': 'Saved Stat'});
                    vue.$router.push('/stats');

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
        }
    };
</script>
