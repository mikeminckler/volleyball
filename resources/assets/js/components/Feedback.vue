<template>

        <div class="feedback">
            <transition-group name="feedback" tag="div">
                <div class="feedback-item" 
                    v-bind:key="feedback" 
                    v-bind:class="feedback.type" 
                    v-for="(feedback, index) in $store.state.feedback" 
                    @click="$store.state.feedback.splice(index, 1)"
                >
                {{ feedback.message }}
                </div>
            </transition-group>
        </div>

</template>

<script>
    export default {


    }
</script>
