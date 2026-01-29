<script setup lang="ts">
import { ref, onMounted } from 'vue';

const urls = ref<Record<string, string>>({
    product: '',
    cart: '',
    gallery: ''
});
const selectedTypes = ref<Set<string>>(new Set());
const isLoading = ref(false);
const isAuthenticated = ref(false);

onMounted(() => {
    const enteredPassword = prompt('Enter password to access this tool:');
    if (enteredPassword === import.meta.env.VITE_APP_PASSWORD) {
        isAuthenticated.value = true;
    } else {
        alert('Incorrect password');
    }
});

function toggleType(type: string) {
    if (selectedTypes.value.has(type)) {
        selectedTypes.value.delete(type);
    } else {
        selectedTypes.value.add(type);
    }
    // Force reactivity
    selectedTypes.value = new Set(selectedTypes.value);
}
const results = ref<any>(null);
const error = ref<string | null>(null);

async function analyzeUrl() {
    if (selectedTypes.value.size === 0) return;

    isLoading.value = true;
    error.value = null;
    results.value = null;

    try {
        const response = await fetch(`${import.meta.env.VITE_API_URL}/api/approve/analyze`, {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                urls: Array.from(selectedTypes.value).map(type => ({
                    url: urls.value[type],
                    type: type
                }))
            })
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        results.value = await response.json();
    } catch (e: any) {
        error.value = e.message || 'Failed to analyze URL';
    } finally {
        isLoading.value = false;
    }
}
</script>

<template>
    <div v-if="isAuthenticated" class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Header -->
        <header class="bg-white dark:bg-gray-800 shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    APPROVE Integration Tool
                </h1>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- URL Input Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Analyze URL</h2>
                <div class="space-y-4">
                    <div>
                        <label for="buttons" class="block text-md font-medium text-gray-700 dark:text-gray-300">
                            Integration Types
                        </label>
                        <div id="buttons" class="flex gap-2 mt-2">
                            <button 
                                type="button"
                                @click="toggleType('product')"
                                :class="selectedTypes.has('product') 
                                    ? 'bg-blue-600 text-white' 
                                    : 'bg-gray-200 text-gray-700 dark:bg-gray-600 dark:text-gray-300'"
                                class="px-4 py-2 rounded-md transition-colors"
                            >
                                Product
                            </button>
                            <button 
                                type="button"
                                @click="toggleType('cart')"
                                :class="selectedTypes.has('cart') 
                                    ? 'bg-blue-600 text-white' 
                                    : 'bg-gray-200 text-gray-700 dark:bg-gray-600 dark:text-gray-300'"
                                class="px-4 py-2 rounded-md transition-colors"
                            >
                                Cart
                            </button>
                            <button 
                                type="button"
                                @click="toggleType('gallery')"
                                :class="selectedTypes.has('gallery') 
                                    ? 'bg-blue-600 text-white' 
                                    : 'bg-gray-200 text-gray-700 dark:bg-gray-600 dark:text-gray-300'"
                                class="px-4 py-2 rounded-md transition-colors"
                            >
                                Gallery
                            </button>
                        </div>
                    </div>
                    <div>
                        <div v-for="type in Array.from(selectedTypes)" :key="type">
                            <label :for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ type.charAt(0).toUpperCase() + type.slice(1) }} Page Url
                            </label>
                            <input :id="type" v-model="urls[type]" type="url" placeholder="https://example.com/product/123"
                                class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white px-3 py-2" /><br>
                        </div>
                    </div>
                    <button @click="analyzeUrl" :disabled="isLoading || !urls || selectedTypes.size === 0"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                        {{ isLoading ? 'Analyzing...' : 'Analyze' }}
                    </button>
                </div>
            </div>

            <!-- Error Alert -->
            <div v-if="error"
                class="mt-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                <h3 class="text-red-800 dark:text-red-400 font-medium">Error</h3>
                <p class="text-red-700 dark:text-red-300 text-sm mt-1">{{ error }}</p>
            </div>

            <!-- Results Card -->
            <div v-if="results" class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Analysis Results</h2>
                <div class="space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="text-gray-600 dark:text-gray-400">Status:</span>
                        <span :class="results.success
                            ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
                            : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'"
                            class="px-2 py-1 rounded-full text-xs font-medium">
                            {{ results.success ? 'Success' : 'Failed' }}
                        </span>
                    </div>

                    <div v-if="results.selectors" class="mt-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">
                            Found Selectors
                        </h3>
                        <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-4">
                            <pre
                                class="text-sm text-gray-800 dark:text-gray-200 overflow-x-auto">{{ JSON.stringify(results.selectors, null, 2) }}</pre>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <div v-else class="min-h-screen bg-gray-50 dark:bg-gray-900 flex items-center justify-center">
        <p class="text-gray-600 dark:text-gray-400">Access denied. Please refresh and enter the correct password.</p>
    </div>
</template>
