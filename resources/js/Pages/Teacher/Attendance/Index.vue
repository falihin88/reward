<template>
  <AppLayout>
    <div class="space-y-6">
      <!-- Header Banner -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 rounded-3xl bg-white dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 shadow-xl dark:shadow-2xl">
        <div class="space-y-1">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30 text-xs font-bold uppercase tracking-wider">
            <CalendarCheck class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
            Daily Attendance Register
          </div>
          <h2 class="font-heading text-2xl font-bold text-slate-900 dark:text-slate-100">Classroom Attendance & Point Reward Engine</h2>
          <p class="text-xs text-slate-600 dark:text-slate-400 max-w-lg">
            Mark daily attendance for students. Students marked as <strong class="text-emerald-600 dark:text-emerald-400 font-bold">Present receive +100 Points</strong> and <strong class="text-amber-600 dark:text-amber-400 font-bold">Late receive +50 Points</strong> automatically!
          </p>
        </div>

        <!-- Date Picker & Quick Actions -->
        <div class="flex flex-wrap items-center gap-3">
          <div class="flex items-center gap-2 bg-slate-100 dark:bg-slate-950 px-3 py-2 rounded-2xl border border-slate-200 dark:border-slate-800">
            <Calendar class="w-4 h-4 text-amber-500" />
            <input
              type="date"
              v-model="dateInput"
              @change="changeDate"
              class="bg-transparent border-none text-xs font-bold text-slate-900 dark:text-slate-100 focus:outline-none cursor-pointer"
            />
          </div>

          <button
            @click="markAllPresent"
            type="button"
            class="px-4 py-2.5 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs shadow-md transition-all flex items-center gap-1.5 cursor-pointer scale-100 hover:scale-105"
          >
            <CheckCircle2 class="w-4 h-4 text-slate-950" />
            Mark All Present (+100 Pts)
          </button>
        </div>
      </div>

      <!-- Attendance Metrics Summary Grid -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900/80 border border-emerald-500/30 shadow-md flex items-center justify-between">
          <div>
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Present (+100 Pts)</div>
            <div class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-0.5">{{ countByStatus('present') }}</div>
          </div>
          <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
            <CheckCircle2 class="w-5 h-5" />
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900/80 border border-amber-500/30 shadow-md flex items-center justify-between">
          <div>
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Late (+50 Pts)</div>
            <div class="text-2xl font-extrabold text-amber-600 dark:text-amber-400 mt-0.5">{{ countByStatus('late') }}</div>
          </div>
          <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold">
            <Clock class="w-5 h-5" />
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900/80 border border-rose-500/30 shadow-md flex items-center justify-between">
          <div>
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Absent (0 Pts)</div>
            <div class="text-2xl font-extrabold text-rose-600 dark:text-rose-400 mt-0.5">{{ countByStatus('absent') }}</div>
          </div>
          <div class="w-10 h-10 rounded-xl bg-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold">
            <XCircle class="w-5 h-5" />
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900/80 border border-blue-500/30 shadow-md flex items-center justify-between">
          <div>
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Excused (0 Pts)</div>
            <div class="text-2xl font-extrabold text-blue-600 dark:text-blue-400 mt-0.5">{{ countByStatus('excused') }}</div>
          </div>
          <div class="w-10 h-10 rounded-xl bg-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold">
            <AlertCircle class="w-5 h-5" />
          </div>
        </div>
      </div>

      <!-- Attendance Table Form -->
      <form @submit.prevent="saveAttendance" class="space-y-6">
        <div class="rounded-3xl bg-white dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 shadow-xl overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-800 dark:text-slate-200">
              <thead class="bg-slate-100 dark:bg-slate-950 text-slate-500 dark:text-slate-400 uppercase text-[11px] font-mono border-b border-slate-200 dark:border-slate-800">
                <tr>
                  <th class="py-4 px-6">Student</th>
                  <th class="py-4 px-6">Attendance Status</th>
                  <th class="py-4 px-6">Award Preview</th>
                  <th class="py-4 px-6">Notes / Reason</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                <tr v-for="student in students" :key="student.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                  <!-- Student Info -->
                  <td class="py-4 px-6">
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-emerald-500 p-0.5 shadow-md">
                        <div class="w-full h-full bg-slate-950 rounded-[10px] flex items-center justify-center font-bold text-amber-400">
                          {{ student.name.charAt(0) }}
                        </div>
                      </div>
                      <div>
                        <div class="font-bold text-slate-900 dark:text-slate-100">{{ student.name }}</div>
                        <div class="text-xs text-slate-500 dark:text-slate-400">{{ student.email }}</div>
                      </div>
                    </div>
                  </td>

                  <!-- Status Selector Buttons -->
                  <td class="py-4 px-6">
                    <div class="flex flex-wrap items-center gap-1.5">
                      <button
                        type="button"
                        @click="setStatus(student.id, 'present')"
                        :class="[
                          'px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1 cursor-pointer',
                          recordsMap[student.id]?.status === 'present'
                            ? 'bg-emerald-500 text-slate-950 shadow-md ring-2 ring-emerald-400 scale-105'
                            : 'bg-slate-100 dark:bg-slate-950 text-slate-600 dark:text-slate-400 hover:bg-emerald-500/20 border border-slate-200 dark:border-slate-800'
                        ]"
                      >
                        <CheckCircle2 class="w-3.5 h-3.5" />
                        Present (+100 Pts)
                      </button>

                      <button
                        type="button"
                        @click="setStatus(student.id, 'late')"
                        :class="[
                          'px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1 cursor-pointer',
                          recordsMap[student.id]?.status === 'late'
                            ? 'bg-amber-500 text-slate-950 shadow-md ring-2 ring-amber-400 scale-105'
                            : 'bg-slate-100 dark:bg-slate-950 text-slate-600 dark:text-slate-400 hover:bg-amber-500/20 border border-slate-200 dark:border-slate-800'
                        ]"
                      >
                        <Clock class="w-3.5 h-3.5" />
                        Late (+50 Pts)
                      </button>

                      <button
                        type="button"
                        @click="setStatus(student.id, 'absent')"
                        :class="[
                          'px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1 cursor-pointer',
                          recordsMap[student.id]?.status === 'absent'
                            ? 'bg-rose-500 text-white shadow-md ring-2 ring-rose-400 scale-105'
                            : 'bg-slate-100 dark:bg-slate-950 text-slate-600 dark:text-slate-400 hover:bg-rose-500/20 border border-slate-200 dark:border-slate-800'
                        ]"
                      >
                        <XCircle class="w-3.5 h-3.5" />
                        Absent (0 Pts)
                      </button>

                      <button
                        type="button"
                        @click="setStatus(student.id, 'excused')"
                        :class="[
                          'px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1 cursor-pointer',
                          recordsMap[student.id]?.status === 'excused'
                            ? 'bg-blue-500 text-white shadow-md ring-2 ring-blue-400 scale-105'
                            : 'bg-slate-100 dark:bg-slate-950 text-slate-600 dark:text-slate-400 hover:bg-blue-500/20 border border-slate-200 dark:border-slate-800'
                        ]"
                      >
                        <AlertCircle class="w-3.5 h-3.5" />
                        Excused
                      </button>
                    </div>
                  </td>

                  <!-- Award Preview Badge -->
                  <td class="py-4 px-6">
                    <span
                      v-if="recordsMap[student.id]?.status === 'present'"
                      class="px-2.5 py-1 rounded-full text-xs font-extrabold bg-emerald-500/20 text-emerald-600 dark:text-emerald-300 border border-emerald-500/40 inline-flex items-center gap-1"
                    >
                      <Coins class="w-3.5 h-3.5 text-amber-500" />
                      +100 Pts
                    </span>
                    <span
                      v-else-if="recordsMap[student.id]?.status === 'late'"
                      class="px-2.5 py-1 rounded-full text-xs font-extrabold bg-amber-500/20 text-amber-600 dark:text-amber-300 border border-amber-500/40 inline-flex items-center gap-1"
                    >
                      <Coins class="w-3.5 h-3.5 text-amber-500" />
                      +50 Pts
                    </span>
                    <span v-else class="text-xs text-slate-400 font-mono">0 Pts</span>
                  </td>

                  <!-- Notes Input -->
                  <td class="py-4 px-6">
                    <input
                      type="text"
                      v-model="recordsMap[student.id].notes"
                      placeholder="Optional note e.g. Doctor's note..."
                      class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl py-1.5 px-3 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:border-amber-500"
                    />
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end">
          <button
            type="submit"
            :disabled="form.processing"
            class="px-6 py-3 rounded-2xl bg-gradient-to-r from-amber-500 via-emerald-500 to-indigo-500 text-slate-950 font-extrabold text-sm shadow-xl hover:scale-105 transition-all flex items-center gap-2 cursor-pointer"
          >
            <Send class="w-4 h-4 text-slate-950" />
            Save Attendance Batch & Award Points
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, watch, onMounted } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import {
  CalendarCheck,
  Calendar,
  CheckCircle2,
  Clock,
  XCircle,
  AlertCircle,
  Coins,
  Send,
} from 'lucide-vue-next';

