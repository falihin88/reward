<template>
  <AppLayout>
    <div class="space-y-6">
      <!-- Header Banner -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 rounded-3xl bg-white dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 shadow-xl dark:shadow-2xl">
        <div class="space-y-1">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30 text-xs font-bold uppercase tracking-wider">
            <BookOpenCheck class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
            Scholar Collectible Deck
          </div>
          <h2 class="font-heading text-2xl font-bold text-slate-900 dark:text-slate-100">Muslim Heroes & Scholar Cards</h2>
          <p class="text-xs text-slate-600 dark:text-slate-400 max-w-lg">
            Spend your earned learning points to collect cards featuring legendary Islamic scholars, scientists, and heroes!
          </p>
        </div>

        <!-- Album Progress Card -->
        <div class="bg-slate-100 dark:bg-slate-950 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 text-right min-w-[200px]">
          <div class="text-xs text-slate-500 dark:text-slate-400 font-semibold mb-1">Collection Progress</div>
          <div class="text-2xl font-extrabold text-amber-600 dark:text-amber-400">
            {{ unlockedCount }} / {{ cards.length }} <span class="text-xs font-normal text-slate-500">Cards</span>
          </div>
          <div class="w-full bg-slate-200 dark:bg-slate-900 h-2 rounded-full mt-2 overflow-hidden border border-slate-300 dark:border-slate-800">
            <div
              class="bg-gradient-to-r from-amber-500 to-emerald-500 h-full rounded-full transition-all duration-700"
              :style="{ width: ((unlockedCount / (cards.length || 1)) * 100) + '%' }"
            ></div>
          </div>
        </div>
      </div>

      <!-- Filters Bar -->
      <div class="flex flex-wrap items-center justify-between gap-4 p-4 rounded-2xl bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 shadow-sm">
        <!-- Status Filters -->
        <div class="flex items-center gap-2">
          <button
            v-for="st in statusOptions"
            :key="st.value"
            @click="selectedStatus = st.value"
            type="button"
            :class="[
              'px-3 py-1.5 rounded-xl text-xs font-semibold transition-all cursor-pointer',
              selectedStatus === st.value
                ? 'bg-amber-500 text-slate-950 shadow-md font-bold'
                : 'bg-slate-100 dark:bg-slate-950 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white border border-slate-200 dark:border-slate-800'
            ]"
          >
            {{ st.label }}
          </button>
        </div>

        <!-- Rarity Filters -->
        <div class="flex items-center gap-1.5 overflow-x-auto">
          <button
            v-for="r in rarityOptions"
            :key="r.value"
            @click="selectedRarity = r.value"
            type="button"
            :class="[
              'px-3 py-1.5 rounded-xl text-xs font-semibold transition-all uppercase tracking-wider cursor-pointer',
              selectedRarity === r.value
                ? 'bg-slate-200 dark:bg-slate-800 text-amber-700 dark:text-amber-400 border border-amber-500/40 shadow-sm'
                : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'
            ]"
          >
            {{ r.label }}
          </button>
        </div>
      </div>

      <!-- Cards Grid -->
      <div v-if="filteredCards.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <ScholarCard
          v-for="card in filteredCards"
          :key="card.id"
          :card="card"
          :userPoints="userPoints"
          @unlock="handleUnlockPrompt"
        />
      </div>

      <div v-else class="p-12 text-center text-slate-500 text-sm bg-white dark:bg-slate-900/60 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm">
        No scholar cards match your selected filters.
      </div>
    </div>

    <!-- Unlock Celebratory Modal -->
    <UnlockCardModal
      :show="showModal"
      :card="activeCard"
      @close="showModal = false"
    />
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ScholarCard from '@/Components/ScholarCard.vue';
import UnlockCardModal from '@/Components/UnlockCardModal.vue';
import { BookOpenCheck } from 'lucide-vue-next';

const props = defineProps({
  cards: Array,
  userPoints: Number,
});

const selectedStatus = ref('all');
const selectedRarity = ref('all');
const showModal = ref(false);
const activeCard = ref(null);

const statusOptions = [
  { label: 'All Cards', value: 'all' },
  { label: 'Unlocked', value: 'unlocked' },
  { label: 'Locked', value: 'locked' },
];

const rarityOptions = [
  { label: 'All Rarities', value: 'all' },
  { label: 'Common', value: 'common' },
  { label: 'Rare', value: 'rare' },
  { label: 'Epic', value: 'epic' },
  { label: 'Legendary', value: 'legendary' },
];

const unlockedCount = computed(() => {
  return props.cards.filter((c) => c.is_unlocked).length;
});

const filteredCards = computed(() => {
  return props.cards.filter((card) => {
    // Filter status
    if (selectedStatus.value === 'unlocked' && !card.is_unlocked) return false;
    if (selectedStatus.value === 'locked' && card.is_unlocked) return false;

    // Filter rarity
    if (selectedRarity.value !== 'all' && card.rarity !== selectedRarity.value) return false;

    return true;
  });
});

const handleUnlockPrompt = (card) => {
  router.post(
    `/cards/${card.id}/unlock`,
    {},
    {
      onSuccess: () => {
        activeCard.value = card;
        showModal.value = true;
      },
    }
  );
};
</script>
