<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex bg-white">
        <!-- Left Side: Image & Branding (Hidden on mobile) -->
        <div class="hidden lg:flex lg:w-1/2 relative bg-gray-900 justify-center items-center overflow-hidden">
            <img 
                src="https://images.unsplash.com/photo-1497366216548-37526070297c?q=80&w=2301&auto=format&fit=crop" 
                alt="Office" 
                class="absolute inset-0 w-full h-full object-cover opacity-30"
            />
            <!-- Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-900/90 to-blue-900/90 mix-blend-multiply"></div>
            
            <div class="relative z-10 text-center px-12 max-w-2xl">
                <div class="mb-8 flex justify-center">
                    <div class="p-4 bg-white/10 rounded-2xl backdrop-blur-sm border border-white/20 shadow-xl">
                        <img 
                            v-if="$page.props.settings && $page.props.settings.app_logo" 
                            :src="$page.props.settings.app_logo" 
                            class="h-20 w-20 object-contain"
                            alt="Logo"
                        />
                        <ApplicationLogo v-else class="h-20 w-20 fill-current text-white" />
                    </div>
                </div>
                <h1 class="text-4xl font-extrabold text-white tracking-tight mb-4 drop-shadow-lg">
                    {{ $page.props.settings ? $page.props.settings.app_name : 'SISTEM ARSIP SURAT' }}
                </h1>
                <p class="text-blue-100 text-lg font-light leading-relaxed">
                    {{ $page.props.settings ? $page.props.settings.app_description : 'Platform manajemen surat menyurat digital yang efisien, aman, dan terintegrasi untuk produktivitas divisi Anda.' }}
                </p>
                
                <!-- Feature Pills -->
                <div class="mt-10 flex flex-wrap justify-center gap-3">
                    <span class="px-4 py-2 bg-white/10 backdrop-blur-md rounded-full text-sm text-white border border-white/10">✨ Digitalisasi Arsip</span>
                    <span class="px-4 py-2 bg-white/10 backdrop-blur-md rounded-full text-sm text-white border border-white/10">🚀 Disposisi Cepat</span>
                    <span class="px-4 py-2 bg-white/10 backdrop-blur-md rounded-full text-sm text-white border border-white/10">🔒 Aman & Terpercaya</span>
                </div>
            </div>
            
            <!-- Decorative Elements -->
            <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-indigo-600 rounded-full mix-blend-screen filter blur-3xl opacity-20"></div>
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-blue-500 rounded-full mix-blend-screen filter blur-3xl opacity-20"></div>
        </div>

        <!-- Right Side: Content -->
        <div class="w-full lg:w-1/2 flex flex-col justify-center items-center px-6 py-12 lg:px-24 bg-white">
            <div class="w-full max-w-[440px]">
                <!-- Mobile Logo -->
                <div class="flex justify-center lg:hidden mb-8">
                    <Link href="/">
                        <img 
                            v-if="$page.props.settings && $page.props.settings.app_logo" 
                            :src="$page.props.settings.app_logo" 
                            class="h-16 w-16 object-contain"
                            alt="Logo"
                        />
                        <ApplicationLogo v-else class="h-16 w-16 fill-current text-indigo-600" />
                    </Link>
                </div>

                <slot />
                
                <div class="mt-10 pt-6 border-t border-gray-100 text-center">
                    <p class="text-xs text-gray-400">
                        &copy; {{ new Date().getFullYear() }} {{ $page.props.settings ? $page.props.settings.app_name : 'Sistem Arsip Surat' }}. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
