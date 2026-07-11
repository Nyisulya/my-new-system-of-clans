<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use Carbon\Carbon;

class SimuliziSeeder extends Seeder
{
    public function run(): void
    {
        $adminUserId = \App\Models\User::first()?->id ?? 1;

        // Find admin user specifically
        $admin = \App\Models\User::where('role', 'admin')
            ->orWhere('email', 'LIKE', '%nyisulya%')
            ->first();

        if ($admin) {
            $adminUserId = $admin->id;
        }

        $posts = [
            [
                'user_id' => $adminUserId,
                'content' => "🏡 Karibu kwenye Mfumo wa Ukoo wetu!\n\nHuu ni mfumo wa kutunza historia, simulizi, na picha za ukoo wetu. Kila mmoja wenu anaweza:\n\n✍️ Kuandika simulizi — hadithi, kumbukumbu, au habari yoyote ya familia\n📸 Kupakia picha za zamani na za sasa\n💬 Kutoa maoni na kupenda machapisho ya wengine\n\nTunaomba kila mtu ashiriki! Hata hadithi ndogo unayoikumbuka kutoka kwa bibi au babu yako ni thamani kubwa kwa vizazi vijavyo.\n\nKaribu sana! 🙏",
                'created_at' => Carbon::now()->subDays(4),
            ],
            [
                'user_id' => $adminUserId,
                'content' => "📜 Historia ya Ukoo Wetu\n\nMababu zetu walikuwa watu wa bidii na upendo mkubwa. Kila familia ina hadithi yake — na sasa tuna nafasi ya kuzihifadhi zote pamoja.\n\nJe, unajua hadithi ya jinsi babu yako au bibi yako walivyokutana? Walikuwa wanafanya kazi gani? Walizaliwa wapi?\n\nTunaomba kila mtu aandike angalau hadithi moja ya familia yake hapa. Hii itakuwa hazina kwa watoto wetu na wajukuu wetu! 📖\n\n#HistoriaYaUkoo #KumbukumbuZaFamilia",
                'created_at' => Carbon::now()->subDays(3),
            ],
            [
                'user_id' => $adminUserId,
                'content' => "❓ Swali la Wiki\n\nLeo tunataka kujua kutoka kwa kila mmoja wenu:\n\n👉 Kumbukumbu yako ya kwanza kabisa ya bibi au babu yako ni ipi?\n\nLabda ni hadithi aliyokuambia, chakula alichokupikia, au mchezo mlioucheza pamoja. Tuambie kwenye maoni! 👇\n\nKumbukumbu hizi ndogo ndogo ndizo zinazojenga historia yetu kubwa. 💛",
                'created_at' => Carbon::now()->subDays(2),
            ],
            [
                'user_id' => $adminUserId,
                'content' => "📸 Changamoto ya Picha!\n\nTunaomba kila mtu atafute picha moja ya zamani ya familia yake — inaweza kuwa picha ya arusi, picha ya shule, au picha yoyote ya kizamani.\n\nPakia kwenye sehemu ya 'Picha za Ukoo' au hapa kwenye Simulizi! 📷\n\nPicha za zamani ni hazina — zinatusaidia kuona jinsi tulivyotoka mbali na jinsi tunavyofanana na mababu zetu. Tushirikiane! 🤝",
                'created_at' => Carbon::now()->subDays(1),
            ],
            [
                'user_id' => $adminUserId,
                'content' => "🙏 Tukumbuke Waliotutangulia\n\nMfumo huu si tu kwa watu walio hai — pia ni kwa kutunza kumbukumbu za wapendwa wetu waliofariki.\n\nKama una kumbukumbu nzuri ya mtu yeyote wa familia aliyekwishaondoka, tafadhali ishiriki hapa. Inaweza kuwa:\n\n• Sifa yake nzuri aliyokuwa nayo\n• Ushauri aliokuachia\n• Hadithi ya kuchekesha au ya kugusa moyo\n\nKwa kufanya hivyo, tunawaheshimu na tunawahifadhi milele kwenye mioyo yetu. ❤️\n\n#TukumbukeMababu #UkooBilaKupotea",
                'created_at' => Carbon::now()->subHours(6),
            ],
        ];

        foreach ($posts as $postData) {
            Post::create($postData);
        }

        $this->command->info('✅ Machapisho 5 ya Simulizi yamewekwa!');
    }
}
