<script setup>

const props = defineProps({
    score: { type: Object, required: true },
});

</script>
<template>
    <div class="flex items-baseline">
        <div class="pl-2 font-bold">{{ score.score }}</div>
        <div class="pl-1 text-xs opacity-70 font-semibold" v-if="score.attempts > 0">({{ score.attempts }})</div>
    </div>
</template>
