<template>
  <div class="relative group perspective-1000 w-full h-[400px]">
    <div
      :class="[
        'w-full h-full duration-700 transform-style-3d transition-transform cursor-pointer relative rounded-2xl border shadow-xl overflow-hidden',
        'bg-white dark:bg-slate-900/90 text-slate-800 dark:text-slate-100 border-slate-200 dark:border-slate-800',
        isFlipped ? 'rotate-y-180' : '',
        card.rarity === 'legendary' ? 'rarity-legendary-glow' :
        card.rarity === 'epic' ? 'rarity-epic-glow' :
        card.rarity === 'rare' ? 'rarity-rare-glow' : 'rarity-common-glow'
      ]"
      @click="isFlipped = !isFlipped"
    >
      <!-- FRONT SIDE -->
      <div class="absolute inset-0 w-full h-full backface-hidden flex flex-col p-4 justify-between">
        <!-- Rarity Badge & Accent Header -->
        <div class="flex items-center justify-between z-10">
          <span
            :class="[
              'px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider shadow-sm flex items-center gap-1',
              card.rarity === 'legendary' ? 'bg-gradient-to-r from-amber-500 to-yellow-400 text-slate-950 font-extrabold' :
              card.rarity === 'epic' ? 'bg-purple-600 text-white' :
              card.rarity === 'rare' ? 'bg-blue-600 text-white' : 'bg-slate-700 text-slate-200'
            ]"
          >
            <Sparkles v-if="card.rarity === 'legendary'" class="w-3.5 h-3.5" />
            {{ card.rarity }}
          </span>

          <div class="flex items-center gap-1.5 bg-slate-900/80 dark:bg-slate-950/80 backdrop-blur-md px-2.5 py-1 rounded-full text-xs text-amber-400 font-bold border border-amber-500/30">
            <Coins class="w-3.5 h-3.5 text-amber-400" />
            <span>{{ card.unlock_cost }} Pts</span>
          </div>
        </div>

        <!-- Center Scholar Artwork & Portrait -->
        <div class="flex-1 flex flex-col items-center justify-center my-3 relative">
          <!-- Glow aura behind portrait -->
          <div
            class="absolute w-32 h-32 rounded-full blur-2xl opacity-40 transition-opacity group-hover:opacity-75"
            :style="{ backgroundColor: card.accent_color || '#10B981' }"
          ></div>

          <!-- Portrait Frame -->
          <div
            class="w-32 h-32 rounded-full p-1.5 relative shadow-2xl z-10 transition-transform group-hover:scale-105 border-2"
            :style="{ borderColor: card.accent_color || '#10B981' }"
          >
            <div class="w-full h-full rounded-full overflow-hidden bg-slate-950 flex items-center justify-center relative">
              <img
                v-if="card.image_url"
                :src="card.image_url"
                :alt="card.name"
                class="w-full h-full object-cover"
                :class="{ 'grayscale opacity-40': !card.is_unlocked }"
              />
              <div v-else class="w-full h-full bg-gradient-to-tr from-slate-900 to-slate-800 flex items-center justify-center">
                <BookOpen class="w-12 h-12 text-slate-500" :style="{ color: card.is_unlocked ? card.accent_color : '#64748b' }" />
              </div>
            </div>
          </div>

          <!-- Title & Era -->
          <div class="text-center mt-4 z-10 px-2">
            <h3 class="font-heading text-lg font-bold text-slate-900 dark:text-slate-100 line-clamp-1 group-hover:text-amber-600 dark:group-hover:text-amber-300 transition-colors">
              {{ card.name }}
            </h3>
            <p class="text-xs text-amber-600 dark:text-amber-400 font-bold line-clamp-1 mt-0.5">{{ card.title }}</p>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-mono mt-1">{{ card.era }}</p>
          </div>
        </div>

        <!-- Footer Hint -->
        <div class="text-center text-[10px] text-slate-500 dark:text-slate-400 uppercase tracking-widest font-semibold flex items-center justify-center gap-1">
          <RotateCw class="w-3 h-3 text-slate-400" />
          Click to flip card
        </div>

        <!-- LOCKED OVERLAY (If Not Unlocked) -->
        <div
          v-if="!card.is_unlocked"
          class="absolute inset-0 bg-slate-950/85 backdrop-blur-[2px] z-20 flex flex-col items-center justify-between p-5 text-center text-white"
        >
          <div class="w-full flex justify-end">
            <span class="px-2.5 py-1 rounded-full text-[10px] font-mono bg-slate-800 text-slate-300 border border-slate-700">
              Locked
            </span>
          </div>

          <div class="flex flex-col items-center gap-3 my-auto">
            <div class="w-14 h-14 rounded-2xl bg-slate-900 border border-amber-500/40 flex items-center justify-center shadow-lg">
              <Lock class="w-7 h-7 text-amber-400" />
            </div>
            <div>
              <h4 class="font-heading font-bold text-slate-100 text-base">{{ card.name }}</h4>
              <p class="text-xs text-slate-400 mt-1 max-w-[200px]">Unlock to reveal scholar biography and Hadith quotes</p>
            </div>
          </div>

          <!-- Unlock Button -->
          <div class="w-full space-y-2">
            <button
              v-if="canAfford"
              @click.stop="$emit('unlock', card)"
              class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-amber-500 to-emerald-500 text-slate-950 font-bold text-xs hover:from-amber-400 hover:to-emerald-400 transition-all shadow-lg hover:shadow-amber-500/25 flex items-center justify-center gap-2 scale-100 hover:scale-[1.02] cursor-pointer"
            >
              <Key class="w-4 h-4" />
              Unlock for {{ card.unlock_cost }} Pts
            </button>
            <div v-else class="w-full py-2 px-4 rounded-xl bg-slate-900 border border-slate-800 text-slate-400 text-xs font-medium flex items-center justify-center gap-2">
              <Lock class="w-3.5 h-3.5" />
              Need {{ card.unlock_cost - userPoints }} more pts
            </div>
          </div>
        </div>
      </div>

      <!-- BACK SIDE -->
      <div class="absolute inset-0 w-full h-full backface-hidden rotate-y-180 flex flex-col p-5 justify-between bg-slate-900 text-white">
        <div>
          <!-- Header -->
          <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <div>
              <h4 class="font-heading text-sm font-bold text-amber-300">{{ card.name }}</h4>
              <span class="text-[11px] text-slate-400">{{ card.title }}</span>
            </div>
            <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold bg-slate-800 text-slate-300">
              {{ card.rarity }}
            </span>
          </div>

          <!-- Bio -->
          <div class="mt-3 text-xs text-slate-300 space-y-2 leading-relaxed max-h-[200px] overflow-y-auto pr-1">
            <p><strong class="text-amber-400">Era:</strong> {{ card.era }}</p>
            <p class="text-slate-300">{{ card.bio }}</p>
          </div>

          <!-- Famous Quote -->
          <div v-if="card.quote" class="mt-3 p-3 rounded-xl bg-slate-950/80 border border-amber-500/20 italic text-[11px] text-amber-200/90 flex gap-2">
            <Quote class="w-4 h-4 text-amber-400 flex-shrink-0" />
            <span>"{{ card.quote }}"</span>
          </div>
        </div>

        <!-- Footer Flip Back -->
        <div class="text-center text-[10px] text-slate-400 uppercase tracking-widest font-semibold flex items-center justify-center gap-1 pt-2 border-t border-slate-800">
          <RotateCw class="w-3 h-3 text-slate-400" />
          Click to flip back
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Sparkles, Coins, BookOpen, RotateCw, Lock, Key, Quote } from 'lucide-vue-next';

const props = defineProps({
  card: {
    type: Object,
    required: true,
  },
  userPoints: {
    type: Number,
    default: 0,
  },
});

defineEmits(['unlock']);

const isFlipped = ref(false);

const canAfford = computed(() => {
  return props.userPoints >= props.card.unlock_cost;
});
</script>
