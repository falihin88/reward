<template>
  <AppLayout>
    <div class="space-y-6">
      <!-- Back Button & Breadcrumbs -->
      <div class="flex items-center justify-between">
        <Link
          :href="route('teacher.dashboard')"
          class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors"
        >
          <ArrowLeft class="w-4 h-4" />
          Back to Student Roster
        </Link>
      </div>

      <!-- Student Banner -->
      <div class="p-6 md:p-8 rounded-3xl bg-white dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 shadow-xl dark:shadow-2xl flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden">
        <div class="flex items-center gap-5 z-10">
          <div class="w-20 h-20 rounded-2xl bg-gradient-to-tr from-amber-500 to-emerald-500 p-1 shadow-xl">
            <div class="w-full h-full bg-slate-950 rounded-xl flex items-center justify-center text-2xl font-extrabold text-amber-400 font-heading">
              {{ student.name.charAt(0) }}
            </div>
          </div>
          <div>
            <div class="flex items-center gap-2">
              <h2 class="font-heading text-2xl font-bold text-slate-900 dark:text-slate-100">{{ student.name }}</h2>
              <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-blue-100 dark:bg-blue-500/20 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-500/30">
                Student Profile
              </span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ student.email }}</p>
            <div class="flex items-center gap-3 mt-2 text-xs font-medium text-slate-700 dark:text-slate-300">
              <span class="flex items-center gap-1 text-amber-600 dark:text-amber-400 font-bold"><Coins class="w-4 h-4 text-amber-500" /> {{ student.points }} Pts</span>
              <span class="flex items-center gap-1 text-orange-600 dark:text-orange-400 font-bold"><Flame class="w-4 h-4 text-orange-500" /> {{ student.current_streak }}d Streak</span>
              <span class="flex items-center gap-1 text-indigo-600 dark:text-indigo-400 font-bold"><BookOpenCheck class="w-4 h-4 text-indigo-500" /> {{ unlockedCount }}/{{ totalCardsCount }} Cards</span>
            </div>
          </div>
        </div>

        <div class="flex flex-wrap items-center gap-3 z-10">
          <!-- One-Click Login As Student Button -->
          <button
            @click="loginAsStudent"
            type="button"
            class="px-4 py-3 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-xs shadow-lg transition-all flex items-center gap-2 cursor-pointer scale-100 hover:scale-105"
          >
            <LogIn class="w-4 h-4 text-slate-950" />
            Login as {{ student.name.split(' ')[0] }}
          </button>

          <!-- Award / Deduct Button -->
          <button
            @click="showAwardModal = true"
            class="px-5 py-3 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-500 text-slate-950 font-extrabold text-xs shadow-lg hover:from-emerald-400 hover:to-teal-400 transition-all flex items-center gap-2 cursor-pointer"
          >
            <Award class="w-4 h-4" />
            Award / Deduct Points & Add Comment
          </button>
        </div>
      </div>

      <!-- Student Card Deck Collection (Teacher View) -->
      <div class="p-6 rounded-3xl bg-white dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 shadow-xl space-y-4">
        <div class="flex items-center justify-between">
          <h3 class="font-heading text-lg font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
            <BookOpenCheck class="w-5 h-5 text-emerald-500" />
            {{ student.name }}'s Scholar Card Album
          </h3>
          <span class="text-xs text-slate-500 dark:text-slate-400 font-mono">
            {{ unlockedCount }} of {{ totalCardsCount }} Unlocked
          </span>
        </div>

        <!-- Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          <div
            v-for="card in cards"
            :key="card.id"
            :class="[
              'p-4 rounded-2xl border flex flex-col justify-between space-y-3 relative overflow-hidden transition-all',
              card.is_unlocked
                ? 'bg-slate-50 dark:bg-slate-950 border-amber-500/40 shadow-lg'
                : 'bg-slate-100 dark:bg-slate-950 border-slate-200 dark:border-slate-800/80 opacity-60'
            ]"
          >
            <div class="flex items-center justify-between">
              <span
                :class="[
                  'px-2 py-0.5 rounded text-[10px] font-bold uppercase',
                  card.rarity === 'legendary' ? 'bg-amber-500 text-slate-950' :
                  card.rarity === 'epic' ? 'bg-purple-500 text-white' :
                  card.rarity === 'rare' ? 'bg-blue-500 text-white' : 'bg-slate-700 text-slate-200'
                ]"
              >
                {{ card.rarity }}
              </span>
              <span v-if="card.is_unlocked" class="text-[10px] text-emerald-600 dark:text-emerald-400 font-mono font-bold">Unlocked</span>
              <span v-else class="text-[10px] text-slate-400 dark:text-slate-500 font-mono">Locked</span>
            </div>

            <div class="text-center py-2">
              <div class="w-16 h-16 mx-auto rounded-full border-2 p-1 mb-2 bg-white dark:bg-slate-900 flex items-center justify-center shadow-inner" :style="{ borderColor: card.is_unlocked ? card.accent_color : '#94a3b8' }">
                <BookOpen class="w-8 h-8" :style="{ color: card.is_unlocked ? card.accent_color : '#94a3b8' }" />
              </div>
              <h4 class="font-heading font-bold text-slate-900 dark:text-slate-100 text-sm line-clamp-1">{{ card.name }}</h4>
              <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-1">{{ card.title }}</p>
            </div>

            <div class="text-[10px] text-slate-500 text-center font-mono border-t border-slate-200 dark:border-slate-900 pt-2">
              {{ card.era }}
            </div>
          </div>
        </div>
      </div>

      <!-- Point History & Teacher Comments Table -->
      <div class="p-6 rounded-3xl bg-white dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 shadow-xl space-y-4">
        <h3 class="font-heading text-lg font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
          <History class="w-5 h-5 text-indigo-500" />
          Points Ledger & Teacher Comments
        </h3>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
            <thead class="bg-slate-100 dark:bg-slate-950 text-slate-500 dark:text-slate-400 uppercase text-[10px] font-mono border-b border-slate-200 dark:border-slate-800">
              <tr>
                <th class="py-3 px-4">Date</th>
                <th class="py-3 px-4">Reason</th>
                <th class="py-3 px-4">Awarded By</th>
                <th class="py-3 px-4">Comment / Note</th>
                <th class="py-3 px-4 text-right">Points</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
              <tr v-for="tx in transactions.data" :key="tx.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                <td class="py-3 px-4 font-mono text-[11px] text-slate-500 dark:text-slate-400">{{ formatDate(tx.created_at) }}</td>
                <td class="py-3 px-4 font-semibold text-slate-900 dark:text-slate-200">{{ tx.reason.replace('_', ' ').toUpperCase() }}</td>
                <td class="py-3 px-4 text-blue-600 dark:text-blue-400 font-medium">{{ tx.teacher ? tx.teacher.name : 'System' }}</td>
                <td class="py-3 px-4 italic text-slate-600 dark:text-slate-300 max-w-xs truncate">{{ tx.note || '-' }}</td>
                <td :class="['py-3 px-4 font-extrabold text-right text-sm', tx.points > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400']">
                  {{ tx.points > 0 ? '+' : '' }}{{ tx.points }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Award Modal -->
    <AwardPointModal
      :show="showAwardModal"
      :student="student"
      @close="showAwardModal = false"
    />
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import AwardPointModal from '@/Components/AwardPointModal.vue';
import {
  ArrowLeft,
  Coins,
  Flame,
  BookOpenCheck,
  Award,
  BookOpen,
  History,
  LogIn,
} from 'lucide-vue-next';

const props = defineProps({
  student: Object,
  cards: Array,
  transactions: Object,
  unlockedCount: Number,
  totalCardsCount: Number,
});

const showAwardModal = ref(false);

const formatDate = (dateStr) => {
  if (!dateStr) return '';
  return new Date(dateStr).toLocaleDateString(undefined, {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};

const loginAsStudent = () => {
  router.post(`/impersonate/${props.student.id}`);
};
</script>
