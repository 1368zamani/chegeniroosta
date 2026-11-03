<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Village;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

function normalizeName($str) {
    if (!$str) return '';
    $str = str_replace(['ي', 'ك', 'ة', 'آ', 'أ', 'إ'], ['ی', 'ک', 'ه', 'ا', 'ا', 'ا'], $str);
    $str = preg_replace('/[\s\x{200c}\/]+/u', '', $str);
    return trim($str);
}

$data = [
    ['بلوط بان / مراد علي', 'اسداله ملکی شاهیوندی', '09169611830'],
    ['بنارکبوددو', 'علی بیگی نژاد', '09169856993'],
    ['بنارکبودسه', 'جهانبخش طهماسبی', '09187243718'],
    ['بنارکبوديک', 'ایوب خانی کله کله', '09902058244'],
    ['پير صمدين / پير سيد احمد', 'مهران مرادی', '09394445259'],
    ['پيري رضا / پيري سوخته زار', 'امین رضا حسینی', '09169599284'],
    ['چرخستانه', 'حامد شرفی', '09106568252'],
    ['چشمه جوميله', 'سجاد طولابی', '09010131408'],
    ['چشمه زمزم', 'اسکندر میرزاوند', '09160537669'],
    ['چشمه سرده/ دره نورمراد', 'عزیز فتحی مهاجر', '09907461690'],
    ['چم خوشه شير کش', 'مسلم اسدی', '09167122747'],
    ['چم خوشه صفرعلي', 'نبی مرادی حسن آباد', '09169611748'],
    ['چمداود', 'سجاد طهماسبی', '09920545595'],
    ['چنار بالا/چنار کالياب', 'امیرحسین میرزاوند', '09169804998'],
    ['چناران / حسين خان', 'شیرزاد مهدی پناه', '09195352586'],
    ['چهارآب', 'سعید کمالی', null],
    ['خاطره', 'محسن نوری سماق', '09169611916'],
    ['خانه سرخ', 'سعید شاهیوند', '09167505260'],
    ['درمره', 'فردین غلامی چراغ آباد', '09194478830'],
    ['دره رحمانه', 'مجید طولابی فرد', '09166639685'],
    ['دول گاو ميشان/کله بان1', 'همت امیری', '09163636529'],
    ['ريخته کوه ورپل', 'زهرا ملکی نژاد شاهیوندی', '09332218662'],
    ['زنگي بان/شترمل', 'علی مروتی', '09169612283'],
    ['ژريژبان', 'معصومه محمدی حسن آباد', '09168953510'],
    ['سره زمان', 'ابراهیم گندابی', '09168577396'],
    ['سياه چم', 'علی شاهیوند', '09167223301'],
    ['سياه دره 1 فرهادي', 'برزو طولابی', '09163980445'],
    ['سياه دره 3 کوسه', 'چنگیز عینی پور فرد', '09169551644'],
    ['شهبازبک', 'کیومرث میربیک', '09194268051'],
    ['شيخ گل', 'امیر شکری', '09188403053'],
    ['طالقان يک', 'فرامرز دیناری گله دار', '09168616170'],
    ['طالقان2', 'موسی شاهیوند', '09167426396'],
    ['کچکان', 'زهرا طولابی', '09167482057'],
    ['کرگز', 'علی مردان ساعتی', '09169612115'],
    ['کرم / ميان کنان', 'علی عظیمی', '09198028017'],
    ['کله بان / کله بان 2', 'حسین حسینی فرد', '09168662607'],
    ['کله هو', 'یدالله حاجی زاده شاهیوند', '09160810517'],
    ['گاوگيرچمدانه', 'محمد مهدی نوری', '09167122770'],
    ['گرمورت ميرزاخاني', 'مسلم نادری رمضان آباد', '09167204778'],
    ['گرمورت نصرتي', 'مسعود مرادی', '09168634607'],
    ['ملک آباد سماق', 'مجتبی ملکی طولابی', '09169790194'],
    ['ملک ميرزاچم دشتي', 'علی ملکیان شاهیوندی', '09167068431'],
    ['هفت ‌چشمه', 'سید حسین حسینی', '09163637197'],
    ['هودر بساطعلي', 'سعید بساطی', '09169850105'],
    ['ورپل', 'اکبر صفری مطلق', '09165598939'],
    ['ولدکش', 'حمید نوری', '09190965308'],
    ['ولي آباد شيري', 'مهناز کرمیان فعلی', '09169615136'],
];

