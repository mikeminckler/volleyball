<template>

        <div class="feedback">
            <transition-group name="feedback" tag="div">
                <div class="feedback-item" 
                    :key="feedback.message" 
                    :class="feedback.type" 
                    v-for="(feedback, index) in $store.state.feedback" 
                    @click="$store.state.feedback.splice(index, 1)"
                >
                    <div class="feedback-icon" v-if="feedback.type == 'error'">
                        <i class="fas fa-times"></i>
                    </div>
                    <div class="feedback-icon" v-if="feedback.type == 'success'">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="feedback-icon" v-if="feedback.type == 'info'">
                        <i class="fas fa-info"></i>
                    </div>
                    <div class="feedback-icon" v-if="feedback.type == 'announcement'">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <router-link v-if="feedback.link" :to="{ path: feedback.link }" class="feedback">{{ feedback.message }}</router-link>
                    <div v-else>{{ feedback.message }}</div>
                </div>
            </transition-group>
        </div>

</template>

<script>
    export default {


    }
</script>
