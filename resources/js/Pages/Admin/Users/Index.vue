<template>
  <AppLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 rounded-3xl bg-white dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 shadow-xl dark:shadow-2xl">
        <div class="space-y-1">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/10 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 border border-indigo-500/30 text-xs font-bold uppercase tracking-wider">
            <Users class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
            User & Student Management
          </div>
          <h2 class="font-heading text-2xl font-bold text-slate-900 dark:text-slate-100">Platform Accounts & Student CRUD</h2>
          <p class="text-xs text-slate-600 dark:text-slate-400 max-w-lg">
            Create, edit, and manage Admins, Teachers, and Students. Assign students to specific responsible Teachers.
          </p>
        </div>

        <button
          @click="openCreateModal"
          type="button"
          class="px-5 py-3 rounded-2xl bg-gradient-to-r from-amber-500 to-emerald-500 text-slate-950 font-bold text-xs shadow-lg hover:from-amber-400 hover:to-emerald-400 transition-all flex items-center gap-2 cursor-pointer scale-100 hover:scale-105"
        >
          <UserPlus class="w-4 h-4" />
          Create New Account
        </button>
      </div>

      <!-- Filters & Search Bar -->
      <div class="flex flex-wrap items-center justify-between gap-4 p-4 rounded-2xl bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 shadow-sm">
        <!-- Role Tabs -->
        <div class="flex items-center gap-2">
          <button
            v-for="tab in roleTabs"
            :key="tab.value"
            @click="selectedRole = tab.value"
            type="button"
            :class="[
              'px-3 py-1.5 rounded-xl text-xs font-semibold transition-all cursor-pointer',
              selectedRole === tab.value
                ? 'bg-amber-500 text-slate-950 shadow-md font-bold'
                : 'bg-slate-100 dark:bg-slate-950 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white border border-slate-200 dark:border-slate-800'
            ]"
          >
            {{ tab.label }}
          </button>
        </div>

        <!-- Search Input -->
        <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-xs min-w-[240px]">
          <Search class="w-4 h-4 text-slate-400" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search by name or email..."
            class="bg-transparent border-none w-full text-slate-900 dark:text-slate-100 focus:outline-none placeholder-slate-400"
          />
        </div>
      </div>

      <!-- Users Table -->
      <div class="rounded-3xl bg-white dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm text-slate-800 dark:text-slate-200">
            <thead class="bg-slate-100 dark:bg-slate-950 text-slate-500 dark:text-slate-400 uppercase text-[11px] font-mono border-b border-slate-200 dark:border-slate-800">
              <tr>
                <th class="py-4 px-6">Name & Email</th>
                <th class="py-4 px-6">Role</th>
                <th class="py-4 px-6">Assigned Teacher</th>
                <th class="py-4 px-6">Points</th>
                <th class="py-4 px-6 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
              <tr v-for="user in filteredUsers" :key="user.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                <td class="py-4 px-6">
                  <div class="font-bold text-slate-900 dark:text-slate-100">{{ user.name }}</div>
                  <div class="text-xs text-slate-500 dark:text-slate-400">{{ user.email }}</div>
                </td>

                <td class="py-4 px-6">
                  <span
                    :class="[
                      'px-2.5 py-1 rounded-full text-xs font-semibold uppercase tracking-wider',
                      user.role === 'admin' ? 'bg-purple-100 dark:bg-purple-500/20 text-purple-800 dark:text-purple-300 border border-purple-200 dark:border-purple-500/30' :
                      user.role === 'teacher' ? 'bg-blue-100 dark:bg-blue-500/20 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-500/30' :
                      'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30'
                    ]"
                  >
                    {{ user.role }}
                  </span>
                </td>

                <td class="py-4 px-6">
                  <span v-if="user.role === 'student' && user.teacher" class="text-xs text-blue-600 dark:text-blue-400 font-medium">
                    {{ user.teacher.name }}
                  </span>
                  <span v-else-if="user.role === 'student'" class="text-xs text-slate-400 italic">
                    Unassigned
                  </span>
                  <span v-else class="text-xs text-slate-400">-</span>
                </td>

                <td class="py-4 px-6 font-mono font-bold text-amber-600 dark:text-amber-400">
                  <span v-if="user.role === 'student'">{{ user.points }} Pts</span>
                  <span v-else class="text-slate-400">-</span>
                </td>

                <td class="py-4 px-6 text-right space-x-2">
                  <button
                    @click="openEditModal(user)"
                    type="button"
                    class="p-2 rounded-lg text-slate-400 hover:text-amber-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                    title="Edit Account"
                  >
                    <Edit3 class="w-4 h-4" />
                  </button>
                  <button
                    @click="deleteUser(user)"
                    type="button"
                    class="p-2 rounded-lg text-slate-400 hover:text-rose-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                    title="Delete Account"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </td>
              </tr>
              <tr v-if="filteredUsers.length === 0">
                <td colspan="5" class="py-8 text-center text-slate-400 text-xs">
                  No accounts found matching your selected role/search filters.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-slate-100 w-full max-w-md rounded-2xl shadow-2xl p-6 relative">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4 mb-4">
          <h3 class="font-heading font-bold text-lg text-slate-900 dark:text-slate-100">
            {{ isEditing ? 'Edit Account' : 'Create New Account' }}
          </h3>
          <button @click="showModal = false" type="button" class="p-1 text-slate-400 hover:text-slate-800 dark:hover:text-white cursor-pointer">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="submitForm" class="space-y-4 text-xs">
          <div>
            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Full Name</label>
            <input
              v-model="form.name"
              type="text"
              required
              class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl py-2 px-3 text-slate-900 dark:text-slate-100 text-xs focus:outline-none focus:border-amber-500"
            />
          </div>

          <div>
            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Email Address</label>
            <input
              v-model="form.email"
              type="email"
              required
              class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl py-2 px-3 text-slate-900 dark:text-slate-100 text-xs focus:outline-none focus:border-amber-500"
            />
          </div>

          <div>
            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Password {{ isEditing ? '(Leave blank to keep current)' : '' }}</label>
            <input
              v-model="form.password"
              type="password"
              :required="!isEditing"
              class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl py-2 px-3 text-slate-900 dark:text-slate-100 text-xs focus:outline-none focus:border-amber-500"
            />
          </div>

          <div>
            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Role</label>
            <select
              v-model="form.role"
              required
              class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl py-2 px-3 text-slate-900 dark:text-slate-100 text-xs focus:outline-none focus:border-amber-500"
            >
              <option value="student" class="bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100">Student</option>
              <option value="teacher" class="bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100">Teacher</option>
              <option value="admin" class="bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100">Admin</option>
            </select>
          </div>

          <div v-if="form.role === 'student'">
            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Assigned Teacher</label>
            <select
              v-model="form.teacher_id"
              class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl py-2 px-3 text-slate-900 dark:text-slate-100 text-xs focus:outline-none focus:border-amber-500"
            >
              <option :value="null" class="bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100">-- Select Teacher --</option>
              <option v-for="t in teachers" :key="t.id" :value="t.id" class="bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100">{{ t.name }}</option>
            </select>
          </div>

          <div v-if="form.role === 'teacher'">
            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Manage Additional Campuses (multi-tenant)</label>
            <div class="space-y-1 max-h-40 overflow-y-auto bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-2">
              <label v-for="t in tenants" :key="t.id" class="flex items-center gap-2 py-1 px-1 rounded-lg cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300">
                <input type="checkbox" :value="t.id" v-model="form.tenant_ids" class="rounded border-slate-300 dark:border-slate-700" />
                {{ t.name }} ({{ t.code }})
              </label>
            </div>
          </div>

          <div class="flex justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
            <button type="button" @click="showModal = false" class="px-4 py-2 text-xs font-semibold text-slate-500 dark:text-slate-400 cursor-pointer">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs rounded-xl shadow-lg cursor-pointer">
              Save Account
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Users, UserPlus, Edit3, Trash2, X, Search } from 'lucide-vue-next';

