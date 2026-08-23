<template>
  <AppLayout>
    <div class="space-y-6">
      <!-- Header Banner -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 rounded-3xl bg-white dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 shadow-xl dark:shadow-2xl">
        <div class="space-y-1">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 border border-amber-500/30 text-xs font-bold uppercase tracking-wider">
            <Building2 class="w-4 h-4 text-amber-600 dark:text-amber-400" />
            Multi-Tenant Platform Hub
          </div>
          <h2 class="font-heading text-2xl font-bold text-slate-900 dark:text-slate-100">Madrasah & School Organizations</h2>
          <p class="text-xs text-slate-600 dark:text-slate-400 max-w-lg">
            Manage multi-tenant campuses, configure custom branding, activate or deactivate branches, and switch active tenant scope.
          </p>
        </div>

        <button
          @click="openCreateModal"
          type="button"
          class="px-5 py-3 rounded-2xl bg-gradient-to-r from-amber-500 to-emerald-500 text-slate-950 font-bold text-xs shadow-lg hover:from-amber-400 hover:to-emerald-400 transition-all flex items-center gap-2 cursor-pointer scale-100 hover:scale-105"
        >
          <PlusCircle class="w-4 h-4" />
          Create New Tenant Organization
        </button>
      </div>

      <!-- Tenants Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="t in tenants"
          :key="t.id"
          :class="[
            'p-6 rounded-3xl border flex flex-col justify-between space-y-4 shadow-xl transition-all relative overflow-hidden',
            $page.props.tenant?.id === t.id
              ? 'bg-gradient-to-b from-amber-500/10 to-emerald-500/10 border-amber-500 dark:border-amber-400 ring-2 ring-amber-500/30'
              : 'bg-white dark:bg-slate-900/90 border-slate-200 dark:border-slate-800'
          ]"
        >
          <!-- Top Tag & Status -->
          <div class="flex items-center justify-between">
            <span class="px-2.5 py-1 rounded-full text-[11px] font-mono font-extrabold uppercase bg-amber-100 dark:bg-amber-500/20 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-500/30">
              {{ t.code }}
            </span>

            <div class="flex items-center gap-2">
              <span v-if="$page.props.tenant?.id === t.id" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-500 text-slate-950 shadow-sm">
                Active Context
              </span>
              <span :class="['text-xs font-semibold', t.is_active ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400']">
                {{ t.is_active ? 'Active' : 'Disabled' }}
              </span>
            </div>
          </div>

          <!-- Body Info -->
          <div class="space-y-2">
            <h3 class="font-heading font-bold text-lg text-slate-900 dark:text-slate-100 flex items-center gap-2">
              <div class="w-4 h-4 rounded-full border border-slate-300 dark:border-slate-700" :style="{ backgroundColor: t.accent_color || '#f59e0b' }"></div>
              {{ t.name }}
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-mono">Slug: {{ t.slug }}</p>
            <p v-if="t.domain" class="text-xs text-slate-500 dark:text-slate-400 font-mono">Domain: {{ t.domain }}</p>
          </div>

          <!-- Stats Counters -->
          <div class="grid grid-cols-3 gap-2 py-3 px-3 bg-slate-50 dark:bg-slate-950 rounded-2xl border border-slate-200 dark:border-slate-800/80 text-center">
            <div>
              <div class="text-xs font-semibold text-slate-400">Users</div>
              <div class="text-base font-extrabold text-indigo-600 dark:text-indigo-400">{{ t.users_count || 0 }}</div>
            </div>
            <div>
              <div class="text-xs font-semibold text-slate-400">Cards</div>
              <div class="text-base font-extrabold text-emerald-600 dark:text-emerald-400">{{ t.cards_count || 0 }}</div>
            </div>
            <div>
              <div class="text-xs font-semibold text-slate-400">Records</div>
              <div class="text-base font-extrabold text-amber-600 dark:text-amber-400">{{ t.attendances_count || 0 }}</div>
            </div>
          </div>

          <!-- Actions Footer -->
          <div class="flex items-center justify-between pt-2">
            <button
              v-if="$page.props.tenant?.id !== t.id"
              @click="switchTenant(t)"
              type="button"
              class="px-3 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs shadow-md transition-all flex items-center gap-1 cursor-pointer"
            >
              <CheckCircle2 class="w-3.5 h-3.5" />
              Switch Context
            </button>
            <span v-else class="text-xs font-bold text-amber-600 dark:text-amber-400 flex items-center gap-1">
              <CheckCircle2 class="w-4 h-4 text-emerald-500" /> Current Scope
            </span>

            <div class="flex items-center gap-1">
              <button @click="openEditModal(t)" class="p-1.5 rounded-lg text-slate-400 hover:text-amber-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer" title="Edit Tenant">
                <Edit3 class="w-4 h-4" />
              </button>
              <button @click="deleteTenant(t)" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer" title="Delete Tenant">
                <Trash2 class="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Create / Edit Modal -->
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-slate-100 w-full max-w-md rounded-2xl shadow-2xl p-6 relative">
          <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4 mb-4">
            <h3 class="font-heading font-bold text-lg text-slate-900 dark:text-slate-100">
              {{ isEditing ? 'Edit Tenant Organization' : 'Create New Tenant Organization' }}
            </h3>
            <button @click="showModal = false" type="button" class="p-1 text-slate-400 hover:text-slate-800 dark:hover:text-white cursor-pointer">
              <X class="w-5 h-5" />
            </button>
          </div>

          <form @submit.prevent="submitForm" class="space-y-4 text-xs">
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Organization Name</label>
              <input
                v-model="form.name"
                type="text"
                required
                placeholder="e.g. Al-Hikmah Academy"
                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl py-2.5 px-3 text-slate-900 dark:text-slate-100 text-xs focus:outline-none focus:border-amber-500"
              />
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Organization Code</label>
                <input
                  v-model="form.code"
                  type="text"
                  required
                  placeholder="e.g. HIKMAH-02"
                  class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl py-2.5 px-3 text-slate-900 dark:text-slate-100 text-xs focus:outline-none focus:border-amber-500"
                />
              </div>

              <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">URL Slug</label>
                <input
                  v-model="form.slug"
                  type="text"
                  placeholder="e.g. al-hikmah"
                  class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl py-2.5 px-3 text-slate-900 dark:text-slate-100 text-xs focus:outline-none focus:border-amber-500"
                />
              </div>
            </div>

            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Domain (Optional)</label>
              <input
                v-model="form.domain"
                type="text"
                placeholder="e.g. hikmah.amynmadrasah.com"
                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl py-2.5 px-3 text-slate-900 dark:text-slate-100 text-xs focus:outline-none focus:border-amber-500"
              />
            </div>

            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Accent Brand Color</label>
              <input
                v-model="form.accent_color"
                type="text"
                placeholder="#f59e0b"
                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl py-2.5 px-3 text-slate-900 dark:text-slate-100 text-xs focus:outline-none focus:border-amber-500"
              />
            </div>

            <div class="flex items-center gap-2">
              <input v-model="form.is_active" type="checkbox" id="is_active_tenant" class="rounded bg-slate-100 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-amber-500 cursor-pointer" />
              <label for="is_active_tenant" class="font-semibold text-slate-700 dark:text-slate-300 cursor-pointer">Active (Allows login & operations)</label>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
              <button type="button" @click="showModal = false" class="px-4 py-2 font-semibold text-slate-500 dark:text-slate-400 cursor-pointer">Cancel</button>
              <button type="submit" :disabled="form.processing" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold rounded-xl shadow-lg cursor-pointer">
                Save Tenant
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Building2, PlusCircle, CheckCircle2, Edit3, Trash2, X } from 'lucide-vue-next';

