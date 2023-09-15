<script setup>
import { router } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';
import { debounce } from 'lodash';

const props = defineProps({
    model: { type: String, required: true },
    modelValue: {},
    focus: { type: Boolean, default: false },
    placeholder: { type: String, default: 'Search' },
});

const emit = defineEmits(['update:modelValue', 'create']);

const terms = ref('');
const results = ref([]);
const input = ref();
const searched = ref(false);

watch(() => terms.value, () => {
    searched.value = false;
    if (terms.value.length > 2) {
        search();
    } else {
        results.value = [];
    }
});

const search = debounce(function() {
    axios.post(route(props.model + '.search'), { terms: terms.value }).then(response => {
        results.value = response.data;
        searched.value = true;
    });
}, 500);

onMounted(() => {
    if (props.focus) {
        input.value.focus();
    }
});

const selectResult = (result) => {
    emit('update:modelValue', result);
    results.value = [];
    terms.value = '';
}

const createResult = () => {
    emit('create', terms.value);
    results.value = [];
    terms.value = '';
}

</script>

<template>
    <div class="max-w-xs relative">
        <input v-model="terms" ref="input" :placeholder="placeholder + '...'" class="px-2 w-full border focus:outline-none focus:border-gray-400 rounded" />
        <div class="w-full text-sm absolute w-full">
            <div class="row" v-for="result in results" @click="selectResult(result)">{{ result.name }}</div>
            <div class="row flex items-center cursor-pointer" v-if="searched && !results.length && terms.length > 2" @click="createResult()">
                <FaIcon icon="fas fa-plus">Add {{ terms }}</FaIcon>
            </div>
        </div>
    </div>
</template>
