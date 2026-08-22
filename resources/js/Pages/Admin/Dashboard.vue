<template>
  <AppLayout>
    <div class="space-y-6">
      <!-- Header Banner -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 rounded-3xl bg-white dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 shadow-xl dark:shadow-2xl">
        <div class="space-y-1">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-500/10 dark:bg-purple-500/20 text-purple-700 dark:text-purple-300 border border-purple-500/30 text-xs font-bold uppercase tracking-wider">
            <ShieldCheck class="w-4 h-4 text-purple-600 dark:text-purple-400" />
            Admin Platform Oversight
          </div>
          <h2 class="font-heading text-2xl font-bold text-slate-900 dark:text-slate-100">System Dashboard & Analytics</h2>
          <p class="text-xs text-slate-600 dark:text-slate-400 max-w-lg">
            Monitor point economy, active student streaks, scholar card claims, and manage platform users and cards.
          </p>
        </div>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900/80 border border-indigo-500/30 shadow-xl flex items-center justify-between">
          <div>
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Students</div>
            <div class="text-2xl font-extrabold text-indigo-600 dark:text-indigo-400 mt-1">{{ stats.total_students }}</div>
            <div class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">{{ stats.total_teachers }} Teachers assigned</div>
          </div>
          <div class="w-12 h-12 rounded-xl bg-indigo-500/20 border border-indigo-500/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
            <Users class="w-6 h-6" />
          </div>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900/80 border border-amber-500/30 shadow-xl flex items-center justify-between">
          <div>
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Points in Circulation</div>
            <div class="text-2xl font-extrabold text-amber-600 dark:text-amber-400 mt-1">{{ stats.total_points_in_circulation }}</div>
            <div class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">{{ stats.total_points_earned_lifetime }} lifetime pts</div>
          </div>
          <div class="w-12 h-12 rounded-xl bg-amber-500/20 border border-amber-500/30 flex items-center justify-center text-amber-600 dark:text-amber-400">
            <Coins class="w-6 h-6" />
          </div>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900/80 border border-emerald-500/30 shadow-xl flex items-center justify-between">
          <div>
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Scholar Cards Deck</div>
            <div class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">{{ stats.total_cards }} Cards</div>
            <div class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">{{ stats.total_cards_unlocked }} Cards unlocked</div>
          </div>
          <div class="w-12 h-12 rounded-xl bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
            <Layers class="w-6 h-6" />
          </div>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900/80 border border-purple-500/30 shadow-xl flex items-center justify-between">
          <div>
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Platform Status</div>
            <div class="text-base font-bold text-emerald-600 dark:text-emerald-400 mt-1 flex items-center gap-1.5">
              <CheckCircle2 class="w-4 h-4" /> Gamification Active
            </div>
            <div class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">Streaks & Cards enabled</div>
          </div>
          <div class="w-12 h-12 rounded-xl bg-purple-500/20 border border-purple-500/30 flex items-center justify-center text-purple-600 dark:text-purple-400">
            <Activity class="w-6 h-6" />
          </div>
        </div>
      </div>

      <!-- Top Students & Recent Transactions Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Top Students Leaderboard -->
        <div class="lg:col-span-1 p-6 rounded-3xl bg-white dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 shadow-xl space-y-4">
          <h3 class="font-heading text-lg font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
            <Trophy class="w-5 h-5 text-amber-500" />
            Top Points Leaderboard
          </h3>

          <div class="space-y-3">
            <div
              v-for="(st, idx) in topStudents"
              :key="st.id"
              class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 flex items-center justify-between gap-3"
            >
              <div class="flex items-center gap-3">
                <div
                  :class="[
                    'w-7 h-7 rounded-xl font-mono text-xs font-extrabold flex items-center justify-center',
                    idx === 0 ? 'bg-amber-500 text-slate-950' :
                    idx === 1 ? 'bg-slate-300 text-slate-950' :
                    idx === 2 ? 'bg-amber-700 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-400'
                  ]"
                >
                  #{{ idx + 1 }}
                </div>
                <div>
                  <div class="text-sm font-bold text-slate-900 dark:text-slate-200">{{ st.name }}</div>
                  <div class="text-[11px] text-slate-500">{{ st.current_streak }}d streak</div>
                </div>
              </div>

              <div class="text-right">
                <div class="text-sm font-extrabold text-amber-600 dark:text-amber-400">{{ st.total_points_earned }} Pts</div>
                <div class="text-[10px] text-slate-500">Lifetime</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Audit Log -->
        <div class="lg:col-span-2 p-6 rounded-3xl bg-white dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 shadow-xl space-y-4">
          <h3 class="font-heading text-lg font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
            <History class="w-5 h-5 text-indigo-500" />
            System Transaction Audit Log
          </h3>

          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
              <thead class="bg-slate-100 dark:bg-slate-950 text-slate-500 dark:text-slate-400 uppercase text-[10px] font-mono border-b border-slate-200 dark:border-slate-800">
                <tr>
                  <th class="py-3 px-4">User</th>
                  <th class="py-3 px-4">Reason</th>
                  <th class="py-3 px-4">Staff / System</th>
                  <th class="py-3 px-4 text-right">Points</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                <tr v-for="tx in recentTransactions" :key="tx.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                  <td class="py-3 px-4 font-bold text-slate-900 dark:text-slate-200">{{ tx.user ? tx.user.name : '-' }}</td>
                  <td class="py-3 px-4 text-slate-800 dark:text-slate-300 font-semibold">{{ tx.reason.replace('_', ' ').toUpperCase() }}</td>
                  <td class="py-3 px-4 text-blue-600 dark:text-blue-400 font-medium">{{ tx.teacher ? tx.teacher.name : 'System' }}</td>
                  <td :class="['py-3 px-4 font-extrabold text-right text-sm', tx.points > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400']">
                    {{ tx.points > 0 ? '+' : '' }}{{ tx.points }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import {
  ShieldCheck,
  Users,
  Coins,
  Layers,
  Activity,
  CheckCircle2,
  Trophy,
  History,
} from 'lucide-vue-next';

defineProps({
  stats: Object,
  recentTransactions: Array,
  topStudents: Array,
});
</script>
