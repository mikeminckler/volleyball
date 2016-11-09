<template>

    <div class="menu">
        <div class="menu-item" v-for="item in $store.state.menu">
            <router-link :to="{path: '/' + item.url}" v-if="roleCheck(item.roles)">{{ item.name }}</router-link>
        </div>
    </div>

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
