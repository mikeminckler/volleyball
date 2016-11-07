<template>

    <div class="menu">
        <div class="menu-item" v-for="item in $store.state.menu">
            <router-link :to="{path: '/' + item.url}" v-if="roleCheck(item.roles)">{{ item.name }}</router-link>
        </div>
    </div>

</template>

<script>
    export default {

        methods: {

            roleCheck(roles) {
                var vue = this;
                var hasRole = false;
                _.forEach(roles, function(role) {
                    if (_.includes(vue.$store.state.user.roles, role)) {
                        hasRole = true;
                    }
                });
                return hasRole;
            }

        }

    }

</script>
