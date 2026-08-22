<template>
  <AppLayout>
    <div class="space-y-6">
      <!-- Header Banner -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 rounded-3xl bg-white dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 shadow-xl dark:shadow-2xl">
        <div class="space-y-1">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300 border border-blue-500/30 text-xs font-bold uppercase tracking-wider">
            <Users class="w-4 h-4 text-blue-600 dark:text-blue-400" />
            Teacher Classroom Hub
          </div>
          <h2 class="font-heading text-2xl font-bold text-slate-900 dark:text-slate-100">Student Roster & Point Management</h2>
          <p class="text-xs text-slate-600 dark:text-slate-400 max-w-lg">
            Create and edit student accounts, award points for active participation, log in directly as students, and inspect card albums.
          </p>
        </div>

        <div class="flex items-center gap-3">
          <div class="bg-slate-100 dark:bg-slate-950 px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-800 text-right">
            <div class="text-xs text-slate-500 dark:text-slate-400 font-semibold">Total Students</div>
            <div class="text-2xl font-extrabold text-blue-600 dark:text-blue-400 mt-0.5">{{ students.length }}</div>
          </div>

          <button
            @click="openCreateStudentModal"
            type="button"
            class="px-4 py-3 rounded-2xl bg-gradient-to-r from-amber-500 to-emerald-500 text-slate-950 font-extrabold text-xs shadow-lg hover:from-amber-400 hover:to-emerald-400 transition-all flex items-center gap-2 cursor-pointer scale-100 hover:scale-105"
          >
            <UserPlus class="w-4 h-4" />
            Add New Student
          </button>
        </div>
      </div>

      <!-- Search Input -->
      <div class="flex items-center gap-3 p-4 rounded-2xl bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 shadow-sm">
        <Search class="w-5 h-5 text-slate-400 dark:text-slate-500" />
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search student by name or email..."
          class="w-full bg-transparent border-none text-slate-900 dark:text-slate-100 text-sm focus:outline-none placeholder-slate-400 dark:placeholder-slate-500"
        />
      </div>

      <!-- Students Table -->
      <div class="rounded-3xl bg-white dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm text-slate-800 dark:text-slate-200">
            <thead class="bg-slate-100 dark:bg-slate-950 text-slate-600 dark:text-slate-400 uppercase text-[11px] font-mono border-b border-slate-200 dark:border-slate-800">
              <tr>
                <th class="py-4 px-6">Student</th>
                <th class="py-4 px-6">Current Points</th>
                <th class="py-4 px-6">Daily Streak</th>
                <th class="py-4 px-6">Cards Unlocked</th>
                <th class="py-4 px-6 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
              <tr v-for="student in filteredStudents" :key="student.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                <!-- Student Name & Email -->
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

                <!-- Points -->
                <td class="py-4 px-6">
                  <div class="flex items-center gap-1.5 text-amber-600 dark:text-amber-400 font-extrabold text-base">
                    <Coins class="w-4 h-4 text-amber-500" />
                    <span>{{ student.points }} Pts</span>
                  </div>
                </td>

                <!-- Streak -->
                <td class="py-4 px-6">
                  <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-orange-100 dark:bg-orange-500/20 text-orange-800 dark:text-orange-300 border border-orange-200 dark:border-orange-500/30">
                    <Flame class="w-3.5 h-3.5 text-orange-500" />
                    {{ student.current_streak }}d Streak
                  </span>
                </td>

                <!-- Cards Unlocked -->
                <td class="py-4 px-6">
                  <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 dark:text-indigo-300">
                    <BookOpenCheck class="w-4 h-4 text-indigo-500" />
                    {{ student.unlocked_cards_count }} Cards
                  </span>
                </td>

                <!-- Actions -->
                <td class="py-4 px-6 text-right space-x-1.5">
                  <!-- One-Click Login As Student Button -->
                  <button
                    @click="loginAsStudent(student)"
                    type="button"
                    title="Log in as student to assist device-less learners"
                    class="px-3 py-1.5 rounded-xl bg-amber-500/15 hover:bg-amber-500/25 border border-amber-500/40 text-amber-800 dark:text-amber-300 text-xs font-extrabold transition-all inline-flex items-center gap-1 shadow-sm cursor-pointer scale-100 hover:scale-105"
                  >
                    <LogIn class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" />
                    Login as Student
                  </button>

                  <!-- Award / Deduct Points Button -->
                  <button
                    @click="openAwardModal(student)"
                    type="button"
                    title="Award or deduct points with feedback note"
                    class="px-3 py-1.5 rounded-xl bg-emerald-600/15 hover:bg-emerald-600/25 border border-emerald-500/40 text-emerald-800 dark:text-emerald-300 text-xs font-bold transition-all inline-flex items-center gap-1 shadow-sm cursor-pointer"
                  >
                    <Award class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                    Points
                  </button>

                  <!-- View Card Collection & Profile Button -->
                  <Link
                    :href="route('teacher.students.show', student.id)"
                    title="View scholar card album & ledger"
                    class="px-3 py-1.5 rounded-xl bg-indigo-600/15 hover:bg-indigo-600/25 border border-indigo-500/40 text-indigo-800 dark:text-indigo-300 text-xs font-bold transition-all inline-flex items-center gap-1 shadow-sm"
                  >
                    <BookOpen class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" />
                    Cards
                  </Link>

                  <!-- Edit Student Button -->
                  <button
                    @click="openEditStudentModal(student)"
                    type="button"
                    title="Edit Student Profile"
                    class="p-1.5 rounded-lg text-slate-400 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                  >
                    <Edit3 class="w-4 h-4" />
                  </button>

                  <!-- Delete Student Button -->
                  <button
                    @click="deleteStudent(student)"
                    type="button"
                    title="Delete Student Account"
                    class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Award / Deduct Points Modal -->
    <AwardPointModal
      :show="showAwardModal"
      :student="selectedStudent"
      @close="showAwardModal = false"
    />

    <!-- Create / Edit Student Modal -->
    <div v-if="showStudentModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-slate-100 w-full max-w-md rounded-2xl shadow-2xl p-6 relative">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4 mb-4">
          <div class="flex items-center gap-2">
            <UserPlus v-if="!isEditingStudent" class="w-5 h-5 text-amber-500" />
            <Edit3 v-else class="w-5 h-5 text-amber-500" />
            <h3 class="font-heading font-bold text-lg text-slate-900 dark:text-slate-100">
              {{ isEditingStudent ? 'Edit Student Account' : 'Add New Student' }}
            </h3>
          </div>
          <button @click="showStudentModal = false" type="button" class="p-1 text-slate-400 hover:text-slate-800 dark:hover:text-white cursor-pointer">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="submitStudentForm" class="space-y-4 text-xs">
          <div>
            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Student Full Name</label>
            <input
              v-model="studentForm.name"
              type="text"
              required
              placeholder="e.g. Tariq ibn Ziyad"
              class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl py-2 px-3 text-slate-900 dark:text-slate-100 text-xs focus:outline-none focus:border-amber-500"
            />
            <div v-if="studentForm.errors.name" class="text-rose-500 text-[11px] mt-1">{{ studentForm.errors.name }}</div>
          </div>

          <div>
            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Email Address</label>
            <input
              v-model="studentForm.email"
              type="email"
              required
              placeholder="tariq@student.hikmahway.com"
              class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl py-2 px-3 text-slate-900 dark:text-slate-100 text-xs focus:outline-none focus:border-amber-500"
            />
            <div v-if="studentForm.errors.email" class="text-rose-500 text-[11px] mt-1">{{ studentForm.errors.email }}</div>
          </div>

          <div>
            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
              Password {{ isEditingStudent ? '(Leave blank to keep unchanged)' : '' }}
            </label>
            <input
              v-model="studentForm.password"
              type="password"
              :required="!isEditingStudent"
              placeholder="••••••••"
              class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl py-2 px-3 text-slate-900 dark:text-slate-100 text-xs focus:outline-none focus:border-amber-500"
            />
            <div v-if="studentForm.errors.password" class="text-rose-500 text-[11px] mt-1">{{ studentForm.errors.password }}</div>
          </div>

          <div>
            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Points Balance</label>
            <input
              v-model.number="studentForm.points"
              type="number"
              min="0"
              required
              class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl py-2 px-3 text-slate-900 dark:text-slate-100 text-xs focus:outline-none focus:border-amber-500"
            />
          </div>

          <div class="flex justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
            <button type="button" @click="showStudentModal = false" class="px-4 py-2 text-xs font-semibold text-slate-500 dark:text-slate-400 cursor-pointer">Cancel</button>
            <button
              type="submit"
              :disabled="studentForm.processing"
              class="px-5 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs rounded-xl shadow-lg cursor-pointer"
            >
              {{ isEditingStudent ? 'Update Student' : 'Create Student' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import AwardPointModal from '@/Components/AwardPointModal.vue';
import { Users, Search, Coins, Flame, BookOpenCheck, Award, BookOpen, LogIn, UserPlus, Edit3, Trash2, X } from 'lucide-vue-next';

const props = defineProps({
  students: Array,
  teacher: Object,
});

const searchQuery = ref('');
const showAwardModal = ref(false);
const selectedStudent = ref(null);

const showStudentModal = ref(false);
const isEditingStudent = ref(false);
const editingStudentId = ref(null);

const studentForm = useForm({
  name: '',
  email: '',
  password: '',
  points: 100,
});

const filteredStudents = computed(() => {
  if (!searchQuery.value) return props.students;
  const q = searchQuery.value.toLowerCase();
  return props.students.filter((s) => s.name.toLowerCase().includes(q) || s.email.toLowerCase().includes(q));
});

const openAwardModal = (student) => {
  selectedStudent.value = student;
  showAwardModal.value = true;
};

const openCreateStudentModal = () => {
  isEditingStudent.value = false;
  editingStudentId.value = null;
  studentForm.reset();
  studentForm.points = 100;
  showStudentModal.value = true;
};

const openEditStudentModal = (student) => {
  isEditingStudent.value = true;
  editingStudentId.value = student.id;
  studentForm.name = student.name;
  studentForm.email = student.email;
  studentForm.password = '';
  studentForm.points = student.points;
  showStudentModal.value = true;
};

const submitStudentForm = () => {
  if (isEditingStudent.value) {
    studentForm.put(`/teacher/students/${editingStudentId.value}`, {
      onSuccess: () => (showStudentModal.value = false),
    });
  } else {
    studentForm.post('/teacher/students', {
      onSuccess: () => (showStudentModal.value = false),
    });
  }
};

const deleteStudent = (student) => {
  if (confirm(`Are you sure you want to delete student account '${student.name}'?`)) {
    router.delete(`/teacher/students/${student.id}`);
  }
};

const loginAsStudent = (student) => {
  router.post(`/impersonate/${student.id}`);
};
</script>