const props = defineProps({
  students: Array,
  attendances: Object,
  selectedDate: String,
});

const dateInput = ref(props.selectedDate);
const recordsMap = reactive({});

const initializeRecords = () => {
  if (!props.students) return;
  props.students.forEach((student) => {
    const existing = props.attendances && typeof props.attendances === 'object' ? props.attendances[student.id] : null;
    recordsMap[student.id] = {
      student_id: student.id,
      status: existing ? existing.status : 'present',
      notes: existing ? existing.notes : '',
    };
  });
};

onMounted(() => {
  initializeRecords();
});

watch([() => props.attendances, () => props.students], () => {
  initializeRecords();
});

const setStatus = (studentId, status) => {
  if (recordsMap[studentId]) {
    recordsMap[studentId].status = status;
  }
};

const markAllPresent = () => {
  props.students.forEach((student) => {
    if (recordsMap[student.id]) {
      recordsMap[student.id].status = 'present';
    }
  });
};

const countByStatus = (status) => {
  return Object.values(recordsMap).filter((r) => r.status === status).length;
};

const changeDate = () => {
  router.get(route('teacher.attendance.index'), { date: dateInput.value }, { preserveState: false });
};

const form = useForm({
  date: '',
  records: [],
});

const saveAttendance = () => {
  form.date = dateInput.value;
  form.records = Object.values(recordsMap);
  form.post(route('teacher.attendance.store'));
};
</script>
