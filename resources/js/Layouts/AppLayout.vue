<template>
  <div :class="['min-h-screen flex flex-col transition-colors duration-300 selection:bg-amber-500 selection:text-slate-950', isDark ? 'dark bg-slate-950 text-slate-100' : 'bg-slate-50 text-slate-800']">
    <!-- Main Navigation Bar -->
    <header :class="['backdrop-blur-md border-b sticky top-0 z-40 transition-colors', isDark ? 'bg-slate-900/80 border-slate-800/80 text-slate-100' : 'bg-white/85 border-slate-200/90 text-slate-900 shadow-sm']">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <!-- Logo & Brand -->
          <div class="flex items-center gap-6">
            <Link :href="homeUrl" class="flex items-center gap-3 group">
              <div class="w-10 h-10 rounded-xl overflow-hidden shadow-lg group-hover:scale-105 transition-transform border border-amber-500/30 bg-white p-0.5">
                <img src="/logo.png" alt="AMYN Madrasah Logo" class="w-full h-full object-cover rounded-lg" />
              </div>
              <div>
                <h1 :class="['font-heading text-lg font-bold bg-gradient-to-r bg-clip-text text-transparent', isDark ? 'from-amber-200 via-emerald-200 to-indigo-200' : 'from-amber-600 via-emerald-600 to-indigo-700']">
                  AMYN Madrasah
                </h1>
                <p :class="['text-[10px] font-medium tracking-wide uppercase', isDark ? 'text-slate-400' : 'text-slate-500']">Striving for the Deeds</p>
              </div>
            </Link>

            <!-- Desktop Nav Links -->
            <nav class="hidden md:flex items-center gap-1">
              <!-- Student Nav -->
              <template v-if="currentUser?.role === 'student'">
                <Link
                  href="/dashboard"
                  :class="[
                    'px-3 py-2 rounded-lg text-sm font-medium transition-all flex items-center gap-2',
                    $page.component === 'Student/Dashboard'
                      ? (isDark ? 'bg-slate-800 text-amber-400 font-semibold' : 'bg-amber-100 text-amber-900 font-bold')
                      : (isDark ? 'text-slate-300 hover:bg-slate-800/50 hover:text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900')
                  ]"
                >
                  <LayoutDashboard class="w-4 h-4" />
                  Dashboard
                </Link>
                <Link
                  href="/cards"
                  :class="[
                    'px-3 py-2 rounded-lg text-sm font-medium transition-all flex items-center gap-2',
                    $page.component === 'Student/Cards/Index'
                      ? (isDark ? 'bg-slate-800 text-amber-400 font-semibold' : 'bg-amber-100 text-amber-900 font-bold')
                      : (isDark ? 'text-slate-300 hover:bg-slate-800/50 hover:text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900')
                  ]"
                >
                  <BookOpenCheck class="w-4 h-4 text-emerald-500" />
                  Card Album
                </Link>
                <Link
                  href="/profile"
                  :class="[
                    'px-3 py-2 rounded-lg text-sm font-medium transition-all flex items-center gap-2',
                    $page.component === 'Student/Profile'
                      ? (isDark ? 'bg-slate-800 text-amber-400 font-semibold' : 'bg-amber-100 text-amber-900 font-bold')
                      : (isDark ? 'text-slate-300 hover:bg-slate-800/50 hover:text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900')
                  ]"
                >
                  <User class="w-4 h-4 text-indigo-500" />
                  My Profile
                </Link>
              </template>

              <!-- Teacher Nav -->
              <template v-if="currentUser?.role === 'teacher'">
                <Link
                  href="/teacher"
                  :class="[
                    'px-3 py-2 rounded-lg text-sm font-medium transition-all flex items-center gap-2',
                    $page.component === 'Teacher/Dashboard'
                      ? (isDark ? 'bg-slate-800 text-amber-400 font-semibold' : 'bg-amber-100 text-amber-900 font-bold')
                      : (isDark ? 'text-slate-300 hover:bg-slate-800/50 hover:text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900')
                  ]"
                >
                  <Users class="w-4 h-4 text-blue-500" />
                  Student Roster
                </Link>

                <Link
                  href="/teacher/attendance"
                  :class="[
                    'px-3 py-2 rounded-lg text-sm font-medium transition-all flex items-center gap-2',
                    $page.component === 'Teacher/Attendance/Index'
                      ? (isDark ? 'bg-slate-800 text-amber-400 font-semibold' : 'bg-amber-100 text-amber-900 font-bold')
                      : (isDark ? 'text-slate-300 hover:bg-slate-800/50 hover:text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900')
                  ]"
                >
                  <CalendarCheck class="w-4 h-4 text-emerald-500" />
                  Take Attendance (+100 Pts)
                </Link>
              </template>

              <!-- Admin Nav -->
              <template v-if="currentUser?.role === 'admin'">
                <Link
                  href="/admin"
                  :class="[
                    'px-3 py-2 rounded-lg text-sm font-medium transition-all flex items-center gap-2',
                    $page.component === 'Admin/Dashboard'
                      ? (isDark ? 'bg-slate-800 text-amber-400 font-semibold' : 'bg-amber-100 text-amber-900 font-bold')
                      : (isDark ? 'text-slate-300 hover:bg-slate-800/50 hover:text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900')
                  ]"
                >
                  <ShieldCheck class="w-4 h-4 text-purple-500" />
                  Overview
                </Link>
                <Link
                  href="/admin/users"
                  :class="[
                    'px-3 py-2 rounded-lg text-sm font-medium transition-all flex items-center gap-2',
                    $page.component === 'Admin/Users/Index'
                      ? (isDark ? 'bg-slate-800 text-amber-400 font-semibold' : 'bg-amber-100 text-amber-900 font-bold')
                      : (isDark ? 'text-slate-300 hover:bg-slate-800/50 hover:text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900')
                  ]"
                >
                  <Users class="w-4 h-4 text-indigo-500" />
                  Manage Users
                </Link>
                <Link
                  href="/admin/cards"
                  :class="[
                    'px-3 py-2 rounded-lg text-sm font-medium transition-all flex items-center gap-2',
                    $page.component === 'Admin/Cards/Index'
                      ? (isDark ? 'bg-slate-800 text-amber-400 font-semibold' : 'bg-amber-100 text-amber-900 font-bold')
                      : (isDark ? 'text-slate-300 hover:bg-slate-800/50 hover:text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900')
                  ]"
                >
                  <Layers class="w-4 h-4 text-emerald-500" />
                  Manage Cards
                </Link>
              </template>
            </nav>
          </div>

          <!-- User Stats, Theme Toggle & Profile Controls -->
          <div class="flex items-center gap-3 md:gap-4">
            <!-- Student Streak & Points Badge -->
            <div v-if="currentUser?.role === 'student'" class="flex items-center gap-2">
              <!-- Streak counter -->
              <div :class="['flex items-center gap-1.5 border px-3 py-1.5 rounded-full text-xs font-bold shadow-sm', isDark ? 'bg-gradient-to-r from-orange-500/20 to-amber-500/20 border-orange-500/30 text-orange-300' : 'bg-orange-100 border-orange-300 text-orange-800']">
                <Flame class="w-4 h-4 text-orange-500 animate-pulse fill-orange-400/40" />
                <span>{{ currentUser.current_streak }}d Streak</span>
              </div>

              <!-- Points Ticker -->
              <div :class="['flex items-center gap-1.5 border px-3 py-1.5 rounded-full text-xs font-bold shadow-sm', isDark ? 'bg-gradient-to-r from-amber-500/20 to-emerald-500/20 border-amber-500/30 text-amber-300' : 'bg-amber-100 border-amber-300 text-amber-800']">
                <Coins class="w-4 h-4 text-amber-500 animate-bounce" />
                <span>{{ currentUser.points }} Pts</span>
              </div>
            </div>

            <!-- Role Badge -->
            <span
              :class="[
                'hidden sm:inline-block px-2.5 py-1 rounded-full text-xs font-semibold uppercase tracking-wider',
                currentUser?.role === 'admin' ? (isDark ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'bg-purple-100 text-purple-800 border border-purple-200') :
                currentUser?.role === 'teacher' ? (isDark ? 'bg-blue-500/20 text-blue-300 border border-blue-500/30' : 'bg-blue-100 text-blue-800 border border-blue-200') :
                (isDark ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-emerald-100 text-emerald-800 border border-emerald-200')
              ]"
            >
              {{ currentUser?.role }}
            </span>

            <!-- Dark / Light Theme Toggle Button -->
            <button
              @click="toggleTheme"
              type="button"
              :title="isDark ? 'Switch to Light Theme' : 'Switch to Dark Theme'"
              :class="[
                'p-2 rounded-xl transition-all cursor-pointer flex items-center justify-center gap-1.5 text-xs font-semibold shadow-sm',
                isDark ? 'bg-slate-800 hover:bg-slate-700 text-amber-400 border border-slate-700' : 'bg-amber-100 hover:bg-amber-200 text-amber-800 border border-amber-300'
              ]"
            >
              <Sun v-if="isDark" class="w-4 h-4 text-amber-400" />
              <Moon v-else class="w-4 h-4 text-indigo-600" />
              <span class="hidden lg:inline">{{ isDark ? 'Light' : 'Dark' }}</span>
            </button>

            <!-- User Menu & Logout -->
            <div :class="['flex items-center gap-2 border-l pl-3 md:pl-4', isDark ? 'border-slate-800' : 'border-slate-200']">
              <div class="text-right hidden sm:block">
                <div :class="['text-sm font-semibold', isDark ? 'text-slate-200' : 'text-slate-800']">{{ currentUser?.name }}</div>
                <div :class="['text-[11px]', isDark ? 'text-slate-400' : 'text-slate-500']">{{ currentUser?.email }}</div>
              </div>
              <button
                @click="logout"
                type="button"
                title="Logout"
                :class="['p-2 rounded-lg transition-colors cursor-pointer', isDark ? 'text-slate-400 hover:text-rose-400 hover:bg-slate-800' : 'text-slate-500 hover:text-rose-600 hover:bg-slate-100']"
              >
                <LogOut class="w-5 h-5" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Impersonation Active Banner -->
    <div v-if="impersonator" class="bg-gradient-to-r from-purple-700 via-indigo-700 to-blue-700 text-white px-4 py-2.5 shadow-lg border-b border-indigo-500/30 sticky top-16 z-30 animate-fade-in">
      <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-3 text-xs md:text-sm font-medium">
        <div class="flex items-center gap-2.5">
          <span class="text-base animate-bounce">🎭</span>
          <div>
            Teacher <strong class="font-bold underline">{{ impersonator.name }}</strong> is logged in as Student:
            <span class="font-extrabold bg-amber-400 text-slate-950 px-2.5 py-0.5 rounded-full ml-1 shadow-sm">{{ currentUser?.name }}</span>
          </div>
        </div>
        <button
          @click="stopImpersonating"
          type="button"
          class="px-4 py-1.5 rounded-xl bg-white text-indigo-950 hover:bg-amber-300 font-extrabold text-xs shadow-md transition-all flex items-center gap-1.5 cursor-pointer scale-100 hover:scale-105"
        >
          <UserCheck class="w-4 h-4 text-indigo-700" />
          Exit Student Mode & Return to Teacher Account
        </button>
      </div>
    </div>

    <!-- Flash Message Banners -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
      <div
        v-if="$page.props.flash.success"
        class="mb-4 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-700 dark:text-emerald-300 flex items-center justify-between gap-3 shadow-lg animate-fade-in"
      >
        <div class="flex items-center gap-3">
          <CheckCircle2 class="w-5 h-5 text-emerald-500 flex-shrink-0" />
          <span class="text-sm font-medium">{{ $page.props.flash.success }}</span>
        </div>
      </div>

      <div
        v-if="$page.props.flash.error"
        class="mb-4 p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-700 dark:text-rose-300 flex items-center justify-between gap-3 shadow-lg animate-fade-in"
      >
        <div class="flex items-center gap-3">
          <AlertCircle class="w-5 h-5 text-rose-500 flex-shrink-0" />
          <span class="text-sm font-medium">{{ $page.props.flash.error }}</span>
        </div>
      </div>
    </div>

    <!-- Main Content Area -->
    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 w-full">
      <slot />
    </main>

    <!-- Footer -->
    <footer :class="['border-t py-6 text-center text-xs transition-colors', isDark ? 'border-slate-800/80 text-slate-500 bg-slate-950' : 'border-slate-200 text-slate-600 bg-white']">
      <div class="max-w-7xl mx-auto px-4">
        <p>© 2026 AMYN Madrasah • Striving for the Deeds</p>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { usePage, Link, router } from '@inertiajs/vue3';
