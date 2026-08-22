<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md animate-fade-in">
    <div class="bg-white dark:bg-slate-900 border border-amber-500/40 text-slate-900 dark:text-slate-100 w-full max-w-lg rounded-3xl shadow-2xl p-6 relative overflow-hidden text-center">
      <!-- Glow Aura background -->
      <div class="absolute -top-24 -left-24 w-64 h-64 bg-amber-500/20 rounded-full blur-3xl"></div>
      <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-emerald-500/20 rounded-full blur-3xl"></div>

      <!-- Close Button -->
      <button @click="$emit('close')" class="absolute top-4 right-4 p-2 rounded-full bg-slate-100 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white z-20 cursor-pointer">
        <X class="w-5 h-5" />
      </button>

      <!-- Celebratory Banner -->
      <div class="relative z-10 mb-6">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gradient-to-r from-amber-500/20 to-emerald-500/20 border border-amber-500/40 text-amber-700 dark:text-amber-300 text-xs font-bold uppercase tracking-wider mb-2">
          <Sparkles class="w-4 h-4 text-amber-500 animate-spin" />
          Scholar Card Unlocked!
        </div>
        <h2 class="font-heading text-2xl font-bold text-slate-900 dark:text-slate-100">{{ card?.name }}</h2>
        <p class="text-xs text-amber-600 dark:text-amber-400 font-bold">{{ card?.title }}</p>
      </div>

      <!-- Card Display -->
      <div class="relative z-10 w-48 h-64 mx-auto mb-6 rounded-2xl p-4 bg-slate-950 text-white border border-amber-500/40 shadow-2xl flex flex-col justify-between">
        <div class="flex justify-between items-center">
          <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-slate-800 text-amber-300">
            {{ card?.rarity }}
          </span>
          <Coins class="w-4 h-4 text-amber-400" />
        </div>

        <div class="my-auto flex flex-col items-center">
          <div class="w-20 h-20 rounded-full border-2 border-amber-500/50 p-1 bg-slate-900 shadow-xl mb-2">
            <div class="w-full h-full rounded-full bg-slate-950 flex items-center justify-center overflow-hidden">
              <img v-if="card?.image_url" :src="card.image_url" class="w-full h-full object-cover" />
              <BookOpen v-else class="w-8 h-8 text-amber-400" />
            </div>
          </div>
          <span class="text-xs font-bold text-slate-100 line-clamp-1">{{ card?.name }}</span>
          <span class="text-[10px] text-slate-400 font-mono">{{ card?.era }}</span>
        </div>

        <div class="text-[9px] text-slate-500 font-mono">ID #{{ card?.id }}</div>
      </div>

      <!-- Quote & Bio Preview -->
      <div class="relative z-10 bg-slate-50 dark:bg-slate-950/80 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 text-left mb-6 space-y-2">
        <p class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed max-h-24 overflow-y-auto pr-1">
          {{ card?.bio }}
        </p>
        <div v-if="card?.quote" class="pt-2 border-t border-slate-200 dark:border-slate-800 italic text-[11px] text-amber-800 dark:text-amber-200/90 flex gap-2">
          <Quote class="w-4 h-4 text-amber-500 flex-shrink-0" />
          <span>"{{ card.quote }}"</span>
        </div>
      </div>

      <!-- Button -->
      <button
        @click="$emit('close')"
        class="relative z-10 w-full py-3 px-6 rounded-2xl bg-gradient-to-r from-amber-500 via-emerald-500 to-indigo-500 text-slate-950 font-bold text-sm shadow-xl hover:opacity-95 transition-opacity cursor-pointer"
      >
        Awesome! Return to Card Album
      </button>
    </div>
  </div>
</template>

<script setup>
import { watch } from 'vue';
import confetti from 'canvas-confetti';
import { Sparkles, Coins, BookOpen, Quote, X } from 'lucide-vue-next';

const props = defineProps({
  show: Boolean,
  card: Object,
});

defineEmits(['close']);

watch(() => props.show, (newVal) => {
  if (newVal) {
    fireConfetti();
  }
});

const fireConfetti = () => {
  try {
    confetti({
      particleCount: 80,
      spread: 70,
      origin: { y: 0.6 },
      colors: ['#f59e0b', '#10b981', '#6366f1', '#eab308'],
    });
  } catch (e) {
    console.error('Confetti trigger error', e);
  }
};
</script>
