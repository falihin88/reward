<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8 selection:bg-amber-500 selection:text-slate-950 transition-colors duration-300 relative">
    <!-- Theme Switcher Top-Right -->
    <div class="absolute top-6 right-6">
      <button
        @click="toggleTheme"
        type="button"
        :title="isDark ? 'Switch to Light Theme' : 'Switch to Dark Theme'"
        class="p-2.5 rounded-xl transition-all cursor-pointer flex items-center justify-center gap-1.5 text-xs font-semibold shadow-md bg-white dark:bg-slate-800 text-slate-700 dark:text-amber-400 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700"
      >
        <Sun v-if="isDark" class="w-4 h-4 text-amber-400" />
        <Moon v-else class="w-4 h-4 text-indigo-600" />
        <span class="hidden sm:inline">{{ isDark ? 'Light Mode' : 'Dark Mode' }}</span>
      </button>
    </div>

    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
      <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl overflow-hidden bg-white p-1 shadow-2xl mb-4 border border-amber-500/40">
        <img src="/logo.png" alt="AMYN Madrasah Logo" class="w-full h-full object-cover rounded-xl" />
      </div>
      <h2 class="font-heading text-3xl font-extrabold bg-gradient-to-r from-amber-600 via-emerald-600 to-indigo-700 dark:from-amber-200 dark:via-emerald-200 dark:to-indigo-200 bg-clip-text text-transparent">
        AMYN Madrasah
      </h2>
      <p class="mt-2 text-sm text-slate-500 dark:text-slate-400 font-medium">Striving for the Deeds</p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4">
      <!-- Standard Login Form -->
      <div class="bg-white dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl dark:shadow-2xl backdrop-blur-md">
        <form @submit.prevent="submit">
          <div class="mb-4">
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Email Address</label>
            <input
              v-model="form.email"
              type="email"
              required
              placeholder="user@amynmadrasah.com"
              class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl py-2.5 px-3 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:border-amber-500 placeholder-slate-400 dark:placeholder-slate-500"
            />
            <div v-if="form.errors.email" class="text-xs text-rose-500 dark:text-rose-400 mt-1">{{ form.errors.email }}</div>
          </div>

          <div class="mb-6">
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Password</label>
            <input
              v-model="form.password"
              type="password"
              required
              placeholder="••••••••"
              class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl py-2.5 px-3 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:border-amber-500 placeholder-slate-400 dark:placeholder-slate-500"
            />
          </div>

          <button
            type="submit"
            :disabled="form.processing"
            class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-amber-500 to-emerald-500 text-slate-950 font-bold text-sm shadow-lg hover:from-amber-400 hover:to-emerald-400 transition-all flex items-center justify-center gap-2 cursor-pointer"
          >
            <LogIn class="w-4 h-4" />
            Sign In to AMYN Madrasah
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { LogIn, Sun, Moon } from 'lucide-vue-next';

const form = useForm({
  email: '',
  password: '',
});

const theme = ref(localStorage.getItem('theme') || 'dark');
const isDark = computed(() => theme.value === 'dark');

const applyTheme = () => {
  if (theme.value === 'dark') {
    document.documentElement.classList.add('dark');
  } else {
    document.documentElement.classList.remove('dark');
  }
};

const toggleTheme = () => {
  theme.value = theme.value === 'dark' ? 'light' : 'dark';
  localStorage.setItem('theme', theme.value);
  applyTheme();
};

onMounted(() => {
  applyTheme();
});

const submit = () => {
  form.post('/login');
};
</script>
