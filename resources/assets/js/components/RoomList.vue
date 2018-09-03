<template>

    <div class="room-list-container" :class="{ hidden: hidden }">

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

        <div class="room-list-button-container">
            <div class="room-list-button" @click="hidden = !hidden">{{ roomUsers.length }}</div>
        </div>

    </div>

</template>

<script>

    import ListTransition from './ListTransition'
    export default {

        data: function () {
            return {
                roomUsers: [],
                connected: false,
                hidden: true
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

                if (room_prop.match(/\d+/g) && this.connected == false) {

                    window.socket.on('update-room', room => {
                        window.socket.emit('room-list', room);
                    });

                    window.socket.on('room-list', data => {
                        if (data.room == room_prop) {
                            this.roomUsers = data.users;
                        }
                    });

                    window.socket.emit('room-list', room_prop);

                    this.connected = true;

                }
            
            }
        }
    };
</script>
