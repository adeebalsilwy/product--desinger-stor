<?php

namespace Database\Seeders;

use App\Models\ProductType;
use App\Models\Product;
use App\Models\PrintArea;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // الفساتين اليمنية التقليدية
        $yemeniDress = ProductType::firstOrCreate(
            ['slug' => 'yemeni-dress'],
            [
                'name' => 'فستان يمني تقليدي',
                'description' => 'فساتين يمنية تقليدية مطرزة يدوياً بتصاميم أصيلة تعكس التراث اليمني الأصيل',
                'base_price' => 299.99,
                'is_active' => true,
                'image_url' => asset('template/yemeni-dress.png'),
            ]
        );
        
        PrintArea::firstOrCreate(
            ['product_type_id' => $yemeniDress->id, 'name' => 'front'],
            [
                'display_name' => 'التصميم الأمامي',
                'width_mm' => 300,
                'height_mm' => 400,
                'offset_x' => 50,
                'offset_y' => 50,
                'is_default' => true,
            ]
        );
        
        PrintArea::firstOrCreate(
            ['product_type_id' => $yemeniDress->id, 'name' => 'back'],
            [
                'display_name' => 'التصميم الخلفي',
                'width_mm' => 300,
                'height_mm' => 400,
                'offset_x' => 50,
                'offset_y' => 50,
                'is_default' => false,
            ]
        );

        // العبايات اليمنية الفاخرة
        $abaya = ProductType::firstOrCreate(
            ['slug' => 'yemeni-abaya'],
            [
                'name' => 'عباية يمنية فاخرة',
                'description' => 'عبايات يمنية فاخرة مطرزة بالذهب والفضة، تصميمات حصرية للنساء اليمنيات',
                'base_price' => 449.99,
                'is_active' => true,
                'image_url' => asset('template/yemeni-abaya.png'),
            ]
        );
        
        PrintArea::firstOrCreate(
            ['product_type_id' => $abaya->id, 'name' => 'external'],
            [
                'display_name' => 'التطريز الخارجي',
                'width_mm' => 350,
                'height_mm' => 450,
                'offset_x' => 25,
                'offset_y' => 25,
                'is_default' => true,
            ]
        );

        // البلوزات اليمنية العصرية
        $yemeniBlouse = ProductType::firstOrCreate(
            ['slug' => 'yemeni-blouse'],
            [
                'name' => 'بلوزة يمنية عصرية',
                'description' => 'بلوزات يمنية عصرية تجمع بين الأصالة والمعاصرة، مناسبة للمناسبات المختلفة',
                'base_price' => 89.99,
                'is_active' => true,
                'image_url' => asset('template/yemeni-blouse.png'),
            ]
        );
        
        PrintArea::firstOrCreate(
            ['product_type_id' => $yemeniBlouse->id, 'name' => 'front'],
            [
                'display_name' => 'الواجهة الأمامية',
                'width_mm' => 250,
                'height_mm' => 300,
                'offset_x' => 50,
                'offset_y' => 50,
                'is_default' => true,
            ]
        );

        // التنانير اليمنية المزخرفة
        $yemeniSkirt = ProductType::firstOrCreate(
            ['slug' => 'yemeni-skirt'],
            [
                'name' => 'تنورة يمنية مزخرفة',
                'description' => 'تنانير يمنية تقليدية مزخرفة بألوان زاهية وتصاميم تراثية جميلة',
                'base_price' => 129.99,
                'is_active' => true,
                'image_url' => asset('template/yemeni-skirt.png'),
            ]
        );
        
        PrintArea::firstOrCreate(
            ['product_type_id' => $yemeniSkirt->id, 'name' => 'waistband'],
            [
                'display_name' => 'منطقة الخصر',
                'width_mm' => 200,
                'height_mm' => 100,
                'offset_x' => 50,
                'offset_y' => 0,
                'is_default' => true,
            ]
        );

        // الحجاب اليمني المطرز
        $hijab = ProductType::firstOrCreate(
            ['slug' => 'yemeni-hijab'],
            [
                'name' => 'حجاب يمني مطرز',
                'description' => 'حجاب يمني فاخر مطرز بتطريز يدوي دقيق، يعكس جمال المرأة اليمنية',
                'base_price' => 59.99,
                'is_active' => true,
                'image_url' => asset('template/yemeni-hijab.png'),
            ]
        );
        
        PrintArea::firstOrCreate(
            ['product_type_id' => $hijab->id, 'name' => 'border'],
            [
                'display_name' => 'تطريز الحواف',
                'width_mm' => 180,
                'height_mm' => 50,
                'offset_x' => 10,
                'offset_y' => 10,
                'is_default' => true,
            ]
        );

        // إنشاء منتجات نموذجية لكل نوع
        $this->createYemeniProducts($yemeniDress);
        $this->createYemeniProducts($abaya);
        $this->createYemeniProducts($yemeniBlouse);
        $this->createYemeniProducts($yemeniSkirt);
        $this->createYemeniProducts($hijab);
    }
    
    protected function createYemeniProducts(ProductType $productType)
    {
        Product::firstOrCreate(
            ['slug' => "classic-{$productType->slug}"],
            [
                'product_type_id' => $productType->id,
                'name' => "{$productType->name} الكلاسيكي",
                'description' => "{$productType->name} كلاسيكي بتصميم تقليدي أصيل وجودة عالية",
                'sku' => strtoupper(str_replace('-', '', substr($productType->slug, 0, 3))) . '-CLS-' . strtoupper(Str::random(4)),
                'price' => $productType->base_price,
                'inventory_count' => 50,
                'is_active' => true,
            ]
        );
        
        Product::firstOrCreate(
            ['slug' => "luxury-{$productType->slug}"],
            [
                'product_type_id' => $productType->id,
                'name' => "{$productType->name} الفاخرة",
                'description' => "{$productType->name} فاخرة بتطريز ذهبي وتصاميم حصرية",
                'sku' => strtoupper(str_replace('-', '', substr($productType->slug, 0, 3))) . '-LUX-' . strtoupper(Str::random(4)),
                'price' => $productType->base_price * 1.8,
                'inventory_count' => 25,
                'is_active' => true,
            ]
        );
    }
}
