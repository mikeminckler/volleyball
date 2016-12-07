<template>

        <transition name="slide-down">
            <div class="menu" v-if="$store.state.activeTeam.id">
                <div class="home-link menu-item" v-if="$store.state.activeTeam.id">
                    <router-link to="/home" class="">Home</router-link>
                </div>
                <div class="menu-item" v-for="item in $store.state.menu">
                    <router-link :to="{path: '/' + item.url}" v-if="roleCheck(item.roles)">{{ item.name }}</router-link>
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
                var vue = this;
                var hasRole = false;
                return vue.userHasRole(roles);
            }

        },

    }

</script>
