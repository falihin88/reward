<?php

namespace Database\Seeders;

use App\Models\Card;
use App\Models\PointTransaction;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GamificationSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Settings
        Setting::setValue('cards_enabled', 'true');
        Setting::setValue('points_daily_streak', '15');
        Setting::setValue('points_lesson_completed', '10');

        // 2. Scholar Cards
        $cards = [
            [
                'name' => 'Imam Al-Bukhari',
                'title' => 'Master of Hadith Sciences',
                'era' => 'Golden Age of Islam (810–870 CE)',
                'rarity' => 'legendary',
                'unlock_cost' => 250,
                'bio' => 'Renowned Islamic scholar who compiled Sahih al-Bukhari, the most authentic collection of Hadith in Islamic tradition, traveling thousands of miles across Persia and Arabia.',
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
                'bio' => 'First Sultan of Egypt and Syria, celebrated worldwide for his honor, chivalry, justice, and merciful liberation of Jerusalem during the Crusades.',
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
                'bio' => 'Polymath physician and philosopher whose medical encyclopedia "The Canon of Medicine" remained the standard medical textbook in European universities for over 500 years.',
                'quote' => 'The knowledge of anything, since all things have causes, is not acquired or complete unless it is known by its causes.',
                'accent_color' => '#a855f7',
                'order' => 3,
            ],
            [
                'name' => 'Al-Khwarizmi',
                'title' => 'Father of Algebra & Algorithms',
                'era' => 'Abbasid Caliphate (780–850 CE)',
                'rarity' => 'epic',
                'unlock_cost' => 180,
                'bio' => 'Mathematician and astronomer at the House of Wisdom in Baghdad. His pioneer work introduced Hindu-Arabic numerals and algebra (al-Jabr) to the world.',
                'quote' => 'That fondness for science... has encouraged me to compose a short work on calculating by completion and balancing.',
                'accent_color' => '#8b5cf6',
                'order' => 4,
            ],
            [
                'name' => 'Fatima al-Fihri',
                'title' => 'Founder of the World’s First University',
                'era' => 'Fez, Morocco (800–880 CE)',
                'rarity' => 'epic',
                'unlock_cost' => 180,
                'bio' => 'Visionary Muslim woman who devoted her entire inheritance to build the University of al-Qarawiyyin in Fez, Morocco—recognized by UNESCO as the oldest continuously operating university.',
                'quote' => 'True wealth is that which is spent in the path of seeking sacred knowledge.',
                'accent_color' => '#ec4899',
                'order' => 5,
            ],
            [
                'name' => 'Ibn Battuta',
                'title' => 'Master Explorer of the Medieval World',
                'era' => 'Morocco to China (1304–1369 CE)',
                'rarity' => 'rare',
                'unlock_cost' => 120,
                'bio' => 'Moroccan scholar and explorer who journeyed over 73,000 miles across Africa, the Middle East, India, and China over 30 years, documenting diverse cultures.',
                'quote' => 'Traveling—it leaves you speechless, then turns you into a storyteller.',
                'accent_color' => '#3b82f6',
                'order' => 6,
            ],
            [
                'name' => 'Mariam al-Astrulabi',
                'title' => 'Pioneer of Celestial Navigation',
                'era' => 'Aleppo, Syria (10th Century CE)',
                'rarity' => 'rare',
                'unlock_cost' => 120,
                'bio' => 'Brilliant astronomer and master engineer who designed and manufactured complex astrolabes used for celestial navigation and determining prayer times.',
                'quote' => 'By aligning the stars and geometry, we decipher the harmony of the heavens.',
                'accent_color' => '#06b6d4',
                'order' => 7,
            ],
            [
                'name' => 'Al-Razi (Rhazes)',
                'title' => 'Pioneer of Experimental Chemistry',
                'era' => 'Rayy & Baghdad (865–925 CE)',
                'rarity' => 'rare',
                'unlock_cost' => 100,
                'bio' => 'Pioneering physician and chemist who discovered alcohol and sulfuric acid, and established the first specialized hospital wards in Baghdad.',
                'quote' => 'Truth in medicine is an unattainable goal, and the art as described in books is far beneath the knowledge of an experienced physician.',
                'accent_color' => '#10b981',
                'order' => 8,
            ],
            [
                'name' => 'Jabir ibn Hayyan (Geber)',
                'title' => 'Father of Islamic Chemistry',
                'era' => 'Kufa, Iraq (721–815 CE)',
                'rarity' => 'common',
                'unlock_cost' => 80,
                'bio' => 'Alchemist and polymath who introduced controlled laboratory experimentation, crystallization, distillation, and chemical classification.',
                'quote' => 'The first essential in chemistry is that you should perform practical work and conduct experiments.',
                'accent_color' => '#14b8a6',
                'order' => 9,
            ],
            [
                'name' => 'Imam Al-Ghazali',
                'title' => 'Proof of Islam (Hujjat al-Islam)',
                'era' => 'Seljuk Empire (1058–1111 CE)',
                'rarity' => 'common',
                'unlock_cost' => 60,
                'bio' => 'Renowned theologian, jurist, and mystic whose masterpiece "The Revival of the Religious Sciences" harmonized Islamic law and spiritual purification.',
                'quote' => 'Declare your jihad on twelve enemies you cannot see: Ego, Arrogance, Conceit, Selfishness, Greed, Lust, Intolerance, Anger, Lying, Gossip, Envy, and Hatred.',
                'accent_color' => '#64748b',
                'order' => 10,
            ],
        ];

        foreach ($cards as $c) {
            Card::updateOrCreate(['name' => $c['name']], $c);
        }

        // 3. Admin Account
        $admin = User::updateOrCreate(['email' => 'admin@hikmahway.com'], [
            'name' => 'Admin Director',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // 4. Teachers
        $teacherAhmed = User::updateOrCreate(['email' => 'teacher.ahmed@hikmahway.com'], [
            'name' => 'Teacher Ahmed',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        $teacherFatima = User::updateOrCreate(['email' => 'teacher.fatima@hikmahway.com'], [
            'name' => 'Teacher Fatima',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        // 5. Students
        $studentsData = [
            [
                'name' => 'Zayd Omar',
                'email' => 'zayd@student.com',
                'teacher_id' => $teacherAhmed->id,
                'points' => 220,
                'total_points_earned' => 300,
                'current_streak' => 5,
                'last_activity_date' => now()->toDateString(),
            ],
            [
                'name' => 'Maryam Yusuf',
                'email' => 'maryam@student.com',
                'teacher_id' => $teacherAhmed->id,
                'points' => 150,
                'total_points_earned' => 210,
                'current_streak' => 3,
                'last_activity_date' => now()->toDateString(),
            ],
            [
                'name' => 'Youssef Ali',
                'email' => 'youssef@student.com',
                'teacher_id' => $teacherAhmed->id,
                'points' => 90,
                'total_points_earned' => 120,
                'current_streak' => 1,
                'last_activity_date' => now()->toDateString(),
            ],
            [
                'name' => 'Aisha Hassan',
                'email' => 'aisha@student.com',
                'teacher_id' => $teacherFatima->id,
                'points' => 310,
                'total_points_earned' => 450,
                'current_streak' => 7,
                'last_activity_date' => now()->toDateString(),
            ],
            [
                'name' => 'Tariq Ibrahim',
                'email' => 'tariq@student.com',
                'teacher_id' => $teacherFatima->id,
                'points' => 130,
                'total_points_earned' => 180,
                'current_streak' => 4,
                'last_activity_date' => now()->toDateString(),
            ],
            [
                'name' => 'Bilal Naser',
                'email' => 'bilal@student.com',
                'teacher_id' => $teacherFatima->id,
                'points' => 45,
                'total_points_earned' => 90,
                'current_streak' => 2,
                'last_activity_date' => now()->toDateString(),
            ],
        ];

        foreach ($studentsData as $st) {
            $user = User::updateOrCreate(['email' => $st['email']], array_merge($st, ['password' => Hash::make('password'), 'role' => 'student']));

            // Attach starter unlocked card for top students
            if ($user->points > 100) {
                $ghazaliCard = Card::where('name', 'Imam Al-Ghazali')->first();
                if ($ghazaliCard && !$user->cards()->where('card_id', $ghazaliCard->id)->exists()) {
                    $user->cards()->attach($ghazaliCard->id, ['unlocked_at' => now()->subDays(2)]);
                }
            }

            // Seed sample teacher comments & transactions
            PointTransaction::create([
                'user_id' => $user->id,
                'teacher_id' => $user->teacher_id,
                'points' => 25,
                'reason' => 'teacher_award',
                'note' => 'Outstanding performance and excellent answers during Hadith discussion class!',
                'created_at' => now()->subDays(1),
            ]);
        }
    }
}
