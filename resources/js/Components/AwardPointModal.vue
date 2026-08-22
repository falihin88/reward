<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md animate-fade-in">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-slate-100 w-full max-w-md rounded-2xl shadow-2xl p-6 relative overflow-hidden">
      <!-- Header -->
      <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4 mb-4">
        <div class="flex items-center gap-3">
          <div :class="['w-10 h-10 rounded-xl flex items-center justify-center', isDeduction ? 'bg-rose-500/20 text-rose-500' : 'bg-emerald-500/20 text-emerald-500']">
            <MinusCircle v-if="isDeduction" class="w-5 h-5" />
            <PlusCircle v-else class="w-5 h-5" />
          </div>
          <div>
            <h3 class="font-heading font-bold text-lg text-slate-900 dark:text-slate-100">
              {{ isDeduction ? 'Deduct Points' : 'Award Points' }}
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">For student: <strong class="text-amber-600 dark:text-amber-300">{{ student?.name }}</strong></p>
          </div>
        </div>

        <button @click="$emit('close')" type="button" class="p-1 rounded-lg text-slate-400 hover:text-slate-800 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer">
          <X class="w-5 h-5" />
        </button>
      </div>

      <!-- Action Mode Toggle (Award vs Deduct) -->
      <div class="grid grid-cols-2 gap-2 p-1 bg-slate-100 dark:bg-slate-950 rounded-xl mb-4 text-xs font-semibold">
        <button
          type="button"
          @click="isDeduction = false"
          :class="['py-2 rounded-lg transition-all flex items-center justify-center gap-1.5 cursor-pointer', !isDeduction ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white']"
        >
          <Award class="w-4 h-4" />
          Award Points (+)
        </button>
        <button
          type="button"
          @click="isDeduction = true"
          :class="['py-2 rounded-lg transition-all flex items-center justify-center gap-1.5 cursor-pointer', isDeduction ? 'bg-rose-600 text-white shadow-md' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white']"
        >
          <MinusCircle class="w-4 h-4" />
          Deduct Points (-)
        </button>
      </div>

      <!-- Quick Preset Buttons -->
      <div class="mb-4">
        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2">Quick Amount Presets:</label>
        <div class="grid grid-cols-4 gap-2">
          <button
            v-for="amt in presets"
            :key="amt"
            type="button"
            @click="amount = amt"
            :class="[
              'py-2 rounded-xl text-xs font-bold transition-all border cursor-pointer',
              amount === amt
                ? (isDeduction ? 'bg-rose-500/20 text-rose-700 dark:text-rose-300 border-rose-500 scale-105' : 'bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border-emerald-500 scale-105')
                : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:border-slate-400'
            ]"
          >
            {{ isDeduction ? '-' : '+' }}{{ amt }}
          </button>
        </div>
      </div>

      <!-- Form -->
      <form @submit.prevent="submit">
        <!-- Custom Amount -->
        <div class="mb-4">
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Points Amount:</label>
          <div class="relative">
            <input
              v-model.number="amount"
              type="number"
              min="1"
              max="1000"
              required
              class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl py-2.5 px-3 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:border-amber-500"
            />
            <span class="absolute right-3 top-2.5 text-xs text-slate-400 dark:text-slate-500 font-semibold">Points</span>
          </div>
        </div>

        <!-- Teacher Comment / Note -->
        <div class="mb-6">
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
            Teacher Comment / Reason:
          </label>
          <textarea
            v-model="note"
            rows="3"
            placeholder="e.g., Excellent participation in Quran recitation today! OR Late homework submission."
            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-3 text-slate-900 dark:text-slate-100 text-xs focus:outline-none focus:border-amber-500 placeholder-slate-400 dark:placeholder-slate-600 resize-none"
          ></textarea>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-200 dark:border-slate-800">
          <button
            type="button"
            @click="$emit('close')"
            class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition-colors cursor-pointer"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="form.processing"
            :class="[
              'px-5 py-2.5 rounded-xl font-bold text-xs shadow-lg transition-all flex items-center gap-2 cursor-pointer',
              isDeduction
                ? 'bg-rose-600 hover:bg-rose-500 text-white shadow-rose-600/30'
                : 'bg-emerald-600 hover:bg-emerald-500 text-white shadow-emerald-600/30'
            ]"
          >
            <Send class="w-4 h-4" />
            Submit {{ isDeduction ? 'Deduction' : 'Award' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Award, PlusCircle, MinusCircle, X, Send } from 'lucide-vue-next';

const props = defineProps({
  show: Boolean,
  student: Object,
});

const emit = defineEmits(['close']);

const isDeduction = ref(false);
const amount = ref(15);
const note = ref('');
const presets = [5, 10, 25, 50];

const form = useForm({
  student_id: null,
  points: 15,
  note: '',
});

watch(() => props.student, (newVal) => {
  if (newVal) {
    form.student_id = newVal.id;
  }
});

const submit = () => {
  form.student_id = props.student.id;
  form.points = isDeduction.value ? -Math.abs(amount.value) : Math.abs(amount.value);
  form.note = note.value;

  form.post('/teacher/award', {
    onSuccess: () => {
      note.value = '';
      emit('close');
    },
  });
};
</script>
