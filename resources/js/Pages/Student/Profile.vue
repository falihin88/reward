<template>
  <AppLayout>
    <div class="space-y-6">
      <!-- Profile Header -->
      <div class="p-6 md:p-8 rounded-3xl bg-white dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 shadow-xl dark:shadow-2xl flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden">
        <div class="flex items-center gap-5 z-10">
          <div class="w-20 h-20 rounded-2xl bg-gradient-to-tr from-amber-500 to-emerald-500 p-1 shadow-xl">
            <div class="w-full h-full bg-slate-950 rounded-xl flex items-center justify-center text-2xl font-extrabold text-amber-400 font-heading">
              {{ student.name.charAt(0) }}
            </div>
          </div>
          <div>
            <h2 class="font-heading text-2xl font-bold text-slate-900 dark:text-slate-100">{{ student.name }}</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ student.email }}</p>
            <div class="flex items-center gap-2 mt-2">
              <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30">
                Student Profile
              </span>
              <span v-if="student.teacher" class="text-xs text-blue-600 dark:text-blue-400 font-medium">
                Teacher: {{ student.teacher.name }}
              </span>
            </div>
          </div>
        </div>

        <!-- Quick Summary Stats -->
        <div class="flex items-center gap-4 z-10">
          <div class="text-center px-4 py-2 bg-slate-100 dark:bg-slate-950 rounded-2xl border border-slate-200 dark:border-slate-800">
            <div class="text-lg font-extrabold text-amber-600 dark:text-amber-400">{{ student.points }}</div>
            <div class="text-[10px] text-slate-500 uppercase font-semibold">Spendable Pts</div>
          </div>
          <div class="text-center px-4 py-2 bg-slate-100 dark:bg-slate-950 rounded-2xl border border-slate-200 dark:border-slate-800">
            <div class="text-lg font-extrabold text-orange-600 dark:text-orange-400">{{ student.current_streak }}d</div>
            <div class="text-[10px] text-slate-500 uppercase font-semibold">Streak</div>
          </div>
          <div class="text-center px-4 py-2 bg-slate-100 dark:bg-slate-950 rounded-2xl border border-slate-200 dark:border-slate-800">
            <div class="text-lg font-extrabold text-indigo-600 dark:text-indigo-400">{{ unlockedCardsCount }}</div>
            <div class="text-[10px] text-slate-500 uppercase font-semibold">Cards</div>
          </div>
        </div>
      </div>

      <!-- Teacher Feedback Feed -->
      <div class="p-6 rounded-3xl bg-white dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 shadow-xl space-y-4">
        <h3 class="font-heading text-lg font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
          <MessageSquareQuote class="w-5 h-5 text-amber-500" />
          Teacher Comments & Praise Feed
        </h3>

        <div v-if="teacherComments.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div
            v-for="c in teacherComments"
            :key="c.id"
            class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-amber-500/30 shadow-md flex flex-col justify-between space-y-3"
          >
            <div class="space-y-1">
              <div class="flex items-center justify-between text-xs">
                <span class="font-bold text-blue-600 dark:text-blue-400">{{ c.teacher?.name || 'Teacher' }}</span>
                <span :class="['font-extrabold px-2 py-0.5 rounded text-[11px]', c.points > 0 ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300' : 'bg-rose-100 dark:bg-rose-500/20 text-rose-800 dark:text-rose-300']">
                  {{ c.points > 0 ? '+' : '' }}{{ c.points }} Pts
                </span>
              </div>
              <p class="text-xs text-slate-800 dark:text-slate-200 leading-relaxed italic">
                "{{ c.note }}"
              </p>
            </div>
            <div class="text-[10px] text-slate-500 text-right font-mono">
              {{ formatDate(c.created_at) }}
            </div>
          </div>
        </div>

        <div v-else class="p-6 text-center text-slate-500 text-xs bg-slate-50 dark:bg-slate-950 rounded-2xl">
          No teacher comments received yet.
        </div>
      </div>

      <!-- Complete Point Audit Ledger -->
      <div class="p-6 rounded-3xl bg-white dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 shadow-xl space-y-4">
        <h3 class="font-heading text-lg font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
          <History class="w-5 h-5 text-indigo-500" />
          Point Transaction History
        </h3>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
            <thead class="bg-slate-100 dark:bg-slate-950 text-slate-500 dark:text-slate-400 uppercase text-[10px] font-mono border-b border-slate-200 dark:border-slate-800">
              <tr>
                <th class="py-3 px-4">Date</th>
                <th class="py-3 px-4">Reason</th>
                <th class="py-3 px-4">Awarded By</th>
                <th class="py-3 px-4">Note / Comment</th>
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
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { MessageSquareQuote, History } from 'lucide-vue-next';

defineProps({
  student: Object,
  transactions: Object,
  teacherComments: Array,
  unlockedCardsCount: Number,
});

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
</script>
