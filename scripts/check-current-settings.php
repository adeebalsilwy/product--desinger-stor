<?php
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Current Settings:\n";
echo "==================\n";

$settings = DB::table('settings')->get();
foreach ($settings as $setting) {
    echo $setting->key . ": " . $setting->value . "\n";
}

echo "\nBrand Colors:\n";
echo "=============\n";
$brandSettings = DB::table('settings')
    ->whereIn('key', ['brand_primary_color', 'brand_secondary_color', 'brand_accent_color', 'brand_bg_color', 'brand_text_color', 'brand_tagline'])
    ->get();

foreach ($brandSettings as $setting) {
    echo $setting->key . ": " . $setting->value . "\n";
}