$aliases = [
    'چهارآب' => ['چهاراب'],
    'هودر بساطعلي' => ['هودر بساط علي'],
    'سره زمان' => ['سرزمان'],
    'پيري رضا / پيري سوخته زار' => ['پيري رضا / پيري سوخته زار', 'پيري رضا'],
    'چشمه جوميله' => ['چشمه جوميله'],
    'چنار بالا/چنار کالياب' => ['چنار بالا/چنار کالياب'],
    'دره رحمانه' => ['دره رحمانه'],
    'دول گاو ميشان/کله بان1' => ['دول گاو ميشان/کله بان1'],
];

$allVillages = Village::all();
$dbIndex = [];
foreach ($allVillages as $v) {
    $dbIndex[normalizeName($v->name)] = $v;
}

$updated = 0;
$created = 0;
$adminsCreated = 0;

foreach ($data as $row) {
    $vName = normalizeName($row[0]);
    $village = $dbIndex[$vName] ?? null;

    if (!$village && isset($aliases[$row[0]])) {
        foreach ($aliases[$row[0]] as $alias) {
            $village = $dbIndex[normalizeName($alias)] ?? null;
            if ($village) break;
        }
    }

    if ($village) {
        $village->dehyar_name = $row[1];
        $village->dehyar_phone = $row[2];
        $village->section = 'بخش شاهیوند';
        $village->section_id = 3;
        $village->saveQuietly();
        echo "✅ آپدیت شد: {$village->name}\n";
        $updated++;
    } else {
        $slug = Str::slug($row[0], '-');
        if (empty($slug)) $slug = 'village-' . time() . rand(10,99);
        
        $originalSlug = $slug;
        $counter = 1;
        while (Village::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $village = Village::withoutEvents(function () use ($row, $slug) {
            return Village::create([
                'name' => $row[0],
                'slug' => $slug,
                'county' => 'چگنی',
                'section' => 'بخش شاهیوند',
                'section_id' => 3,
                'province' => 'لرستان',
                'dehyar_name' => $row[1],
                'dehyar_phone' => $row[2],
                'is_active' => 1,
            ]);
        });
        echo "🆕 روستای جدید ساخته شد: {$village->name} (ID: {$village->id})\n";
        $created++;
    }

    $hasAdmin = DB::table('users')
        ->where('role', 'village_admin')
        ->where('village_id', $village->id)
        ->exists();

    if (!$hasAdmin) {
        $username = 'admin_' . $village->id;
        $email = 'admin_' . $village->id . '@chegeniroosta.ir';
        $password = Hash::make('12345678');

        DB::table('users')->insert([
            'name'        => 'مدیر ' . $village->name,
            'username'    => $username,
            'email'       => $email,
            'password'    => $password,
            'role'        => 'village_admin',
            'section_id'  => 3,
            'village_id'  => $village->id,
            'is_active'   => 1,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
        echo "👤 ادمین ساخته شد برای: {$village->name} | نام کاربری: {$username} | رمز: 12345678\n";
        $adminsCreated++;
    }
}

echo "\n======================\n";
echo "آپدیت شده: $updated\n";
echo "روستای جدید: $created\n";
echo "ادمین جدید: $adminsCreated\n";
echo "تعداد کل روستاهای شاهیوند در دیتابیس: " . Village::where('section_id', 3)->count() . "\n";
echo "تعداد کل ادمین‌های شاهیوند: " . DB::table('users')->where('role', 'village_admin')->where('section_id', 3)->count() . "\n";
echo "Done!\n";
