<template>

        <transition name="slide-down">
            <div class="menu" v-if="$store.state.activeTeam.id">
                <div class="home-link menu-item" v-if="$store.state.activeTeam.id">
                    <router-link to="/home" class="">Home</router-link>
                </div>
                <div class="menu-item" v-for="item in $store.state.menu" v-if="roleCheck(item.roles)">
                    <router-link :to="{path: '/' + item.url}">{{ item.name }}</router-link>
                </div>
            </div>

            <div class="menu" v-else>
                <div></div>
            </div>
        </transition>

</template>

<script>

    import UserMixins from './UserMixins'

    export default {

        mixins: [UserMixins],

        methods: {

            roleCheck(roles) {
                var hasRole = false;
                return this.userHasRole(roles);
            }

        },

    }

</script>
