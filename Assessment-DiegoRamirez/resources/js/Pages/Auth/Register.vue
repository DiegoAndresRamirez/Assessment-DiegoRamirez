<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';

const form = useForm({
    name: '',
    lastname: '',
    speciality: '',
    age: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Register" />

    <form @submit.prevent="submit" class="w-full max-w-md mx-auto mt-8 p-6 bg-white dark:bg-gray-800 shadow-md rounded-lg">
        <!-- Name -->
        <div>
            <InputLabel for="name" value="Name" />
            <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required autofocus autocomplete="name" />
            <InputError class="mt-2" :message="form.errors.name" />
        </div>

        <!-- Last Name -->
        <div class="mt-4">
            <InputLabel for="lastname" value="Last Name" />
            <TextInput id="lastname" v-model="form.lastname" type="text" class="mt-1 block w-full" required autocomplete="family-name" />
            <InputError class="mt-2" :message="form.errors.lastname" />
        </div>

        <!-- Speciality -->
        <div class="mt-4">
            <InputLabel for="speciality" value="Speciality" />
            <TextInput id="speciality" v-model="form.speciality" type="text" class="mt-1 block w-full" required autocomplete="organization-title" />
            <InputError class="mt-2" :message="form.errors.speciality" />
        </div>

        <!-- Age -->
        <div class="mt-4">
            <InputLabel for="age" value="Age" />
            <TextInput id="age" v-model="form.age" type="number" class="mt-1 block w-full" required min="0" autocomplete="bday" />
            <InputError class="mt-2" :message="form.errors.age" />
        </div>

        <!-- Email -->
        <div class="mt-4">
            <InputLabel for="email" value="Email" />
            <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" required autocomplete="username" />
            <InputError class="mt-2" :message="form.errors.email" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <InputLabel for="password" value="Password" />
            <TextInput id="password" v-model="form.password" type="password" class="mt-1 block w-full" required autocomplete="new-password" />
            <InputError class="mt-2" :message="form.errors.password" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <InputLabel for="password_confirmation" value="Confirm Password" />
            <TextInput id="password_confirmation" v-model="form.password_confirmation" type="password" class="mt-1 block w-full" required autocomplete="new-password" />
            <InputError class="mt-2" :message="form.errors.password_confirmation" />
        </div>

        <!-- Terms and Privacy Policy -->
        <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" class="mt-4">
            <InputLabel for="terms">
                <div class="flex items-center">
                    <Checkbox id="terms" v-model:checked="form.terms" name="terms" required />
                    <span class="ms-2">
                        I agree to the <Link href="route('terms.show')" class="underline text-sm text-gray-600 dark:text-gray-400">Terms of Service</Link> and <Link href="route('policy.show')" class="underline text-sm text-gray-600 dark:text-gray-400">Privacy Policy</Link>.
                    </span>
                </div>
            </InputLabel>
            <InputError class="mt-2" :message="form.errors.terms" />
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end mt-4">
            <Link :href="route('login')" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                Already registered?
            </Link>
            <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Register
            </PrimaryButton>
        </div>
    </form>
</template>
