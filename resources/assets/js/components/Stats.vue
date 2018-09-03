<template>

    <div class="content">

        <section class="header">
            <div class="h1">Stats</div>
            <div v-if="userHasRole(['admin'])"><router-link class="button button-icon create" to="/stats/create">Create Stat</router-link></div>
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
                        <div @click.prevent="remove(stat.id)" class="delete icon">
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
            window.socket.on('App\\Events\\StatsRefresh', data => {
                this.loadStats();
            });
        },

        beforeDestroy() {
            window.socket.removeListener('App\\Events\\StatsRefresh');
        },

        beforeMount() {
            this.loadStats();
        },

        methods: {

            remove: function(statId) {

                this.$http.post('/api/stats/delete/' + statId).then( response => {

                    this.$store.dispatch('addFeedback', {'type': 'success', 'message': 'Stat Deleted'});
                    this.loadStats();

                }, function (error) {
                
                });
            }
        }
    };
</script>