const props = defineProps({
  users: Object,
  teachers: Array,
  tenants: Array,
});

const selectedRole = ref('all');
const searchQuery = ref('');
const showModal = ref(false);
const isEditing = ref(false);
const editingUserId = ref(null);

const roleTabs = [
  { label: 'All Accounts', value: 'all' },
  { label: 'Students Only', value: 'student' },
  { label: 'Teachers Only', value: 'teacher' },
  { label: 'Admins Only', value: 'admin' },
];

const form = useForm({
  name: '',
  email: '',
  password: '',
  role: 'student',
  teacher_id: null,
  tenant_ids: [],
});

const filteredUsers = computed(() => {
  let list = props.users.data || [];
  if (selectedRole.value !== 'all') {
    list = list.filter((u) => u.role === selectedRole.value);
  }
  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase();
    list = list.filter((u) => u.name.toLowerCase().includes(q) || u.email.toLowerCase().includes(q));
  }
  return list;
});

const openCreateModal = () => {
  isEditing.value = false;
  editingUserId.value = null;
  form.reset();
  showModal.value = true;
};

const openEditModal = (user) => {
  isEditing.value = true;
  editingUserId.value = user.id;
  form.name = user.name;
  form.email = user.email;
  form.password = '';
  form.role = user.role;
  form.teacher_id = user.teacher_id;
  form.tenant_ids = user.managed_tenant_ids || [];
  showModal.value = true;
};

const submitForm = () => {
  if (isEditing.value) {
    form.put(`/admin/users/${editingUserId.value}`, {
      onSuccess: () => (showModal.value = false),
    });
  } else {
    form.post('/admin/users', {
      onSuccess: () => (showModal.value = false),
    });
  }
};

const deleteUser = (user) => {
  if (confirm(`Are you sure you want to delete ${user.name}?`)) {
    router.delete(`/admin/users/${user.id}`);
  }
};
</script>
