<template>

        <div class="feedback">
            <transition-group name="feedback" tag="div">
                <div class="feedback-item" 
                    v-bind:key="feedback" 
                    v-bind:class="feedback.type" 
                    v-for="(feedback, index) in $store.state.feedback" 
                    @click="$store.state.feedback.splice(index, 1)"
                >
                <router-link v-if="feedback.link" :to="{ path: feedback.link }">{{ feedback.message }}</router-link>
                    <div v-else>{{ feedback.message }}</div>
                </div>
            </transition-group>
        </div>

</template>

<script>
    export default {


    }
</script>
