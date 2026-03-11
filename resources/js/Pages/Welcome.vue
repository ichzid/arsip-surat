<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Head title="Welcome" />
    
    <div class="min-h-screen bg-white flex flex-col items-center justify-center selection:bg-indigo-500 selection:text-white">
        <div class="w-full max-w-7xl px-6 lg:px-8">
            <header class="flex justify-end py-6">
                <nav v-if="canLogin" class="flex gap-4">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20]"
                    >
                        Dashboard
                    </Link>

                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20]"
                        >
                            Log in
                        </Link>

                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20]"
                        >
                            Register
                        </Link>
                    </template>
                </nav>
            </header>

            <main class="mt-16 flex flex-col items-center text-center">
                <ApplicationLogo class="h-24 w-auto fill-current text-indigo-600 mb-8" />
                
                <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl">
                    Sistem Arsip Surat
                </h1>
                
                <p class="mt-6 text-lg leading-8 text-gray-600 max-w-2xl">
                    Sistem Informasi Pengarsipan Surat Masuk dan Surat Keluar Berbasis Digital.
                    Kelola disposisi, arsip, dan pencarian surat dengan mudah dan efisien.
                </p>

                <div class="mt-10 flex items-center justify-center gap-x-6">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                    >
                        Masuk ke Dashboard
                    </Link>
                    <Link
                        v-else
                        :href="route('login')"
                        class="rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                    >
                        Login Sekarang
                    </Link>
                </div>
            </main>

            <footer class="mt-32 text-center text-sm text-gray-500">
                &copy; 2024 Arsip Surat. All rights reserved.
            </footer>
        </div>
    </div>
</template>
