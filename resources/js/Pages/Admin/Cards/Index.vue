<template>
  <AppLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 rounded-3xl bg-white dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 shadow-xl dark:shadow-2xl">
        <div class="space-y-1">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30 text-xs font-bold uppercase tracking-wider">
            <Layers class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
            Scholar Deck Builder
          </div>
          <h2 class="font-heading text-2xl font-bold text-slate-900 dark:text-slate-100">Scholar Cards Deck & Artwork Upload</h2>
          <p class="text-xs text-slate-600 dark:text-slate-400 max-w-lg">
            Create, upload custom scholar portrait artwork, edit cards, and configure unlock costs for student card albums.
          </p>
        </div>

        <button
          @click="openCreateModal"
          class="px-5 py-3 rounded-2xl bg-gradient-to-r from-amber-500 to-emerald-500 text-slate-950 font-bold text-xs shadow-lg hover:from-amber-400 hover:to-emerald-400 transition-all flex items-center gap-2 cursor-pointer scale-100 hover:scale-105"
        >
          <PlusCircle class="w-4 h-4" />
          Add New Scholar Card
        </button>
      </div>

      <!-- Cards Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <div
          v-for="card in cards"
          :key="card.id"
          class="p-5 rounded-2xl bg-white dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 flex flex-col justify-between space-y-4 shadow-xl relative overflow-hidden text-slate-900 dark:text-slate-100"
        >
          <!-- Top Info -->
          <div class="flex items-center justify-between">
            <span
              :class="[
                'px-2.5 py-0.5 rounded text-[10px] font-bold uppercase',
                card.rarity === 'legendary' ? 'bg-amber-500 text-slate-950' :
                card.rarity === 'epic' ? 'bg-purple-600 text-white' :
                card.rarity === 'rare' ? 'bg-blue-600 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-300'
              ]"
            >
              {{ card.rarity }}
            </span>
            <div class="flex items-center gap-1 text-amber-600 dark:text-amber-400 text-xs font-bold font-mono">
              <Coins class="w-3.5 h-3.5" />
              {{ card.unlock_cost }} Pts
            </div>
          </div>

          <!-- Card Content -->
          <div class="text-center py-2">
            <div class="w-20 h-20 mx-auto rounded-full border-2 p-1 mb-2 bg-slate-100 dark:bg-slate-950 flex items-center justify-center shadow-md overflow-hidden" :style="{ borderColor: card.accent_color || '#10b981' }">
              <img v-if="card.image_url" :src="card.image_url" :alt="card.name" class="w-full h-full object-cover rounded-full" />
              <BookOpen v-else class="w-8 h-8 text-amber-500" />
            </div>
            <h4 class="font-heading font-bold text-slate-900 dark:text-slate-100 text-base line-clamp-1">{{ card.name }}</h4>
            <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-1 mt-0.5">{{ card.title }}</p>
            <p class="text-[11px] text-slate-400 dark:text-slate-500 font-mono mt-1">{{ card.era }}</p>
          </div>

          <!-- Actions -->
          <div class="flex items-center justify-between pt-3 border-t border-slate-200 dark:border-slate-800">
            <span :class="['text-[10px] font-mono font-bold', card.is_active ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500']">
              {{ card.is_active ? 'Active' : 'Disabled' }}
            </span>

            <div class="flex items-center gap-1">
              <button @click="openEditModal(card)" class="p-1.5 rounded-lg text-slate-400 hover:text-amber-500 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer">
                <Edit3 class="w-4 h-4" />
              </button>
              <button @click="deleteCard(card)" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-500 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer">
                <Trash2 class="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-slate-100 w-full max-w-lg rounded-3xl shadow-2xl p-6 relative max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4 mb-4">
          <h3 class="font-heading font-bold text-lg text-slate-900 dark:text-slate-100">
            {{ isEditing ? 'Edit Scholar Card' : 'Create New Scholar Card' }}
          </h3>
          <button @click="showModal = false" class="p-1 text-slate-400 hover:text-slate-800 dark:hover:text-white cursor-pointer">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="submitForm" class="space-y-4 text-xs">
          <!-- Image Upload & Preview -->
          <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 flex items-center gap-4">
            <div class="w-16 h-16 rounded-xl bg-slate-200 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 flex items-center justify-center overflow-hidden flex-shrink-0">
              <img v-if="imagePreview || form.image_url" :src="imagePreview || form.image_url" class="w-full h-full object-cover" />
              <Upload v-else class="w-6 h-6 text-slate-400" />
            </div>

            <div class="flex-1 space-y-1">
              <label class="block font-semibold text-slate-700 dark:text-slate-300">Upload Scholar Artwork / Photo</label>
              <input
                type="file"
                @change="onFileSelected"
                accept="image/*"
                class="block w-full text-xs text-slate-500 dark:text-slate-400 file:mr-2 file:py-1 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-amber-500 file:text-slate-950 hover:file:bg-amber-400 cursor-pointer"
              />
              <p class="text-[10px] text-slate-400">Supports JPG, PNG, WEBP (Max 5MB)</p>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Scholar Name</label>
              <input v-model="form.name" type="text" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-2.5 text-slate-900 dark:text-slate-100 focus:border-amber-500" />
            </div>
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Honorific Title</label>
              <input v-model="form.title" type="text" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-2.5 text-slate-900 dark:text-slate-100 focus:border-amber-500" />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Era / Period</label>
              <input v-model="form.era" type="text" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-2.5 text-slate-900 dark:text-slate-100 focus:border-amber-500" />
            </div>
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Unlock Cost (Points)</label>
              <input v-model.number="form.unlock_cost" type="number" min="1" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-2.5 text-slate-900 dark:text-slate-100 focus:border-amber-500" />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Rarity Tier</label>
              <select v-model="form.rarity" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-2.5 text-slate-900 dark:text-slate-100 focus:border-amber-500">
                <option value="common">Common</option>
                <option value="rare">Rare</option>
                <option value="epic">Epic</option>
                <option value="legendary">Legendary</option>
              </select>
            </div>
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Accent Hex Color</label>
              <input v-model="form.accent_color" type="text" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-2.5 text-slate-900 dark:text-slate-100 focus:border-amber-500" />
            </div>
          </div>

          <div>
            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Biography</label>
            <textarea v-model="form.bio" rows="3" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-2.5 text-slate-900 dark:text-slate-100 focus:border-amber-500 resize-none"></textarea>
          </div>

          <div>
            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Famous Quote</label>
            <textarea v-model="form.quote" rows="2" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-2.5 text-slate-900 dark:text-slate-100 focus:border-amber-500 resize-none"></textarea>
          </div>

          <div class="flex items-center gap-2">
            <input v-model="form.is_active" type="checkbox" id="is_active" class="rounded bg-slate-100 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-amber-500 focus:ring-0 cursor-pointer" />
            <label for="is_active" class="font-semibold text-slate-700 dark:text-slate-300 cursor-pointer">Active (Visible in student card album)</label>
          </div>

          <div class="flex justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
            <button type="button" @click="showModal = false" class="px-4 py-2 font-semibold text-slate-500 dark:text-slate-400 cursor-pointer">Cancel</button>
            <button type="submit" :disabled="form.processing" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold rounded-xl shadow-lg cursor-pointer">
              Save Card
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Layers, PlusCircle, Coins, BookOpen, Edit3, Trash2, X, Upload } from 'lucide-vue-next';

