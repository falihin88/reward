<template>
  <div class="min-h-screen bg-slate-950 text-slate-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8 selection:bg-amber-500 selection:text-slate-950">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
      <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl overflow-hidden bg-white p-1 shadow-2xl mb-4 border border-amber-500/40">
        <img src="/logo.png" alt="AMYN Madrasah Logo" class="w-full h-full object-cover rounded-xl" />
      </div>
      <h2 class="font-heading text-3xl font-extrabold bg-gradient-to-r from-amber-200 via-emerald-200 to-indigo-200 bg-clip-text text-transparent">
        AMYN Madrasah
      </h2>
      <p class="mt-2 text-sm text-slate-400">Striving for the Deeds</p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4">
      <!-- Standard Login Form -->
      <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-2xl backdrop-blur-md">
        <form @submit.prevent="submit">
          <div class="mb-4">
            <label class="block text-xs font-semibold text-slate-300 mb-1">Email Address</label>
            <input
              v-model="form.email"
              type="email"
              required
              placeholder="user@amynmadrasah.com"
              class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 px-3 text-slate-100 text-sm focus:outline-none focus:border-amber-500"
            />
            <div v-if="form.errors.email" class="text-xs text-rose-400 mt-1">{{ form.errors.email }}</div>
          </div>

          <div class="mb-6">
            <label class="block text-xs font-semibold text-slate-300 mb-1">Password</label>
            <input
              v-model="form.password"
              type="password"
              required
              placeholder="••••••••"
              class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 px-3 text-slate-100 text-sm focus:outline-none focus:border-amber-500"
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
import { useForm } from '@inertiajs/vue3';
import { LogIn } from 'lucide-vue-next';

const form = useForm({
  email: '',
  password: '',
});

const submit = () => {
  form.post('/login');
};
</script>
