<template>

    <div class="content">

        <section class="header">
            <div class="h1">Stats</div>
            <div v-if="userHasRole('admin')"><router-link class="button" to="/stats/create">Create Stat</router-link></div>
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
                v-for="(stat, index) in stats"
                :key="stat.id"
                :data-index="index"
            >
                    <div class="column">
                        <router-link :to="{path: '/stats/' + stat.id}">{{ stat.stat_name }}</router-link>
                    </div>

                    <div class="column">
                        <a @click.prevent="remove" class="delete fa fa-times icon" :href="'/api/stats/delete/' + stat.id"></a>
                    </div>
                </div>
            </transition-group>

        </section>

    </div>
</template>

<script>

    import UserMixins from './UserMixins'
    import StatMixins from './StatMixins'
    import ListTransition from './ListTransition'

    export default {

        mixins: [UserMixins, ListTransition, StatMixins],

        data: function () {
            return {
                stats: []
            }
        },

        props: {
            //
        },

        computed: {
            //
        },

        created () {
            //
        },

        mounted () {
            var vue = this;

            window.socket.on('App\\Events\\StatsRefresh', function (data) {
                vue.loadStats();
            });
        },

        beforeDestroy() {
            window.socket.removeListener('App\\Events\\StatsRefresh');
        },

        beforeMount() {
            this.loadStats();
        },

        methods: {

            remove: function(e) {

                var vue = this;
                
                vue.$http.post(e.target.href).then( function(response) {

                    vue.$store.dispatch('addFeedback', {'type': 'success', 'message': 'Stat Deleted'});

                    vue.loadStats();

                }, function (error) {
                
                });
            }
        }
    };
</script>
