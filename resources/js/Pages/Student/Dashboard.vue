<template>
  <AppLayout>
    <div class="space-y-6">
      <!-- Daily Streak Banner -->
      <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-amber-600 via-slate-900 to-emerald-700 dark:from-amber-900/60 dark:via-slate-900 dark:to-emerald-900/60 border border-amber-400/40 dark:border-amber-500/30 p-6 md:p-8 shadow-2xl">
        <div class="absolute top-0 right-0 -mt-12 -mr-12 w-64 h-64 bg-amber-400/20 dark:bg-amber-500/10 rounded-full blur-3xl"></div>

        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
          <div class="space-y-2">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-400/20 text-amber-200 border border-amber-400/30 text-xs font-bold uppercase tracking-wider">
              <Flame class="w-4 h-4 text-amber-300 animate-pulse fill-amber-400/40" />
              Learning Streak Engine
            </div>
            <h2 class="font-heading text-2xl md:text-3xl font-extrabold text-white">
              Welcome back, <span class="bg-gradient-to-r from-amber-200 via-amber-100 to-emerald-200 bg-clip-text text-transparent">{{ $page.props.auth.user.name }}</span>!
            </h2>
            <p class="text-sm text-slate-200 max-w-xl">
              Keep your daily streak alive to earn extra points and unlock legendary Hadith Scholar cards!
            </p>
          </div>

          <!-- Streak Claim Action -->
          <div class="flex flex-col sm:flex-row items-center gap-4 bg-slate-950/80 p-4 rounded-2xl border border-slate-800 shadow-xl">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-xl bg-orange-500/20 border border-orange-500/40 flex items-center justify-center">
                <Flame class="w-7 h-7 text-orange-400 fill-orange-400/40" />
              </div>
              <div>
                <div class="text-xl font-extrabold text-orange-300">{{ $page.props.auth.user.current_streak }} Days</div>
                <div class="text-[11px] text-slate-400">Current Daily Streak</div>
              </div>
            </div>

            <button
              @click="claimStreak"
              type="button"
              class="w-full sm:w-auto px-5 py-3 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 text-slate-950 font-bold text-xs shadow-lg hover:from-amber-400 hover:to-orange-400 transition-all flex items-center justify-center gap-2 whitespace-nowrap cursor-pointer scale-100 hover:scale-105"
            >
              <Sparkles class="w-4 h-4" />
              Claim Daily Check-In (+15 Pts)
            </button>
          </div>
        </div>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Spendable Points -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900/80 border border-amber-500/30 shadow-xl flex items-center justify-between">
          <div>
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Spendable Points</div>
            <div class="text-2xl font-extrabold text-amber-600 dark:text-amber-400 mt-1">{{ $page.props.auth.user.points }}</div>
            <div class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">Available to spend</div>
          </div>
          <div class="w-12 h-12 rounded-xl bg-amber-500/20 border border-amber-500/30 flex items-center justify-center text-amber-600 dark:text-amber-400">
            <Coins class="w-6 h-6" />
          </div>
        </div>

        <!-- Total Lifetime Earned -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900/80 border border-emerald-500/30 shadow-xl flex items-center justify-between">
          <div>
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Lifetime Points</div>
            <div class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">{{ $page.props.auth.user.total_points_earned }}</div>
            <div class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">Total points accumulated</div>
          </div>
          <div class="w-12 h-12 rounded-xl bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
            <Award class="w-6 h-6" />
          </div>
        </div>

        <!-- Unlocked Cards -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900/80 border border-indigo-500/30 shadow-xl flex items-center justify-between">
          <div>
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Cards Collected</div>
            <div class="text-2xl font-extrabold text-indigo-600 dark:text-indigo-400 mt-1">{{ unlockedCardsCount }} / {{ totalActiveCardsCount }}</div>
            <div class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">Scholar Card Album</div>
          </div>
          <div class="w-12 h-12 rounded-xl bg-indigo-500/20 border border-indigo-500/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
            <BookOpenCheck class="w-6 h-6" />
          </div>
        </div>

        <!-- My Teacher -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900/80 border border-blue-500/30 shadow-xl flex items-center justify-between">
          <div>
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Assigned Teacher</div>
            <div class="text-base font-bold text-blue-600 dark:text-blue-300 mt-1 truncate max-w-[140px]">
              {{ $page.props.auth.user.teacher ? $page.props.auth.user.teacher.name : 'Unassigned' }}
            </div>
            <div class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">Points & Feedback</div>
          </div>
          <div class="w-12 h-12 rounded-xl bg-blue-500/20 border border-blue-500/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
            <UserCheck class="w-6 h-6" />
          </div>
        </div>
      </div>

      <!-- Next Card & Recent Activity Split Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Next Target Card Spotlight -->
        <div class="lg:col-span-1 p-6 rounded-3xl bg-white dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 shadow-xl flex flex-col justify-between">
          <div>
            <div class="flex items-center justify-between mb-4">
              <h3 class="font-heading text-lg font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                <Target class="w-5 h-5 text-amber-500" />
                Next Card Target
              </h3>
              <Link href="/cards" class="text-xs text-amber-600 dark:text-amber-400 hover:underline font-semibold">
                View All →
              </Link>
            </div>

            <div v-if="nextCard" class="bg-slate-50 dark:bg-slate-950 p-4 rounded-2xl border border-amber-500/30 text-center space-y-3">
              <div class="w-20 h-20 mx-auto rounded-full p-1 border-2 border-amber-500 bg-white dark:bg-slate-900 flex items-center justify-center shadow-lg">
                <BookOpen class="w-10 h-10 text-amber-500" />
              </div>
              <div>
                <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded bg-amber-100 dark:bg-slate-800 text-amber-900 dark:text-amber-300">
                  {{ nextCard.rarity }}
                </span>
                <h4 class="font-heading font-bold text-slate-900 dark:text-slate-100 text-base mt-1">{{ nextCard.name }}</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-1">{{ nextCard.title }}</p>
              </div>

              <!-- Cost Progress Bar -->
              <div class="space-y-1">
                <div class="flex justify-between text-xs text-slate-500 dark:text-slate-400 font-mono">
                  <span>Progress</span>
                  <span class="text-amber-600 dark:text-amber-400 font-bold">{{ $page.props.auth.user.points }} / {{ nextCard.unlock_cost }} Pts</span>
                </div>
                <div class="w-full bg-slate-200 dark:bg-slate-900 h-2.5 rounded-full overflow-hidden border border-slate-300 dark:border-slate-800">
                  <div
                    class="bg-gradient-to-r from-amber-500 to-emerald-500 h-full rounded-full transition-all duration-500"
                    :style="{ width: Math.min(100, ($page.props.auth.user.points / nextCard.unlock_cost) * 100) + '%' }"
                  ></div>
                </div>
              </div>
            </div>

            <div v-else class="bg-slate-50 dark:bg-slate-950 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 text-center text-slate-500 dark:text-slate-400 text-xs">
              <CheckCircle2 class="w-8 h-8 text-emerald-500 mx-auto mb-2" />
              Amazing! You have collected all available cards!
            </div>
          </div>

          <Link
            href="/cards"
            class="mt-4 w-full py-2.5 px-4 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-semibold text-xs text-center block transition-colors"
          >
            Open Scholar Card Album
          </Link>
        </div>

        <!-- Recent Points & Teacher Feedback Activity -->
        <div class="lg:col-span-2 p-6 rounded-3xl bg-white dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 shadow-xl">
          <div class="flex items-center justify-between mb-4">
            <h3 class="font-heading text-lg font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
              <History class="w-5 h-5 text-indigo-500" />
              Recent Points & Teacher Feedback
            </h3>
            <Link href="/profile" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline font-semibold">
              Full History →
            </Link>
          </div>

          <div v-if="recentTransactions.length > 0" class="space-y-3">
            <div
              v-for="tx in recentTransactions"
              :key="tx.id"
              class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800/80 flex items-start justify-between gap-3 hover:border-slate-300 dark:hover:border-slate-700 transition-colors"
            >
              <div class="flex items-start gap-3">
                <div
                  :class="[
                    'w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5',
                    tx.points > 0 ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'bg-rose-500/20 text-rose-600 dark:text-rose-400'
                  ]"
                >
                  <PlusCircle v-if="tx.points > 0" class="w-5 h-5" />
                  <MinusCircle v-else class="w-5 h-5" />
                </div>
                <div>
                  <div class="text-sm font-semibold text-slate-900 dark:text-slate-200">
                    {{ tx.reason.replace('_', ' ').toUpperCase() }}
                  </div>
                  <p v-if="tx.note" class="text-xs text-amber-800 dark:text-amber-200/90 mt-0.5 italic">
                    "{{ tx.note }}"
                  </p>
                  <div class="text-[11px] text-slate-500 mt-1 flex items-center gap-2">
                    <span v-if="tx.teacher" class="text-blue-600 dark:text-blue-400 font-medium">By {{ tx.teacher.name }}</span>
                    <span>• {{ formatDate(tx.created_at) }}</span>
                  </div>
                </div>
              </div>

              <div
                :class="[
                  'text-base font-extrabold px-3 py-1 rounded-xl',
                  tx.points > 0 ? 'bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-500/20' : 'bg-rose-100 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border border-rose-300 dark:border-rose-500/20'
                ]"
              >
                {{ tx.points > 0 ? '+' : '' }}{{ tx.points }} Pts
              </div>
            </div>
          </div>

          <div v-else class="p-8 text-center text-slate-500 text-xs bg-slate-50 dark:bg-slate-950 rounded-2xl">
            No point transactions recorded yet. Keep learning and participating!
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import {
  Flame,
  Coins,
  Award,
  BookOpenCheck,
  UserCheck,
  Target,
  History,
  Sparkles,
  BookOpen,
  PlusCircle,
  MinusCircle,
  CheckCircle2,
} from 'lucide-vue-next';

defineProps({
  recentTransactions: Array,
  unlockedCardsCount: Number,
  totalActiveCardsCount: Number,
  nextCard: Object,
});

const claimStreak = () => {
  router.post('/dashboard/streak');
};

const formatDate = (dateStr) => {
  if (!dateStr) return '';
  return new Date(dateStr).toLocaleDateString(undefined, {
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};
</script>
