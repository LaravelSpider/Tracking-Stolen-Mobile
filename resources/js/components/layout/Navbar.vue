<template>
    <nav class="bg-white border-b border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                Logo and Navigation
                <div class="flex">
                    Logo
                    <div class="flex items-center flex-shrink-0">
                        <router-link to="/dashboard" class="flex items-center">
                            <DevicePhoneMobileIcon class="w-8 h-8 text-blue-600" />
                            <span class="ml-2 text-xl font-bold text-gray-900 dark:text-white">
                                {{ $t('app.name') }}
                            </span>
                        </router-link>
                    </div>

                    Navigation Links
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8" :class="{ 'sm:space-x-reverse': isRTL }">
                        <router-link to="/dashboard"
                            class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-500 transition-colors border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300 dark:text-gray-300 dark:hover:text-white"
                            active-class="text-gray-900 border-blue-500 dark:text-white">
                            <ChartBarIcon class="w-4 h-4 mr-2" />
                            {{ $t('nav.dashboard') }}
                        </router-link>

                        <router-link to="/search"
                            class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-500 transition-colors border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300 dark:text-gray-300 dark:hover:text-white"
                            active-class="text-gray-900 border-blue-500 dark:text-white">
                            <MagnifyingGlassIcon class="w-4 h-4 mr-2" />
                            {{ $t('nav.search') }}
                        </router-link>

                        <router-link v-if="authStore.isAuthenticated" to="/devices"
                            class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-500 transition-colors border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300 dark:text-gray-300 dark:hover:text-white"
                            active-class="text-gray-900 border-blue-500 dark:text-white">
                            <DevicePhoneMobileIcon class="w-4 h-4 mr-2" />
                            {{ $t('nav.devices') }}
                        </router-link>
                    </div>
                </div>

                Right side
                <div class="flex items-center space-x-4" :class="{ 'space-x-reverse': isRTL }">
                    Language Switcher
                    <LanguageSwitcher />

                    Theme Toggle
                    <ThemeToggle />

                    User Menu
                    <div v-if="authStore.isAuthenticated" class="relative">
                        <Menu as="div" class="relative inline-block text-left">
                            <div>
                                <MenuButton
                                    class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <img class="w-8 h-8 rounded-full"
                                        :src="authStore.user?.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(authStore.userName)}&color=7F9CF5&background=EBF4FF`"
                                        :alt="authStore.userName" />
                                    <span class="hidden ml-2 text-gray-700 dark:text-gray-300 md:block">
                                        {{ authStore.userName }}
                                    </span>
                                    <ChevronDownIcon class="w-4 h-4 ml-1 text-gray-400" />
                                </MenuButton>
                            </div>

                            <transition enter-active-class="transition duration-100 ease-out"
                                enter-from-class="transform scale-95 opacity-0"
                                enter-to-class="transform scale-100 opacity-100"
                                leave-active-class="transition duration-75 ease-in"
                                leave-from-class="transform scale-100 opacity-100"
                                leave-to-class="transform scale-95 opacity-0">
                                <MenuItems
                                    class="absolute right-0 z-10 w-48 mt-2 origin-top-right bg-white rounded-md shadow-lg dark:bg-gray-800 ring-1 ring-black ring-opacity-5 focus:outline-none">
                                    <div class="py-1">
                                        <MenuItem v-slot="{ active }">
                                        <router-link to="/profile" :class="[
                                            active ? 'bg-gray-100 dark:bg-gray-700' : '',
                                            'flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300'
                                        ]">
                                            <UserIcon class="w-4 h-4 mr-3" />
                                            {{ $t('nav.profile') }}
                                        </router-link>
                                        </MenuItem>

                                        <MenuItem v-if="authStore.isAdmin || authStore.isSecurityAgency"
                                            v-slot="{ active }">
                                        <router-link to="/reports" :class="[
                                            active ? 'bg-gray-100 dark:bg-gray-700' : '',
                                            'flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300'
                                        ]">
                                            <DocumentTextIcon class="w-4 h-4 mr-3" />
                                            {{ $t('nav.reports') }}
                                        </router-link>
                                        </MenuItem>

                                        <MenuItem v-slot="{ active }">
                                        <button @click="handleLogout" :class="[
                                            active ? 'bg-gray-100 dark:bg-gray-700' : '',
                                            'flex w-full items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300'
                                        ]">
                                            <ArrowRightOnRectangleIcon class="w-4 h-4 mr-3" />
                                            {{ $t('nav.logout') }}
                                        </button>
                                        </MenuItem>
                                    </div>
                                </MenuItems>
                            </transition>
                        </Menu>
                    </div>
                    <div v-else class="flex items-center space-x-2" :class="{ 'space-x-reverse': isRTL }">
                        <router-link to="/login"
                            class="px-3 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white">
                            {{ $t('auth.login') }}
                        </router-link>
                        <router-link to="/register"
                            class="px-4 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-md hover:bg-blue-700">
                            {{ $t('auth.register') }}
                        </router-link>
                    </div>

                    Mobile menu button
                    <div class="sm:hidden">
                        <button @click="mobileMenuOpen = !mobileMenuOpen"
                            class="inline-flex items-center justify-center p-2 text-gray-400 rounded-md hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
                            <Bars3Icon v-if="!mobileMenuOpen" class="w-6 h-6" />
                            <XMarkIcon v-else class="w-6 h-6" />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        Mobile menu
        <div v-show="mobileMenuOpen" class="sm:hidden">
            <div class="pt-2 pb-3 space-y-1">
                <router-link to="/dashboard"
                    class="block py-2 pl-3 pr-4 text-base font-medium text-gray-500 border-l-4 border-transparent hover:text-gray-700 hover:border-gray-300 dark:text-gray-300 dark:hover:text-white"
                    active-class="text-blue-700 border-blue-500 bg-blue-50 dark:bg-blue-900 dark:text-blue-200"
                    @click="mobileMenuOpen = false">
                    {{ $t('nav.dashboard') }}
                </router-link>

                <router-link to="/search"
                    class="block py-2 pl-3 pr-4 text-base font-medium text-gray-500 border-l-4 border-transparent hover:text-gray-700 hover:border-gray-300 dark:text-gray-300 dark:hover:text-white"
                    active-class="text-blue-700 border-blue-500 bg-blue-50 dark:bg-blue-900 dark:text-blue-200"
                    @click="mobileMenuOpen = false">
                    {{ $t('nav.search') }}
                </router-link>

                <router-link v-if="authStore.isAuthenticated" to="/devices"
                    class="block py-2 pl-3 pr-4 text-base font-medium text-gray-500 border-l-4 border-transparent hover:text-gray-700 hover:border-gray-300 dark:text-gray-300 dark:hover:text-white"
                    active-class="text-blue-700 border-blue-500 bg-blue-50 dark:bg-blue-900 dark:text-blue-200"
                    @click="mobileMenuOpen = false">
                    {{ $t('nav.devices') }}
                </router-link>
            </div>
        </div>
    </nav>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
import {
    DevicePhoneMobileIcon,
    ChartBarIcon,
    MagnifyingGlassIcon,
    UserIcon,
    DocumentTextIcon,
    ArrowRightOnRectangleIcon,
    ChevronDownIcon,
    Bars3Icon,
    XMarkIcon,
} from '@heroicons/vue/24/outline'

import { useAuthStore } from '../../stores/auth'
import { useI18nStore } from '../../stores/i18n'
import LanguageSwitcher from '../ui/LanguageSwitcher.vue'
import ThemeToggle from '../ui/ThemeToggle.vue'

const router = useRouter()
const authStore = useAuthStore()
const i18nStore = useI18nStore()

const mobileMenuOpen = ref(false)
const isRTL = computed(() => i18nStore.currentLocale === 'ar')

const handleLogout = async () => {
    try {
        await authStore.logout()
        router.push('/login')
    } catch (error) {
        console.error('Logout error:', error)
    }
}
</script>
