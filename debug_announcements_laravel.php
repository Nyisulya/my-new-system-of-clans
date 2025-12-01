<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Announcement;

echo "Current Time: " . now()->toDateTimeString() . "\n";
echo "Start of Day: " . now()->startOfDay()->toDateTimeString() . "\n";

$all = Announcement::all();
echo "Total Announcements: " . $all->count() . "\n";

foreach ($all as $a) {
    echo "ID: {$a->id}, Title: {$a->title}\nContent: {$a->content}\nStart: {$a->start_date->format('Y-m-d')}, End: " . ($a->end_date ? $a->end_date->format('Y-m-d') : 'NULL') . "\n---\n";
}

$active = Announcement::active()->get();
echo "Active Announcements: " . $active->count() . "\n";
