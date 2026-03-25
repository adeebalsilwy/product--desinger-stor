<?php
/**
 * Script to check and prepare the template directory for Yemeni Fashion Images
 */

echo "=== التحقق من مجلد القوالب ===\n";

$templateDir = __DIR__ . '/storage/app/public/template';

if (file_exists($templateDir)) {
    echo "مجلد القوالب موجود: $templateDir\n";
    
    $files = scandir($templateDir);
    $imageFiles = array_filter($files, function($file) {
        $validExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        return in_array($extension, $validExtensions);
    });
    
    echo "عدد ملفات الصور الموجودة: " . count($imageFiles) . "\n";
    
    if (count($imageFiles) > 0) {
        echo "ملفات الصور:\n";
        foreach ($imageFiles as $file) {
            $size = filesize($templateDir . '/' . $file);
            echo "  - $file (" . number_format($size/1024, 2) . " KB)\n";
        }
    } else {
        echo "لم يتم العثور على ملفات صور في المجلد.\n";
    }
} else {
    echo "مجلد القوالب غير موجود: $templateDir\n";
    echo "يرجى إنشاء المجلد ووضع صور الأزياء اليمنية فيه.\n";
    
    // Attempt to create directory
    if (mkdir($templateDir, 0755, true)) {
        echo "تم إنشاء المجلد بنجاح.\n";
        
        // Create sample SVG placeholder
        $sampleSvg = '<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" width="400" height="400" viewBox="0 0 400 400">
  <rect width="400" height="400" fill="#f5e6d3"/>
  <circle cx="200" cy="200" r="80" fill="#d4a5a5" opacity="0.6"/>
  <text x="200" y="210" font-family="Arial" font-size="16" fill="#8B4513" text-anchor="middle">صورة يمنية أصيلة</text>
  <path d="M100,300 Q200,250 300,300" stroke="#8B4513" stroke-width="3" fill="none"/>
</svg>';

        file_put_contents($templateDir . '/yemeni-fashion-sample.svg', $sampleSvg);
        echo "تم إنشاء صورة SVG تجريبية.\n";
    } else {
        echo "فشل في إنشاء المجلد.\n";
    }
}

echo "\n=== تعليمات إضافة الصور ===\n";
echo "1. ضع صور الأزياء اليمنية في المجلد: $templateDir\n";
echo "2. يجب أن تكون الصور بصيغ مدعومة (JPG, PNG, GIF, WEBP)\n";
echo "3. يمكن تسمية الصور بأسماء تعبر عن نوع الزي (مثل: sanaani-dress.jpg، taiz-abaya.png)\n";
echo "4. يمكن وضع صور مصغرة ب.prefixed بالكلمة 'thumb' أو 'thumbnail'\n";
echo "5. عند تشغيل الـ seeders، سيتم استخدام هذه الصور تلقائيًا\n";

echo "\n=== تشغيل الـ Seeders ===\n";
echo "php artisan db:seed\n";
echo "أو\n";
echo "php artisan migrate:fresh --seed\n";