<template>
    <font-awesome-icon :icon="icon"></font-awesome-icon> <span class="pl-2"><slot /></span>
</template>

<script setup>

    import '@fortawesome/fontawesome-svg-core/styles.css';
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

    import { library } from '@fortawesome/fontawesome-svg-core';
    import { config } from '@fortawesome/fontawesome-svg-core';

    import {
        faPlus,
    } from '@fortawesome/free-solid-svg-icons'

    defineProps({
        icon: { type: String, required: true },
    });

    config.autoAddCss = false;

    library.add(faPlus);
</script>
