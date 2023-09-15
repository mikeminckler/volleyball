<script setup>

import { useForm } from '@inertiajs/vue3';

import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ActionMessage from '@/Components/ActionMessage.vue';

const props = defineProps({
    user: { type: Object, required: true },
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    nickname: props.user.nickname,
});

const updateUser = () => {
    form.post(route('users.update', { id: props.user.id }));
}

</script>

<template>

    <div class="max-w-xl">
        <form @submit.prevent="updateUser()" class="mt-2">
            <!-- Name -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="name" value="Name" />
                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="name"
                />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>

            <!-- Name -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="nickname" value="Nickname" />
                <TextInput
                    id="nickname"
                    v-model="form.nickname"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="nickname"
                />
                <InputError :message="form.errors.nickname" class="mt-2" />
            </div>

            <!-- Email -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    autocomplete="username"
                />
                <InputError :message="form.errors.email" class="mt-2" />
            </div>

            <div class="mt-4 flex items-center">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Save
                </PrimaryButton>
                <ActionMessage :on="form.recentlySuccessful" class="ml-3">
                    Saved
                </ActionMessage>
            </div>

        </form>
    </div>

</template>
