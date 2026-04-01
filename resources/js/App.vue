<template>
    <div class="flex min-h-screen">
        <AppSidebar />

        <div class="flex-1 flex flex-col">
            <AppHeader />

            <main class="flex-1 p-8 overflow-y-auto" role="main">
                <router-view v-slot="{ Component }">
                    <transition name="fade" mode="out-in" appear>
                        <component :is="Component" />
                    </transition>
                </router-view>

                <FlashAlert />
            </main>
        </div>
    </div>
</template>

<script setup>
import { onMounted, defineAsyncComponent } from 'vue';
import AppSidebar from './components/Layout/AppSidebar.vue';
import AppHeader from './components/Layout/AppHeader.vue';

const FlashAlert = defineAsyncComponent(() => import('./components/Shared/FlashAlert.vue'));
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>