import {
  CalendarCheck,
  Flame,
  Coins,
  LogOut,
  LayoutDashboard,
  BookOpenCheck,
  User,
  Users,
  ShieldCheck,
  Layers,
  CheckCircle2,
  AlertCircle,
  Sun,
  Moon,
  UserCheck,
} from 'lucide-vue-next';

const page = usePage();
const currentUser = computed(() => page.props.auth.user);
const impersonator = computed(() => page.props.auth.impersonator);

const theme = ref(localStorage.getItem('theme') || 'dark');
const isDark = computed(() => theme.value === 'dark');

const applyTheme = () => {
  if (theme.value === 'dark') {
    document.documentElement.classList.add('dark');
  } else {
    document.documentElement.classList.remove('dark');
  }
};

const toggleTheme = () => {
  theme.value = theme.value === 'dark' ? 'light' : 'dark';
  localStorage.setItem('theme', theme.value);
  applyTheme();
};

onMounted(() => {
  applyTheme();
});

const homeUrl = computed(() => {
  if (currentUser.value?.role === 'admin') return '/admin';
  if (currentUser.value?.role === 'teacher') return '/teacher';
  return '/dashboard';
});

const stopImpersonating = () => {
  router.post('/impersonate/stop');
};

const logout = () => {
  router.post('/logout');
};
</script>
