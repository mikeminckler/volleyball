<template>

    <transition name="fade">
        <div class="menu" v-if="$store.state.activeTeam.id">
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
                _.forEach(roles, function(role) {
                    if (vue.userHasRole(role)) {
                        hasRole = true;
                    }
                });
                return hasRole;
            }

        },

    }

</script>
