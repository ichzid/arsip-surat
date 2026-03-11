<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import SidebarLink from '@/Components/SidebarLink.vue';
import Toast from '@/Components/Toast.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const showingNavigationDropdown = ref(false);
const page = usePage();
const notifications = computed(() => page.props.auth?.notifications?.list || []);
const unreadCount = computed(() => page.props.auth?.notifications?.count || 0);

const markAsRead = (notification) => {
    router.post(route('notifications.read', notification.id));
};

const markAllAsRead = () => {
    router.post(route('notifications.read-all'));
};
</script>

<template>
    <div class="min-h-screen bg-gray-50 flex">
        <!-- Sidebar Desktop -->
        <aside class="w-64 bg-white border-r border-gray-200 hidden md:flex flex-col fixed inset-y-0 z-50">
            <!-- Logo -->
            <div class="h-16 flex items-center px-6 border-b border-gray-100">
                <Link :href="route('dashboard')" class="flex items-center gap-2">
                    <img 
                        v-if="$page.props.settings && $page.props.settings.app_logo" 
                        :src="$page.props.settings.app_logo" 
                        class="block h-8 w-auto" 
                        alt="Logo" 
                    />
                    <ApplicationLogo v-else class="block h-8 w-auto fill-current text-indigo-600" />
                    <span class="text-xl font-bold text-gray-900 tracking-tight">
                        {{ $page.props.settings ? $page.props.settings.app_name : 'ArsipSurat' }}
                    </span>
                </Link>
            </div>

            <!-- Nav Links -->
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                <SidebarLink :href="route('dashboard')" :active="route().current('dashboard')">
                    <template #icon>
                        <svg class="w-5 h-5 flex-shrink-0 transition-colors duration-200" :class="route().current('dashboard') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-600'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </template>
                    Dashboard
                </SidebarLink>

                <div class="pt-4 pb-2">
                    <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Menu Utama</p>
                </div>

                <SidebarLink :href="route('incoming-letters.index')" :active="route().current('incoming-letters.*')">
                    <template #icon>
                        <svg class="w-5 h-5 flex-shrink-0 transition-colors duration-200" :class="route().current('incoming-letters.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-600'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </template>
                    Surat Masuk
                </SidebarLink>

                <SidebarLink :href="route('outgoing-letters.index')" :active="route().current('outgoing-letters.*')">
                    <template #icon>
                        <svg class="w-5 h-5 flex-shrink-0 transition-colors duration-200" :class="route().current('outgoing-letters.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-600'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </template>
                    Surat Keluar
                </SidebarLink>

                <SidebarLink :href="route('reports.index')" :active="route().current('reports.*')">
                    <template #icon>
                        <svg class="w-5 h-5 flex-shrink-0 transition-colors duration-200" :class="route().current('reports.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-600'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </template>
                    Laporan
                </SidebarLink>

                <template v-if="$page.props.auth.user.roles && $page.props.auth.user.roles.some(r => r.name === 'Admin')">
                    <div class="pt-4 pb-2">
                        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Administrasi</p>
                    </div>

                    <SidebarLink :href="route('users.index')" :active="route().current('users.*')">
                        <template #icon>
                            <svg class="w-5 h-5 flex-shrink-0 transition-colors duration-200" :class="route().current('users.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-600'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </template>
                        Manajemen Pengguna
                    </SidebarLink>

                    <SidebarLink :href="route('divisions.index')" :active="route().current('divisions.*')">
                        <template #icon>
                            <svg class="w-5 h-5 flex-shrink-0 transition-colors duration-200" :class="route().current('divisions.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-600'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </template>
                        Manajemen Divisi
                    </SidebarLink>

                    <SidebarLink :href="route('settings.edit')" :active="route().current('settings.*')">
                        <template #icon>
                            <svg class="w-5 h-5 flex-shrink-0 transition-colors duration-200" :class="route().current('settings.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-600'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </template>
                        Pengaturan Website
                    </SidebarLink>
                </template>
            </nav>

            <!-- User Info Sidebar Footer -->
            <div class="border-t border-gray-100 p-4">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0">
                        <div class="h-9 w-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                            {{ $page.props.auth.user.name.charAt(0) }}
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">
                            {{ $page.props.auth.user.name }}
                        </p>
                        <p class="text-xs text-gray-500 truncate">
                            {{ $page.props.auth.user.email }}
                        </p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Mobile Sidebar (Overlay) -->
        <div v-show="showingNavigationDropdown" class="fixed inset-0 z-40 md:hidden" role="dialog" aria-modal="true">
            <!-- Background backdrop -->
            <div class="fixed inset-0 bg-gray-600 bg-opacity-75 transition-opacity" @click="showingNavigationDropdown = false"></div>
            
            <!-- Mobile Sidebar Panel -->
            <div class="relative flex-1 flex flex-col max-w-xs w-full bg-white h-full transition ease-in-out duration-300 transform">
                <div class="absolute top-0 right-0 -mr-12 pt-2">
                    <button type="button" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white" @click="showingNavigationDropdown = false">
                        <span class="sr-only">Close sidebar</span>
                        <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="h-16 flex items-center px-6 border-b border-gray-100">
                    <ApplicationLogo class="block h-8 w-auto fill-current text-indigo-600" />
                    <span class="ml-3 text-xl font-bold text-gray-900">ArsipSurat</span>
                </div>

                <div class="flex-1 h-0 overflow-y-auto pt-5 pb-4">
                    <nav class="mt-5 px-2 space-y-1">
                        <!-- Replicate links for mobile -->
                        <SidebarLink :href="route('dashboard')" :active="route().current('dashboard')">
                            Dashboard
                        </SidebarLink>
                        <SidebarLink :href="route('incoming-letters.index')" :active="route().current('incoming-letters.*')">
                            Surat Masuk
                        </SidebarLink>
                        <SidebarLink :href="route('outgoing-letters.index')" :active="route().current('outgoing-letters.*')">
                            Surat Keluar
                        </SidebarLink>
                         <SidebarLink :href="route('reports.index')" :active="route().current('reports.*')">
                            Laporan
                        </SidebarLink>
                        <template v-if="$page.props.auth.user.roles && $page.props.auth.user.roles.some(r => r.name === 'Admin')">
                            <SidebarLink :href="route('users.index')" :active="route().current('users.*')">
                                Manajemen Pengguna
                            </SidebarLink>
                            <SidebarLink :href="route('divisions.index')" :active="route().current('divisions.*')">
                                Manajemen Divisi
                            </SidebarLink>
                            <SidebarLink :href="route('settings.edit')" :active="route().current('settings.*')">
                                Pengaturan Website
                            </SidebarLink>
                        </template>
                    </nav>
                </div>
                
                <div class="border-t border-gray-200 p-4">
                    <div class="flex items-center px-4 mb-4">
                         <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                                {{ $page.props.auth.user.name.charAt(0) }}
                            </div>
                        </div>
                        <div class="ml-3">
                            <div class="text-base font-medium text-gray-800">{{ $page.props.auth.user.name }}</div>
                            <div class="text-sm font-medium text-gray-500">{{ $page.props.auth.user.email }}</div>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <Link :href="route('profile.edit')" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-md">
                            Profile
                        </Link>
                        <Link :href="route('logout')" method="post" as="button" class="block w-full text-left px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-md">
                            Log Out
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col md:pl-64 min-h-screen transition-all duration-300">
            <!-- Top Header -->
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 sm:px-6 lg:px-8 sticky top-0 z-30 shadow-sm">
                <div class="flex items-center">
                    <button @click="showingNavigationDropdown = true" type="button" class="md:hidden -ml-0.5 -mt-0.5 h-12 w-12 inline-flex items-center justify-center rounded-md text-gray-500 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                        <span class="sr-only">Open sidebar</span>
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    
                    <!-- Breadcrumbs or Page Title -->
                    <div class="ml-4 md:ml-0">
                        <slot name="header" />
                    </div>
                </div>

                <!-- Right Side Actions -->
                <div class="flex items-center">
                    <!-- Notification Dropdown -->
                    <div class="mr-3 relative">
                        <Dropdown align="right" width="128">
                            <template #trigger>
                                <button class="p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 relative">
                                    <span class="sr-only">View notifications</span>
                                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                    <!-- Badge -->
                                    <div v-if="unreadCount > 0" class="absolute top-0 right-0 flex items-center justify-center w-4 h-4 text-xs font-bold text-white bg-red-600 rounded-full border-2 border-white transform translate-x-1/4 -translate-y-1/4">
                                        {{ unreadCount }}
                                    </div>
                                </button>
                            </template>

                            <template #content>
                                <div class="px-4 py-2 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                                    <span class="text-sm font-semibold text-gray-700">Notifikasi</span>
                                    <button v-if="unreadCount > 0" @click="markAllAsRead" class="text-xs text-indigo-600 hover:text-indigo-900 font-medium">
                                        Tandai semua dibaca
                                    </button>
                                </div>
                                
                                <div v-if="notifications.length === 0" class="px-4 py-8 text-sm text-gray-500 text-center">
                                    Tidak ada notifikasi baru
                                </div>

                                <div v-else class="max-h-80 overflow-y-auto">
                                    <div v-for="notification in notifications" :key="notification.id" 
                                        @click="markAsRead(notification)"
                                        class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition duration-150 ease-in-out">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0 pt-0.5">
                                                <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-3 w-0 flex-1">
                                                <p class="text-sm font-medium text-gray-900">
                                                    Disposisi dari {{ notification.data.from_division }}
                                                </p>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ notification.data.agenda_number }} <span class="mx-1">&bull;</span> {{ notification.data.subject }}
                                                </p>
                                                <p v-if="notification.data.notes" class="text-xs text-gray-600 mt-1 italic line-clamp-2">
                                                    "{{ notification.data.notes }}"
                                                </p>
                                                <p class="text-xs text-gray-400 mt-1">
                                                    {{ new Date(notification.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' }) }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </Dropdown>
                    </div>

                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                <div>{{ $page.props.auth.user.name }}</div>
                                <div class="ml-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </template>

                        <template #content>
                            <DropdownLink :href="route('profile.edit')">
                                Profile
                            </DropdownLink>
                            <DropdownLink :href="route('logout')" method="post" as="button">
                                Log Out
                            </DropdownLink>
                        </template>
                    </Dropdown>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto">
                <slot />
            </main>
        </div>

        <Toast />
    </div>
</template>
