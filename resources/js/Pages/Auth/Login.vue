<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

const demoAccounts = [
    { role: 'Admin', email: 'admin@kominfo.go.id', password: 'password' },
    { role: 'Operator Sekretariat', email: 'sekretariat@kominfo.go.id', password: 'password' },
    { role: 'Operator APTIKA', email: 'aptika@kominfo.go.id', password: 'password' },
    { role: 'Operator IKP', email: 'ikp@kominfo.go.id', password: 'password' },
    { role: 'Pimpinan', email: 'kadis@kominfo.go.id', password: 'password' },
];

const fillDemo = (account) => {
    form.email = account.email;
    form.password = account.password;
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div class="mb-10">
            <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Selamat Datang</h2>
            <p class="mt-2 text-sm text-gray-600">
                Silakan masukkan kredensial Anda untuk mengakses sistem.
            </p>
        </div>

        <div v-if="status" class="mb-4 p-4 rounded-lg bg-green-50 border border-green-200 text-sm font-medium text-green-700">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <InputLabel for="email" value="Email Address" class="text-gray-700 font-medium" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="nama@perusahaan.com"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="password" value="Password" class="text-gray-700 font-medium" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center cursor-pointer">
                    <Checkbox name="remember" v-model:checked="form.remember" class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                    <span class="ms-2 text-sm text-gray-600">Ingat Saya</span>
                </label>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm font-medium text-indigo-600 hover:text-indigo-500 hover:underline transition-colors"
                >
                    Lupa password?
                </Link>
            </div>

            <div class="pt-2">
                <PrimaryButton
                    class="w-full justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200"
                    :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Memproses...</span>
                    <span v-else>Masuk ke Sistem</span>
                </PrimaryButton>
            </div>
        </form>

        <!-- Demo Accounts Info -->
        <div class="mt-8 rounded-xl border border-indigo-100 bg-indigo-50 p-4">
            <p class="text-xs font-semibold text-indigo-700 uppercase tracking-wide mb-3">
                🔑 Akun Demo — Klik untuk mengisi otomatis
            </p>
            <div class="space-y-2">
                <button
                    v-for="account in demoAccounts"
                    :key="account.email"
                    type="button"
                    @click="fillDemo(account)"
                    class="w-full text-left flex items-center justify-between rounded-lg bg-white border border-indigo-100 px-3 py-2 transition hover:border-indigo-400 hover:shadow-sm group"
                >
                    <div>
                        <p class="text-xs font-semibold text-gray-800 group-hover:text-indigo-700">{{ account.role }}</p>
                        <p class="text-xs text-gray-500">{{ account.email }}</p>
                    </div>
                    <span class="text-xs text-indigo-400 group-hover:text-indigo-600 font-mono">password</span>
                </button>
            </div>
        </div>
    </GuestLayout>
</template>
