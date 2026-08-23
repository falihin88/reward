<?php

namespace Database\Seeders;

use App\Models\Card;
use App\Models\PointTransaction;
use App\Models\Setting;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GamificationSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Multiple Tenants
        $tenantAmyn = Tenant::updateOrCreate(['slug' => 'amyn-main'], [
            'name' => 'AMYN Main Campus',
            'code' => 'AMYN-01',
            'domain' => 'main.amynmadrasah.com',
            'accent_color' => '#f59e0b',
            'is_active' => true,
        ]);

        $tenantHikmah = Tenant::updateOrCreate(['slug' => 'al-hikmah'], [
            'name' => 'Al-Hikmah Academy',
            'code' => 'HIKMAH-02',
            'domain' => 'hikmah.amynmadrasah.com',
            'accent_color' => '#10b981',
            'is_active' => true,
        ]);

        $tenantNoor = Tenant::updateOrCreate(['slug' => 'an-noor'], [
            'name' => 'An-Noor Institute',
            'code' => 'NOOR-03',
            'domain' => 'noor.amynmadrasah.com',
            'accent_color' => '#6366f1',
            'is_active' => true,
        ]);

        $tenants = [$tenantAmyn, $tenantHikmah, $tenantNoor];

        // 2. Scholar Cards Definition
        $cardsTemplate = [
            [
                'name' => 'Imam Al-Bukhari',
                'title' => 'Master of Hadith Sciences',
                'era' => 'Golden Age of Islam (810–870 CE)',
                'rarity' => 'legendary',
                'unlock_cost' => 250,
                'bio' => 'Renowned Islamic scholar who compiled Sahih al-Bukhari, traveling thousands of miles across Persia and Arabia.',
                'quote' => 'I never wrote down a single Hadith without first taking a bath and performing two raka’ahs of prayer.',
                'accent_color' => '#f59e0b',
                'order' => 1,
            ],
            [
                'name' => 'Saladin (Salah ad-Din)',
                'title' => 'Hero of Chivalry & Liberation',
                'era' => 'Ayyubid Era (1137–1193 CE)',
                'rarity' => 'legendary',
                'unlock_cost' => 300,
                'bio' => 'Sultan of Egypt and Syria, celebrated worldwide for his honor, chivalry, justice, and merciful liberation of Jerusalem.',
                'quote' => 'I warn you against shedding blood, for blood never sleeps.',
                'accent_color' => '#eab308',
                'order' => 2,
            ],
            [
                'name' => 'Ibn Sina (Avicenna)',
                'title' => 'Father of Early Modern Medicine',
                'era' => 'Persian Golden Age (980–1037 CE)',
                'rarity' => 'epic',
                'unlock_cost' => 200,
                'bio' => 'Polymath physician and philosopher whose medical encyclopedia "The Canon of Medicine" remained standard in European universities for 500 years.',
                'quote' => 'The knowledge of anything is not acquired or complete unless it is known by its causes.',
                'accent_color' => '#a855f7',
                'order' => 3,
            ],
            [
                'name' => 'Fatima al-Fihri',
                'title' => 'Founder of the World’s First University',
                'era' => 'Fez, Morocco (800–880 CE)',
                'rarity' => 'epic',
                'unlock_cost' => 180,
                'bio' => 'Visionary Muslim woman who built the University of al-Qarawiyyin in Fez, Morocco—recognized by UNESCO as the oldest operating university.',
                'quote' => 'True wealth is that which is spent in the path of seeking sacred knowledge.',
                'accent_color' => '#ec4899',
                'order' => 4,
            ],
            [
                'name' => 'Imam Al-Ghazali',
                'title' => 'Proof of Islam (Hujjat al-Islam)',
                'era' => 'Seljuk Empire (1058–1111 CE)',
                'rarity' => 'common',
                'unlock_cost' => 60,
                'bio' => 'Renowned theologian, jurist, and mystic whose masterpiece "The Revival of the Religious Sciences" harmonized law and spiritual purification.',
                'quote' => 'Declare your jihad on twelve enemies you cannot see: Ego, Arrogance, Conceit, Selfishness, Greed, Lust...',
                'accent_color' => '#64748b',
                'order' => 5,
            ],
        ];

        // 3. Seed Data per Tenant
        foreach ($tenants as $t) {
            app()->instance('tenant', $t);

            // Settings for this tenant
            Setting::setValue('cards_enabled', 'true');
            Setting::setValue('points_daily_streak', '15');
            Setting::setValue('points_lesson_completed', '10');

            // Cards for this tenant
            foreach ($cardsTemplate as $c) {
                Card::updateOrCreate([
                    'tenant_id' => $t->id,
                    'name' => $c['name'],
                ], array_merge($c, ['tenant_id' => $t->id]));
            }

            // Admin Account for this tenant
            $adminEmail = $t->slug === 'amyn-main' ? 'admin@amynmadrasah.com' : "admin@{$t->slug}.com";
            $admin = User::updateOrCreate(['email' => $adminEmail], [
                'tenant_id' => $t->id,
                'name' => "Admin ({$t->name})",
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]);

            // Teacher Accounts for this tenant
            $teacherEmail1 = $t->slug === 'amyn-main' ? 'ahmad@amynmadrasah.com' : "teacher1@{$t->slug}.com";
            $teacherEmail2 = $t->slug === 'amyn-main' ? 'teacher.fatima@amynmadrasah.com' : "teacher2@{$t->slug}.com";

            $teacher1 = User::updateOrCreate(['email' => $teacherEmail1], [
                'tenant_id' => $t->id,
                'name' => "Ustaz Ahmad ({$t->code})",
                'password' => Hash::make('password'),
                'role' => 'teacher',
            ]);

            $teacher2 = User::updateOrCreate(['email' => $teacherEmail2], [
                'tenant_id' => $t->id,
                'name' => "Ustazah Fatima ({$t->code})",
                'password' => Hash::make('password'),
                'role' => 'teacher',
            ]);

            // Students for this tenant
            $students = [
                [
                    'name' => 'Tariq ibn Ziyad',
                    'email' => $t->slug === 'amyn-main' ? 'tariq@amynmadrasah.com' : "tariq@{$t->slug}.com",
                    'teacher_id' => $teacher1->id,
                    'points' => 220,
                    'total_points_earned' => 300,
                    'current_streak' => 5,
                ],
                [
                    'name' => 'Fatima al-Fihri',
                    'email' => $t->slug === 'amyn-main' ? 'fatima@amynmadrasah.com' : "fatima@{$t->slug}.com",
                    'teacher_id' => $teacher1->id,
                    'points' => 150,
                    'total_points_earned' => 210,
                    'current_streak' => 3,
                ],
                [
                    'name' => 'Zayd Omar',
                    'email' => $t->slug === 'amyn-main' ? 'zayd@amynmadrasah.com' : "zayd@{$t->slug}.com",
                    'teacher_id' => $teacher2->id,
                    'points' => 90,
                    'total_points_earned' => 120,
                    'current_streak' => 1,
                ],
            ];

            foreach ($students as $st) {
                $user = User::updateOrCreate(['email' => $st['email']], array_merge($st, [
                    'tenant_id' => $t->id,
                    'password' => Hash::make('password'),
                    'role' => 'student',
                    'last_activity_date' => now()->toDateString(),
                ]));

                // Starter unlocked card
                $ghazaliCard = Card::where('tenant_id', $t->id)->where('name', 'Imam Al-Ghazali')->first();
                if ($ghazaliCard && !$user->cards()->where('card_id', $ghazaliCard->id)->exists()) {
                    $user->cards()->attach($ghazaliCard->id, ['unlocked_at' => now()->subDays(2)]);
                }

                // Sample transaction
                PointTransaction::create([
                    'tenant_id' => $t->id,
                    'user_id' => $user->id,
                    'teacher_id' => $user->teacher_id,
                    'points' => 25,
                    'reason' => 'teacher_award',
                    'note' => "Great participation in {$t->name} Quran class!",
                    'created_at' => now()->subDays(1),
                ]);
            }
        }
    }
}