const props = defineProps({
  cards: Array,
});

const showModal = ref(false);
const isEditing = ref(false);
const editingCardId = ref(null);
const imagePreview = ref(null);

const form = useForm({
  name: '',
  title: '',
  era: '',
  rarity: 'common',
  unlock_cost: 100,
  bio: '',
  quote: '',
  accent_color: '#10b981',
  image_url: '',
  image_file: null,
  is_active: true,
  order: 0,
});

const onFileSelected = (e) => {
  const file = e.target.files[0];
  if (file) {
    form.image_file = file;
    imagePreview.value = URL.createObjectURL(file);
  }
};

const openCreateModal = () => {
  isEditing.value = false;
  editingCardId.value = null;
  imagePreview.value = null;
  form.reset();
  showModal.value = true;
};

const openEditModal = (card) => {
  isEditing.value = true;
  editingCardId.value = card.id;
  imagePreview.value = null;
  form.name = card.name;
  form.title = card.title;
  form.era = card.era;
  form.rarity = card.rarity;
  form.unlock_cost = card.unlock_cost;
  form.bio = card.bio;
  form.quote = card.quote;
  form.accent_color = card.accent_color;
  form.image_url = card.image_url;
  form.image_file = null;
  form.is_active = card.is_active;
  form.order = card.order;
  showModal.value = true;
};

const submitForm = () => {
  if (isEditing.value) {
    form.post(route('admin.cards.update', editingCardId.value), {
      headers: {
        'X-HTTP-Method-Override': 'PUT',
      },
      onSuccess: () => (showModal.value = false),
    });
  } else {
    form.post(route('admin.cards.store'), {
      onSuccess: () => (showModal.value = false),
    });
  }
};

const deleteCard = (card) => {
  if (confirm(`Are you sure you want to delete ${card.name}?`)) {
    router.delete(route('admin.cards.destroy', card.id));
  }
};
</script>