const props = defineProps({
  tenants: Array,
});

const showModal = ref(false);
const isEditing = ref(false);
const editingTenantId = ref(null);

const form = useForm({
  name: '',
  slug: '',
  code: '',
  domain: '',
  accent_color: '#f59e0b',
  is_active: true,
});

const openCreateModal = () => {
  isEditing.value = false;
  editingTenantId.value = null;
  form.reset();
  showModal.value = true;
};

const openEditModal = (t) => {
  isEditing.value = true;
  editingTenantId.value = t.id;
  form.name = t.name;
  form.slug = t.slug;
  form.code = t.code;
  form.domain = t.domain || '';
  form.accent_color = t.accent_color || '#f59e0b';
  form.is_active = t.is_active;
  showModal.value = true;
};

const submitForm = () => {
  if (isEditing.value) {
    form.put(`/admin/tenants/${editingTenantId.value}`, {
      onSuccess: () => (showModal.value = false),
    });
  } else {
    form.post('/admin/tenants', {
      onSuccess: () => (showModal.value = false),
    });
  }
};

const switchTenant = (t) => {
  router.post(`/tenants/${t.id}/switch`);
};

const deleteTenant = (t) => {
  if (confirm(`Are you sure you want to delete tenant '${t.name}'?`)) {
    router.delete(`/admin/tenants/${t.id}`);
  }
};
</script>
