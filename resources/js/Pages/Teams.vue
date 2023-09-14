<script setup>
import { useForm } from '@inertiajs/vue3';

import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    teams: { type: Array, required: true },
});

const form = useForm({
    name: '',
});

const submit = () => {
    form.post(route('teams.create'), {
        onSuccess: () => form.reset('name'),
    });
};

</script>

<template>
    <Head title="Teams" />

    <div class="">

        <h1>Teams</h1>

        <div class="mt-4">
            <div class="row" v-for="team in teams">{{ team.name }}</div>
        </div>

        <div class="max-w-2xl mt-8">

            <h2>Create Team</h2>

            <form @submit.prevent="submit" class="mt-2">
                <div>
                    <InputLabel for="name" value="Name" />
                    <TextInput
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        autofocus
                        autocomplete="name"
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="flex items-center mt-4">
                    <PrimaryButton class="" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Create Team
                    </PrimaryButton>
                </div>

            </form>

        </div>
    </div>

</template>
