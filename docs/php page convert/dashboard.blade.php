<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
<link rel="stylesheet" href="{{ asset('css/all.min.css') }}">
    <style>
        :root {
            --primary: #9c27b0;
            --secondary: #673ab7;
            --light: #f0f0f0;
            --dark: #4a148c;
            --text: #333;
            --bg-color: #e0e5ec;
            --shadow-light: #ffffff;
            --shadow-dark: #a3b1c6;
            --tools-width: 80px;
            --sidebar-width: 80px;
            --wardrobe-width: 350px;
            --wardrobe-height: 500px;
            --wood-color: #8B4513;
            --wood-light: #A0522D;
            --wood-dark: #5D4037;
            --metal-color: #B0B0B0;
            --clothes-hanger: #C0C0C0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text);
            height: 100vh;
            overflow: hidden;
        }

        /* نمط Neumorphism الأساسي */
        .neumorphic {
            border-radius: 15px;
            background: var(--bg-color);
            box-shadow:  9px 9px 16px var(--shadow-dark),
                        -9px -9px 16px var(--shadow-light);
        }

        .neumorphic-inset {
            border-radius: 15px;
            background: var(--bg-color);
            box-shadow: inset 3px 3px 5px var(--shadow-dark),
                        inset -3px -3px 5px var(--shadow-light);
        }

        .neumorphic-btn {
            border: none;
            border-radius: 10px;
            background: var(--bg-color);
            box-shadow:  5px 5px 10px var(--shadow-dark),
                        -5px -5px 10px var(--shadow-light);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .neumorphic-btn:hover {
            box-shadow:  2px 2px 5px var(--shadow-dark),
                        -2px -2px 5px var(--shadow-light);
        }

        .neumorphic-btn:active {
            box-shadow: inset 2px 2px 5px var(--shadow-dark),
                        inset -2px -2px 5px var(--shadow-light);
        }

        .app-container {
            display: grid;
            grid-template-columns: var(--sidebar-width) 1fr var(--tools-width);
            height: 100vh;
            position: relative;
        }

        /* الشريط الجانبي */
        .sidebar {
            background: var(--bg-color);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 20px;
        }

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 25px;
            width: 100%;
            text-align: center;
        }

        .nav-icon {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: var(--primary);
            margin-bottom: 5px;
            cursor: pointer;
            border-radius: 15px;
            background: var(--bg-color);
            box-shadow:  5px 5px 10px var(--shadow-dark),
                       -5px -5px 10px var(--shadow-light);
            transition: all 0.3s;
        }

        .nav-icon:hover {
            box-shadow:  2px 2px 5px var(--shadow-dark),
                       -2px -2px 5px var(--shadow-light);
        }

        .nav-icon.active {
            color: white;
            background: var(--primary);
            box-shadow: inset 2px 2px 5px rgba(0,0,0,0.2);
        }

        .nav-label {
            font-size: 12px;
            color: var(--text);
            margin-top: 5px;
        }

        /* شريط الأدوات */
        .tools-panel {
            background: var(--bg-color);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 20px;
        }

        .tool-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 25px;
            width: 100%;
            text-align: center;
        }

        .tool-icon {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: var(--primary);
            margin-bottom: 5px;
            cursor: pointer;
            border-radius: 15px;
            background: var(--bg-color);
            box-shadow:  5px 5px 10px var(--shadow-dark),
                       -5px -5px 10px var(--shadow-light);
            transition: all 0.3s;
        }

        .tool-icon:hover {
            box-shadow:  2px 2px 5px var(--shadow-dark),
                       -2px -2px 5px var(--shadow-light);
        }

        .tool-icon.active {
            color: white;
            background: var(--primary);
            box-shadow: inset 2px 2px 5px rgba(0,0,0,0.2);
        }

        .tool-label {
            font-size: 12px;
            color: var(--text);
            margin-top: 5px;
        }

        /* لوحات منبثقة */
        .palette {
            position: fixed;
            background: var(--bg-color);
            border-radius: 20px;
            box-shadow:  8px 8px 15px var(--shadow-dark),
                       -8px -8px 15px var(--shadow-light);
            padding: 20px;
            z-index: 100;
            display: none;
        }

        .color-palette {
            right: 90px; /* تغيير من left إلى right */
            top: 50%;
            transform: translateY(-50%);
            width: 220px;
        }

        .size-palette {
            right: 90px; /* تغيير من left إلى right */
            top: 50%;
            transform: translateY(-50%);
            width: 160px;
        }

        .control-palette {
            left: 50%;
            bottom: 30px;
            transform: translateX(-50%);
            display: flex;
            gap: 15px;
            padding: 15px 25px;
        }

        .sizes-palette {
            right: 90px; /* تغيير من left إلى right */
            top: 30%;
            transform: translateY(-50%);
            width: 220px;
        }

        .dress-colors-palette {
            right: 90px; /* تغيير من left إلى right */
            top: 60%;
            transform: translateY(-50%);
            width: 220px;
        }

        .color-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 12px;
            margin-top: 15px;
        }

        .color-option {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid transparent;
            box-shadow:  3px 3px 6px var(--shadow-dark),
                       -3px -3px 6px var(--shadow-light);
            transition: all 0.2s;
        }

        .color-option:hover {
            transform: scale(1.1);
        }

        .color-option.selected {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--bg-color), 0 0 0 5px var(--primary);
        }

        .size-option {
            padding: 10px;
            margin: 8px 0;
            border-radius: 10px;
            cursor: pointer;
            text-align: center;
            background: var(--bg-color);
            box-shadow: inset 3px 3px 5px var(--shadow-dark),
                       inset -3px -3px 5px var(--shadow-light);
            transition: all 0.3s;
        }

        .size-option:hover {
            box-shadow: inset 1px 1px 3px var(--shadow-dark),
                       inset -1px -1px 3px var(--shadow-light);
        }

        .size-option.selected {
            background: var(--primary);
            color: white;
            box-shadow: none;
        }

        .control-btn {
            padding: 12px 18px;
            border-radius: 12px;
            font-weight: bold;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 50px;
            justify-content: center;
            transition: all 0.3s;
        }

        .delete-btn {
            background: #f44336;
            color: white;
            box-shadow:  5px 5px 10px var(--shadow-dark);
        }

        .save-btn {
            background: var(--primary);
            color: white;
            box-shadow:  5px 5px 10px var(--shadow-dark);
        }

        .reset-btn {
            background: var(--bg-color);
            color: var(--text);
            box-shadow:  5px 5px 10px var(--shadow-dark),
                       -5px -5px 10px var(--shadow-light);
        }

        .control-btn:hover {
            transform: translateY(-2px);
        }

        .control-btn:active {
            transform: translateY(1px);
            box-shadow: inset 2px 2px 5px rgba(0,0,0,0.2);
        }

        /* منطقة التصميم */
        .design-area {
            margin: 20px;
            border-radius: 20px;
            position: relative;
            overflow: hidden;
            box-shadow: inset 5px 5px 10px var(--shadow-dark),
                       inset -5px -5px 10px var(--shadow-light);
        }

        canvas {
            width: 100%;
            height: 100%;
            display: block;
            background-color: white;
        }

        /* صورة المستخدم في لوحة التحكم */
        .user-panel {
            position: absolute;
            top: 30px;
            left: 30px;
            z-index: 50;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-color);
            box-shadow:  5px 5px 10px var(--shadow-dark),
                       -5px -5px 10px var(--shadow-light);
            color: var(--primary);
            font-size: 20px;
            cursor: pointer;
        }

        .palette-title {
            text-align: center;
            margin-bottom: 15px;
            font-weight: bold;
            color: var(--primary);
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* تصميم الدولاب العصري */
        .wardrobe-container-3d {
            position: fixed;
            bottom: 50px;
            right: 100px;
            width: var(--wardrobe-width);
            height: var(--wardrobe-height);
            perspective: 1500px;
            z-index: 90;
        }

        .wardrobe-3d {
            width: 100%;
            height: 100%;
            position: relative;
            transform-style: preserve-3d;
            transition: transform 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .wardrobe-body-3d {
            position: absolute;
            width: 100%;
            height: 100%;
            background: linear-gradient(to right, var(--wood-light), var(--wood-color));
            border-radius: 8px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            transform: translateZ(0);
            overflow: hidden;
        }

        .wardrobe-door-3d {
            position: absolute;
            width: 49%;
            height: 100%;
            background: linear-gradient(to right, var(--wood-dark), var(--wood-color));
            border: 1px solid var(--wood-dark);
            box-sizing: border-box;
            transition: transform 1s ease-in-out;
            box-shadow: 5px 0 15px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .door-left-3d {
            left: 0;
            border-radius: 8px 0 0 8px;
            transform-origin: left center;
            border-right: 1px solid #5D4037;
        }

        .door-right-3d {
            right: 0;
            border-radius: 0 8px 8px 0;
            transform-origin: right center;
            border-left: 1px solid #5D4037;
        }

        .door-handle-3d {
            width: 20px;
            height: 60px;
            background: linear-gradient(to bottom, #D3D3D3, #A9A9A9);
            border-radius: 10px;
            box-shadow: inset 0 0 10px rgba(0,0,0,0.2);
            position: absolute;
        }

        .door-left-3d .door-handle-3d {
            right: 15px;
        }

        .door-right-3d .door-handle-3d {
            left: 15px;
        }

        .wardrobe-inside-3d {
            position: absolute;
            width: calc(100% - 60px);
            height: calc(100% - 60px);
            top: 30px;
            left: 30px;
            background: #F8F8F8;
            border-radius: 4px;
            display: none;
            flex-wrap: wrap;
            align-content: flex-start;
            padding: 20px;
            gap: 15px;
            overflow-y: auto;
            box-shadow: inset 0 0 20px rgba(0,0,0,0.1);
        }

        .clothes-rail-3d {
            width: 100%;
            height: 30px;
            background: var(--metal-color);
            border-radius: 5px;
            position: relative;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .hanger-3d {
            position: absolute;
            width: 25px;
            height: 40px;
            top: 30px;
            background: var(--clothes-hanger);
            border-radius: 0 0 10px 10px;
            transform-origin: top center;
            transition: transform 0.3s;
        }

        .hanger-3d:before {
            content: "";
            position: absolute;
            width: 15px;
            height: 15px;
            background: var(--clothes-hanger);
            border-radius: 50%;
            top: -7px;
            left: 5px;
        }

        .clothing-item-3d {
            width: 85px;
            position: relative;
            transition: all 0.3s ease;
            cursor: pointer;
            margin-bottom: 20px;
            transform-style: preserve-3d;
        }

        .clothing-img-container-3d {
            width: 100%;
            height: 110px;
            position: relative;
            transform-style: preserve-3d;
            transition: inherit;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-radius: 3px;
            overflow: hidden;
        }

        .clothing-img-3d {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .clothing-name-3d {
            font-size: 12px;
            text-align: center;
            margin-top: 8px;
            color: #333;
            font-weight: 600;
            text-shadow: 0 1px 1px rgba(0,0,0,0.1);
        }

        .wardrobe-3d.open {
            transform: translateY(-20px);
        }

        .wardrobe-3d.open .door-left-3d {
            transform: rotateY(-140deg);
        }

        .wardrobe-3d.open .door-right-3d {
            transform: rotateY(140deg);
        }

        .wardrobe-3d.open .wardrobe-inside-3d {
            display: flex;
        }

        .clothing-item-3d:hover {
            transform: translateY(-5px) rotateX(5deg);
        }

        .clothing-item-3d:hover .clothing-img-3d {
            transform: scale(1.05);
        }

        .wardrobe-control-3d {
            position: absolute;
            bottom: -25px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(to bottom, #8B4513, #A0522D);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            cursor: pointer;
            box-shadow: 0 5px 20px rgba(0,0,0,0.3);
            border: 2px solid #5D4037;
            z-index: 110;
            transition: all 0.3s;
        }

        .wardrobe-control-3d:hover {
            transform: translateX(-50%) scale(1.1);
            background: linear-gradient(to bottom, #A0522D, #8B4513);
        }

        /* تأثيرات الأداء */
        .wardrobe-3d * {
            will-change: transform;
            backface-visibility: hidden;
        }

        /* زر التحكم بالدولاب */
        .wardrobe-toggle-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            z-index: 100;
            transition: all 0.3s;
        }

        .wardrobe-toggle-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 7px 20px rgba(0,0,0,0.4);
        }

        /* تصميم الملابس على القماش */
        .dress-template {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .drawing-layer {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 2;
        }
    </style>
</head>
<body>

        <div class="app-container">
        <!-- الشريط الجانبي -->
        <div class="sidebar">
            <div class="nav-item">
                <a href="{{ route('home') }}" class="nav-icon active">
                    <i class="fas fa-home"></i>
                </a>
                <span class="nav-label">الرئيسية</span>
            </div>

            <div class="nav-item">
                <a href="" class="nav-icon">
                    <i class="fas fa-palette"></i>
                </a>
                <span class="nav-label">تصاميمي</span>
            </div>

            <div class="nav-item">
                <a href="" class="nav-icon">
                    <i class="fas fa-tshirt"></i>
                </a>
                <span class="nav-label">المشتريات</span>
            </div>

            <div class="nav-item">
                <a href="" class="nav-icon">
                    <i class="fas fa-file-invoice"></i>
                </a>
                <span class="nav-label">الفواتير</span>
            </div>

            <div class="nav-item">
                <a href="{{ route('profile') }}" class="nav-icon">
                    <i class="fas fa-user"></i>
                </a>
                <span class="nav-label">الملف الشخصي</span>
            </div>
        </div>

        <!-- منطقة التصميم -->
        <div class="design-area">
            <!-- صورة المستخدم في لوحة التحكم -->
            <div class="user-panel">
                <div class="user-avatar neumorphic-btn">
                    <i class="fas fa-user"></i>
                </div>
            </div>

            <!-- طبقات الرسم -->
            <canvas id="dress-template" class="dress-template"></canvas>
            <canvas id="drawing-layer" class="drawing-layer"></canvas>
        </div>

        <!-- شريط الأدوات -->
        <div class="tools-panel">
            <div class="tool-item">
                <div class="tool-icon active" title="فرشاة" data-tool="brush" id="brush-tool">
                    <i class="fas fa-paint-brush"></i>
                </div>
                <span class="tool-label">فرشاة</span>
            </div>

            <div class="tool-item">
                <div class="tool-icon" title="قلم" data-tool="pen" id="pen-tool">
                    <i class="fas fa-pen"></i>
                </div>
                <span class="tool-label">قلم</span>
            </div>

            <div class="tool-item">
                <div class="tool-icon" title="ممحاة" data-tool="eraser" id="eraser-tool">
                    <i class="fas fa-eraser"></i>
                </div>
                <span class="tool-label">ممحاة</span>
            </div>

            <div class="tool-item">
                <div class="tool-icon" title="ألوان" data-tool="color" id="color-tool">
                    <i class="fas fa-palette"></i>
                </div>
                <span class="tool-label">ألوان</span>
            </div>

            <div class="tool-item">
                <div class="tool-icon" title="مقاس الأداة" data-tool="size" id="size-tool">
                    <i class="fas fa-ruler"></i>
                </div>
                <span class="tool-label">مقاس الأداة</span>
            </div>

            <div class="tool-item">
                <div class="tool-icon" title="المقاسات" data-tool="dress-sizes" id="sizes-tool">
                    <i class="fas fa-ruler-combined"></i>
                </div>
                <span class="tool-label">المقاسات</span>
            </div>

            <div class="tool-item">
                <div class="tool-icon" title="لون الفستان" data-tool="dress-color" id="dress-color-tool">
                    <i class="fas fa-fill-drip"></i>
                </div>
                <span class="tool-label">لون الفستان</span>
            </div>
        </div>

        <!-- لوحة الألوان -->
        <div class="palette color-palette" id="color-palette">
            <div class="palette-title">اختر لونًا</div>
            <div class="color-grid">
                <div class="color-option selected" style="background: #000000;"></div>
                <div class="color-option" style="background: #ff0000;"></div>
                <div class="color-option" style="background: #00ff00;"></div>
                <div class="color-option" style="background: #0000ff;"></div>
                <div class="color-option" style="background: #ffff00;"></div>
                <div class="color-option" style="background: #ff00ff;"></div>
                <div class="color-option" style="background: #00ffff;"></div>
                <div class="color-option" style="background: #ff9900;"></div>
                <div class="color-option" style="background: #9900ff;"></div>
                <div class="color-option" style="background: #ff0099;"></div>
                <div class="color-option" style="background: #0099ff;"></div>
                <div class="color-option" style="background: #99ff00;"></div>
                <div class="color-option" style="background: #ffffff; border: 1px solid #ccc;"></div>
                <div class="color-option" style="background: #cccccc;"></div>
                <div class="color-option" style="background: #666666;"></div>
            </div>
        </div>

        <!-- لوحة أحجام الأدوات -->
        <div class="palette size-palette" id="size-palette">
            <div class="palette-title">اختر مقاس الأداة</div>
            <div class="size-option" data-size="1">1px</div>
            <div class="size-option" data-size="3">3px</div>
            <div class="size-option selected" data-size="5">5px</div>
            <div class="size-option" data-size="8">8px</div>
            <div class="size-option" data-size="10">10px</div>
            <div class="size-option" data-size="15">15px</div>
            <div class="size-option" data-size="20">20px</div>
            <div class="size-option" data-size="30">30px</div>
            <div class="size-option" data-size="50">50px</div>
        </div>

        <!-- لوحة المقاسات -->
        <div class="palette sizes-palette" id="sizes-palette">
            <div class="palette-title">اختر مقاس الفستان</div>
            <div class="size-option">XS</div>
            <div class="size-option selected">S</div>
            <div class="size-option">M</div>
            <div class="size-option">L</div>
            <div class="size-option">XL</div>
            <div class="size-option">XXL</div>
        </div>

        <!-- لوحة ألوان الفستان -->
        <div class="palette dress-colors-palette" id="dress-colors-palette">
            <div class="palette-title">اختر لون الفستان</div>
            <div class="color-grid">
                <div class="color-option selected" style="background: #ffffff;"></div>
                <div class="color-option" style="background: #f8bbd0;"></div>
                <div class="color-option" style="background: #e1bee7;"></div>
                <div class="color-option" style="background: #d1c4e9;"></div>
                <div class="color-option" style="background: #c5cae9;"></div>
                <div class="color-option" style="background: #b3e5fc;"></div>
                <div class="color-option" style="background: #b2ebf2;"></div>
                <div class="color-option" style="background: #b2dfdb;"></div>
                <div class="color-option" style="background: #dcedc8;"></div>
                <div class="color-option" style="background: #f0f4c3;"></div>
                <div class="color-option" style="background: #ffecb3;"></div>
                <div class="color-option" style="background: #ffe0b2;"></div>
                <div class="color-option" style="background: #ffccbc;"></div>
                <div class="color-option" style="background: #d7ccc8;"></div>
                <div class="color-option" style="background: #cfd8dc;"></div>
            </div>
        </div>

        <!-- لوحة التحكم -->
        <div class="palette control-palette neumorphic-card" id="control-palette">
            <button class="control-btn delete-btn neumorphic-btn" id="delete-design">
                <i class="fas fa-trash"></i>
                <span>حذف</span>
            </button>
            <button class="control-btn reset-btn neumorphic-btn" id="reset-design">
                <i class="fas fa-redo"></i>
                <span>إعادة تعيين</span>
            </button>
            <button class="control-btn save-btn neumorphic-btn" id="save-design">
                <i class="fas fa-save"></i>
                <span>حفظ</span>
            </button>
        </div>

        <!-- دولاب الملابس ثلاثي الأبعاد -->
        <div class="wardrobe-container-3d" id="wardrobe-container-3d">
            <div class="wardrobe-3d" id="wardrobe-3d">
                <div class="wardrobe-body-3d"></div>
                <div class="wardrobe-door-3d door-left-3d">
                    <div class="door-handle-3d"></div>
                </div>
                <div class="wardrobe-door-3d door-right-3d">
                    <div class="door-handle-3d"></div>
                </div>
                <div class="wardrobe-inside-3d">
                    <!-- السكة العلوية -->
                    <div class="clothes-rail-3d">
                        <!-- علاقات الملابس -->
                        <div class="hanger-3d" style="left: 15%;"></div>
                        <div class="hanger-3d" style="left: 35%; transform: rotate(5deg);"></div>
                        <div class="hanger-3d" style="left: 55%; transform: rotate(-3deg);"></div>
                        <div class="hanger-3d" style="left: 75%;"></div>
                    </div>

                    <!-- قطع الملابس -->
                    <div class="clothing-item-3d" data-dress="dress1">
                        <div class="clothing-img-container-3d">
                            <img src="{{ asset('imgs/bg8.jpg') }}" class="clothing-img-3d" alt="فستان">
                        </div>
                        <div class="clothing-name-3d">فستان حديث</div>
                    </div>

                    <div class="clothing-item-3d" data-dress="dress2">
                        <div class="clothing-img-container-3d">
                            <img src="{{ asset('imgs/bg8.jpg') }}" class="clothing-img-3d" alt="جاكيت">
                        </div>
                        <div class="clothing-name-3d">جاكيت جلد</div>
                    </div>

                    <div class="clothing-item-3d" data-dress="dress3">
                        <div class="clothing-img-container-3d">
                            <img src="{{ asset('imgs/bg8.jpg') }}" class="clothing-img-3d" alt="قميص">
                        </div>
                        <div class="clothing-name-3d">قميص رسمي</div>
                    </div>

                    <div class="clothing-item-3d" data-dress="dress4">
                        <div class="clothing-img-container-3d">
                            <img src="{{ asset('imgs/bg2.jpg') }}" class="clothing-img-3d" alt="بنطال">
                        </div>
                        <div class="clothing-name-3d">بنطال جينز</div>
                    </div>
                </div>
            </div>
            <button class="wardrobe-control-3d" id="wardrobe-control-3d">
                <i class="fas fa-tshirt"></i>
            </button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // تهيئة لوحة الرسم
            const dressCanvas = document.getElementById('dress-template');
            const dressCtx = dressCanvas.getContext('2d');
            const drawingCanvas = document.getElementById('drawing-layer');
            const drawingCtx = drawingCanvas.getContext('2d');
            let isDrawing = false;
            let currentTool = 'brush';
            let currentColor = '#000000';
            let currentSize = 5;
            let lastX = 0;
            let lastY = 0;
            let dressColor = '#ffffff';
            let currentDress = null;

            // ضبط حجم اللوحة
            function resizeCanvas() {
                const designArea = document.querySelector('.design-area');
                dressCanvas.width = designArea.clientWidth;
                dressCanvas.height = designArea.clientHeight;
                drawingCanvas.width = designArea.clientWidth;
                drawingCanvas.height = designArea.clientHeight;

                // رسم خلفية بيضاء
                drawDress();
            }

            // رسم الفستان المحدد
            function drawDress() {
                dressCtx.clearRect(0, 0, dressCanvas.width, dressCanvas.height);
                dressCtx.fillStyle = dressColor;
                dressCtx.fillRect(0, 0, dressCanvas.width, dressCanvas.height);

                if (currentDress) {
                    // هنا يمكنك رسم الفستان المحدد
                    // مثال: رسم شكل الفستان الأساسي
                    dressCtx.fillStyle = '#f0f0f0';
                    dressCtx.beginPath();
                    dressCtx.moveTo(dressCanvas.width * 0.3, dressCanvas.height * 0.1);
                    dressCtx.lineTo(dressCanvas.width * 0.7, dressCanvas.height * 0.1);
                    dressCtx.lineTo(dressCanvas.width * 0.8, dressCanvas.height * 0.9);
                    dressCtx.lineTo(dressCanvas.width * 0.2, dressCanvas.height * 0.9);
                    dressCtx.closePath();
                    dressCtx.fill();
                }
            }

            window.addEventListener('resize', resizeCanvas);
            resizeCanvas();

            // وظائف الرسم
            function startDrawing(e) {
                isDrawing = true;
                [lastX, lastY] = [e.offsetX, e.offsetY];

                // للفرشاة الكبيرة نرسم دائرة عند النقر
                if (currentTool === 'brush' && currentSize >= 20) {
                    drawingCtx.beginPath();
                    drawingCtx.arc(lastX, lastY, currentSize/2, 0, Math.PI * 2);

                    if (currentTool === 'eraser') {
                        drawingCtx.fillStyle = 'transparent';
                        drawingCtx.clearRect(lastX - currentSize/2, lastY - currentSize/2, currentSize, currentSize);
                    } else {
                        drawingCtx.fillStyle = currentColor;
                        drawingCtx.fill();
                    }
                }
            }

            function draw(e) {
                if (!isDrawing) return;

                if (currentTool === 'brush' && currentSize >= 20) {
                    // للفرشاة الكبيرة نرسم خطوط عريضة
                    drawingCtx.beginPath();
                    drawingCtx.moveTo(lastX, lastY);
                    drawingCtx.lineTo(e.offsetX, e.offsetY);

                    if (currentTool === 'eraser') {
                        drawingCtx.strokeStyle = 'transparent';
                        drawingCtx.globalCompositeOperation = 'destination-out';
                    } else {
                        drawingCtx.strokeStyle = currentColor;
                        drawingCtx.globalCompositeOperation = 'source-over';
                    }

                    drawingCtx.lineWidth = currentSize;
                    drawingCtx.lineCap = 'round';
                    drawingCtx.lineJoin = 'round';
                    drawingCtx.stroke();
                } else {
                    // للقلم والفرشاة الصغيرة
                    drawingCtx.beginPath();
                    drawingCtx.moveTo(lastX, lastY);
                    drawingCtx.lineTo(e.offsetX, e.offsetY);

                    if (currentTool === 'eraser') {
                        drawingCtx.strokeStyle = 'transparent';
                        drawingCtx.globalCompositeOperation = 'destination-out';
                    } else {
                        drawingCtx.strokeStyle = currentColor;
                        drawingCtx.globalCompositeOperation = 'source-over';
                    }

                    // اختلاف بين القلم والفرشاة
                    if (currentTool === 'pen') {
                        drawingCtx.lineWidth = Math.max(1, currentSize - 2); // القلم أنحف
                    } else {
                        drawingCtx.lineWidth = currentSize;
                    }

                    drawingCtx.lineCap = 'round';
                    drawingCtx.lineJoin = 'round';
                    drawingCtx.stroke();
                }

                [lastX, lastY] = [e.offsetX, e.offsetY];
            }

            function stopDrawing() {
                isDrawing = false;
                drawingCtx.globalCompositeOperation = 'source-over';
            }

            drawingCanvas.addEventListener('mousedown', startDrawing);
            drawingCanvas.addEventListener('mousemove', draw);
            drawingCanvas.addEventListener('mouseup', stopDrawing);
            drawingCanvas.addEventListener('mouseout', stopDrawing);

            // إدارة الأدوات
            const tools = document.querySelectorAll('.tool-icon');
            tools.forEach(tool => {
                tool.addEventListener('click', function() {
                    tools.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    currentTool = this.dataset.tool;

                    // إظهار/إخفاء اللوحات المنبثقة
                    document.getElementById('color-palette').style.display = 'none';
                    document.getElementById('size-palette').style.display = 'none';
                    document.getElementById('sizes-palette').style.display = 'none';
                    document.getElementById('dress-colors-palette').style.display = 'none';

                    if (currentTool === 'color') {
                        document.getElementById('color-palette').style.display = 'block';
                    } else if (currentTool === 'size') {
                        document.getElementById('size-palette').style.display = 'block';
                    } else if (currentTool === 'dress-sizes') {
                        document.getElementById('sizes-palette').style.display = 'block';
                    } else if (currentTool === 'dress-color') {
                        document.getElementById('dress-colors-palette').style.display = 'block';
                    }

                    // إظهار لوحة التحكم دائماً
                    document.getElementById('control-palette').style.display = 'flex';
                });
            });

            // إدارة الألوان
            const colorOptions = document.querySelectorAll('.color-option');
            colorOptions.forEach(color => {
                color.addEventListener('click', function() {
                    colorOptions.forEach(c => c.classList.remove('selected'));
                    this.classList.add('selected');
                    currentColor = window.getComputedStyle(this).backgroundColor;

                    // إغلاق اللوحة بعد الاختيار
                    if (this.closest('#color-palette')) {
                        document.getElementById('color-palette').style.display = 'none';
                        document.getElementById('brush-tool').click();
                    } else if (this.closest('#dress-colors-palette')) {
                        document.getElementById('dress-colors-palette').style.display = 'none';
                        dressColor = window.getComputedStyle(this).backgroundColor;
                        drawDress();
                        document.getElementById('brush-tool').click();
                    }
                });
            });

            // إدارة أحجام الأدوات
            const sizeOptions = document.querySelectorAll('.size-option[data-size]');
            sizeOptions.forEach(size => {
                size.addEventListener('click', function() {
                    sizeOptions.forEach(s => s.classList.remove('selected'));
                    this.classList.add('selected');
                    currentSize = parseInt(this.dataset.size);
                    document.getElementById('size-palette').style.display = 'none';
                });
            });

            // إدارة مقاسات الفستان
            const dressSizes = document.querySelectorAll('#sizes-palette .size-option');
            dressSizes.forEach(size => {
                size.addEventListener('click', function() {
                    dressSizes.forEach(s => s.classList.remove('selected'));
                    this.classList.add('selected');
                    document.getElementById('sizes-palette').style.display = 'none';
                    // هنا يمكنك تغيير حجم الفستان حسب المقاس المختار
                });
            });

            // إدارة تصاميم الفستان من الدولاب
            const clothingItems = document.querySelectorAll('.clothing-item-3d');
            clothingItems.forEach(item => {
                item.addEventListener('click', function() {
                    currentDress = this.dataset.dress;
                    drawDress();

                    // إغلاق الدولاب بعد الاختيار
                    toggleWardrobe3D();
                });
            });

            // أحداث أزرار التحكم
            document.getElementById('delete-design').addEventListener('click', function() {
                if (confirm('هل أنت متأكد من حذف التصميم الحالي؟')) {
                    drawingCtx.clearRect(0, 0, drawingCanvas.width, drawingCanvas.height);
                }
            });

            document.getElementById('save-design').addEventListener('click', function() {
                // دمج الطبقات للحفظ
                const tempCanvas = document.createElement('canvas');
                tempCanvas.width = dressCanvas.width;
                tempCanvas.height = dressCanvas.height;
                const tempCtx = tempCanvas.getContext('2d');

                // رسم طبقة الفستان أولاً
                tempCtx.drawImage(dressCanvas, 0, 0);

                // ثم رسم طبقة التصميم فوقها
                tempCtx.drawImage(drawingCanvas, 0, 0);

                // حفظ الصورة المدمجة
                const link = document.createElement('a');
                link.download = 'تصميم-فستان.png';
                link.href = tempCanvas.toDataURL('image/png');
                link.click();

                alert('تم حفظ التصميم بنجاح!');
            });

            document.getElementById('reset-design').addEventListener('click', function() {
                if (confirm('هل تريد إعادة تعيين التصميم؟')) {
                    drawingCtx.clearRect(0, 0, drawingCanvas.width, drawingCanvas.height);
                }
            });

            // التحكم في الدولاب ثلاثي الأبعاد
            const wardrobe3D = document.getElementById('wardrobe-3d');
            const wardrobeControl3D = document.getElementById('wardrobe-control-3d');
            let isWardrobeOpen = false;

            function toggleWardrobe3D() {
                isWardrobeOpen = !isWardrobeOpen;

                if (isWardrobeOpen) {
                    wardrobe3D.classList.add('open');
                    wardrobeControl3D.innerHTML = '<i class="fas fa-times"></i>';

                    // تأثير اهتزاز الملابس عند الفتح
                    const hangers = document.querySelectorAll('.hanger-3d');
                    hangers.forEach((hanger, index) => {
                        setTimeout(() => {
                            hanger.style.transform = hanger.style.transform.replace(/rotate\([^)]*\)/, '') + ' rotate(8deg)';
                            setTimeout(() => {
                                hanger.style.transform = hanger.style.transform.replace(/rotate\([^)]*\)/, '') + ' rotate(-5deg)';
                                setTimeout(() => {
                                    hanger.style.transform = hanger.style.transform.replace(/rotate\([^)]*\)/, '');
                                }, 200);
                            }, 200);
                        }, index * 100);
                    });
                } else {
                    wardrobe3D.classList.remove('open');
                    wardrobeControl3D.innerHTML = '<i class="fas fa-tshirt"></i>';
                }
            }

            wardrobeControl3D.addEventListener('click', toggleWardrobe3D);

            // تأثير ثلاثي الأبعاد عند التحويم على الملابس
            document.querySelectorAll('.clothing-item-3d').forEach(item => {
                item.addEventListener('mousemove', (e) => {
                    if (!isWardrobeOpen) return;

                    const rect = item.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    const centerX = rect.width / 2;
                    const centerY = rect.height / 2;

                    const rotateY = (x - centerX) / 20;
                    const rotateX = (centerY - y) / 20;

                    item.style.transform = `translateY(-5px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
                });

                item.addEventListener('mouseleave', () => {
                    item.style.transform = 'translateY(0) rotateX(0) rotateY(0)';
                });
            });

            // إظهار لوحة التحكم عند التحميل
            document.getElementById('control-palette').style.display = 'flex';

            // إغلاق اللوحات عند النقر خارجها
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.palette') && !e.target.closest('.tool-icon')) {
                    document.getElementById('color-palette').style.display = 'none';
                    document.getElementById('size-palette').style.display = 'none';
                    document.getElementById('sizes-palette').style.display = 'none';
                    document.getElementById('dress-colors-palette').style.display = 'none';
                }
            });
        });
    </script>

</body>
</html>
