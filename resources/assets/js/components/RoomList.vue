<template>

    <div class="room-list">
        <transition-group 
            name="list" 
            tag="div"
            v-bind:css="false"
            v-on:before-enter="beforeEnter"
            v-on:enter="enter"
            v-on:leave="leave"
        >
            <div class="room-item"
                v-for="(user, index) in roomUsers"
                :key="user.id"
                :data-index="index"
            >
                {{ user.name }}
            </div>
        </transition-group>
    </div>

</template>

<script>

    import ListTransition from './ListTransition'
    export default {

        data: function () {
            return {
                roomUsers: [],
                connected: false
            }
        },

        mixins: [ListTransition],

        props: ['room'],

        watch: {
            room: function() {
                this.addListeners();        
            }
        },

        mounted () {
            this.addListeners();        
        },

        beforeDestroy() {
            window.socket.removeListener('update-room');
            window.socket.removeListener('room-list');
        },

        methods: {
            addListeners: function() {

                let room_prop = this.room;
                let vue = this;

                if (room_prop.match(/\d+/g) && vue.connected == false) {

                    window.socket.on('update-room', function (room) {
                        window.socket.emit('room-list', room);
                    });

                    window.socket.on('room-list', function (data) {
                        if (data.room == room_prop) {
                            vue.roomUsers = data.users;
                        }
                    });

                    window.socket.emit('room-list', room_prop);

                    vue.connected = true;

                }
            
            }
        }
    };
</script>
