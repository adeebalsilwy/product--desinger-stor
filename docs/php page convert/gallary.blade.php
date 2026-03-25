<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

        <title>كاروسيل ثلاثي الأبعاد مع النجوم</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #1e1f26, #3a3a5a);
            color: white;
            overflow: hidden;
            height: 100vh;
        }

        /* النجوم المتحركة */
        .stars-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            pointer-events: none;
        }

        .star {
            position: absolute;
            background-color: #fff;
            border-radius: 50%;
            animation: twinkle var(--duration) infinite ease-in-out;
            opacity: 0;
        }

        @keyframes twinkle {
            0%, 100% { opacity: 0; }
            50% { opacity: var(--opacity); }
        }

        /* الكاروسيل */
        .carousel {
            position: relative;
            z-index: 2;
            height: 100vh;
            overflow: hidden;
        }

        .carousel-item {
            --items: 10;
            --width: clamp(150px, 30vw, 300px);
            --height: clamp(200px, 40vw, 400px);
            --x: calc(var(--active) * 800%);
            --y: calc(var(--active) * 200%);
            --rot: calc(var(--active) * 120deg);
            --opacity: calc(var(--zIndex) / var(--items) * 3 - 2);
            overflow: hidden;
            position: absolute;
            z-index: var(--zIndex);
            width: var(--width);
            height: var(--height);
            margin: calc(var(--height) * -0.5) 0 0 calc(var(--width) * -0.5);
            border-radius: 10px;
            top: 50%;
            left: 50%;
            user-select: none;
            transform-origin: 0% 100%;
            box-shadow: 0 10px 50px 10px rgba(0, 0, 0, .5);
            background: black;
            pointer-events: all;
            transform: translate(var(--x), var(--y)) rotate(var(--rot));
            transition: transform .8s cubic-bezier(0, 0.02, 0, 1);
        }

        .carousel-item .carousel-box {
            position: absolute;
            z-index: 1;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            transition: opacity .8s cubic-bezier(0, 0.02, 0, 1);
            opacity: var(--opacity);
            font-family: 'Orelo-sw-db', serif;
        }

        .carousel-item .carousel-box:before {
            content: '';
            position: absolute;
            z-index: 1;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(0, 0, 0, .3), rgba(0, 0, 0, 0) 30%, rgba(0, 0, 0, 0) 50%, rgba(0, 0, 0, .5));
        }

        .carousel-item .title {
            position: absolute;
            z-index: 1;
            color: #fff;
            bottom: 20px;
            left: 20px;
            transition: opacity .8s cubic-bezier(0, 0.02, 0, 1);
            font-size: clamp(20px, 3vw, 30px);
            text-shadow: 0 4px 4px rgba(0, 0, 0, .1);
        }

        .carousel-item .num {
            position: absolute;
            z-index: 1;
            color: #fff;
            top: 10px;
            left: 20px;
            transition: opacity .8s cubic-bezier(0, 0.02, 0, 1);
            font-size: clamp(20px, 10vw, 80px);
        }

        .carousel-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            pointer-events: none;
        }

        /* المؤشرات */
        .cursor {
            position: fixed;
            z-index: 10;
            top: 0;
            left: 0;
            --size: 40px;
            width: var(--size);
            height: var(--size);
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, .2);
            margin: calc(var(--size) * -0.5) 0 0 calc(var(--size) * -0.5);
            transition: transform .85s cubic-bezier(0, 0.02, 0, 1);
            display: none;
            pointer-events: none;
        }

        .cursor2 {
            --size: 2px;
            transition-duration: .7s;
        }

        @media (pointer: fine) {
            .cursor {
                display: block;
            }
        }
    </style>

</head>
<body>
        <!-- النجوم المتحركة -->
    <div class="stars-container" id="stars-container"></div>

    <!-- الكاروسيل -->
    <div class="carousel">
        <div class="carousel-item">
            <div class="carousel-box">
                <div class="title">مغلق</div>
                <div class="num">01</div>
               <img src="{{ asset('imgs/bg7.jpg') }}" alt="مغلق">
            </div>
        </div>
        <div class="carousel-item">
            <div class="carousel-box">
                <div class="title">عاري</div>
                <div class="num">02</div>
               <img src="{{ asset('imgs/bg8.jpg') }}" alt="عاري">
            </div>
        </div>
        <div class="carousel-item">
            <div class="carousel-box">
                <div class="title">ريش</div>
                <div class="num">03</div>
              <img src="{{ asset('imgs/bg9.jpg') }}" alt="ريش">
            </div>
        </div>
        <div class="carousel-item">
            <div class="carousel-box">
                <div class="title">ضيق</div>
                <div class="num">04</div>
               <img src="{{ asset('imgs/bg6.jpg') }}" alt="ضيق">
            </div>
        </div>
        <div class="carousel-item">
            <div class="carousel-box">
                <div class="title">منفوش</div>
                <div class="num">05</div>
               <img src="{{ asset('imgs/bg5.jpg') }}" alt="منفوش">
            </div>
        </div>
        <div class="carousel-item">
            <div class="carousel-box">
                <div class="title">مذيل</div>
                <div class="num">06</div>
               <img src="{{ asset('imgs/bg4.jpg') }}" alt="مذيل">
            </div>
        </div>
        <div class="carousel-item">
            <div class="carousel-box">
                <div class="title">rwdv</div>
                <div class="num">07</div>
               <img src="{{ asset('imgs/bg3.jpg') }}" alt="قصير">
            </div>
        </div>
        <div class="carousel-item">
            <div class="carousel-box">
                <div class="title">مكمم</div>
                <div class="num">08</div>
               <img src="{{ asset('imgs/bg2.jpg') }}" alt="مكمم">
            </div>
        </div>
        <div class="carousel-item">
            <div class="carousel-box">
                <div class="title">مرقب</div>
                <div class="num">09</div>
                <img src="{{ asset('imgs/bg11.jpg') }}" alt="مرقب">
            </div>
        </div>
        <div class="carousel-item">
            <div class="carousel-box">
                <div class="title">منقوش</div>
                <div class="num">10</div>
                <img src="{{ asset('imgs/bg10.jpg') }}" alt="منقوش">
            </div>
        </div>
    </div>

    <!-- المؤشرات -->
    <div class="cursor"></div>
    <div class="cursor cursor2"></div>

    <script>
        // إنشاء النجوم
        function createStars() {
            const container = document.getElementById('stars-container');
            const starCount = 150;

            for (let i = 0; i < starCount; i++) {
                const star = document.createElement('div');
                star.classList.add('star');

                // حجم عشوائي بين 1px و 3px
                const size = Math.random() * 2 + 1;
                star.style.width = `${size}px`;
                star.style.height = `${size}px`;

                // موضع عشوائي
                star.style.left = `${Math.random() * 100}%`;
                star.style.top = `${Math.random() * 100}%`;

                // مدة وتأثير عشوائي
                star.style.setProperty('--duration', `${Math.random() * 3 + 2}s`);
                star.style.setProperty('--opacity', Math.random() * 0.8 + 0.2);

                // تأخير عشوائي للبدء
                star.style.animationDelay = `${Math.random() * 5}s`;

                container.appendChild(star);
            }
        }

        // تهيئة الكاروسيل
        let progress = 50;
        let startX = 0;
        let active = 0;
        let isDown = false;
        const speedWheel = 0.02;
        const speedDrag = -0.1;

        // الحصول على مؤشر Z
        const getZindex = (array, index) => (array.map((_, i) => (index === i) ? array.length : array.length - Math.abs(index - i)));

        // العناصر
        const $items = document.querySelectorAll('.carousel-item');
        const $cursors = document.querySelectorAll('.cursor');

        // عرض العناصر
        const displayItems = (item, index, active) => {
            const zIndex = getZindex([...$items], active)[index];
            item.style.setProperty('--zIndex', zIndex);
            item.style.setProperty('--active', (index-active)/$items.length);
        }

        // التحريك
        const animate = () => {
            progress = Math.max(0, Math.min(progress, 100));
            active = Math.floor(progress/100*($items.length-1));

            $items.forEach((item, index) => displayItems(item, index, active));
        }

        // النقر على العناصر
        $items.forEach((item, i) => {
            item.addEventListener('click', () => {
                progress = (i/$items.length) * 100 + 10;
                animate();
            });
        });

        // معالجة الأحداث
        const handleWheel = e => {
            const wheelProgress = e.deltaY * speedWheel;
            progress = progress + wheelProgress;
            animate();
        }

        const handleMouseMove = (e) => {
            if (e.type === 'mousemove') {
                $cursors.forEach(($cursor) => {
                    $cursor.style.transform = `translate(${e.clientX}px, ${e.clientY}px)`;
                });
            }

            if (!isDown) return;
            const x = e.clientX || (e.touches && e.touches[0].clientX) || 0;
            const mouseProgress = (x - startX) * speedDrag;
            progress = progress + mouseProgress;
            startX = x;
            animate();
        }

        const handleMouseDown = e => {
            isDown = true;
            startX = e.clientX || (e.touches && e.touches[0].clientX) || 0;
        }

        const handleMouseUp = () => {
            isDown = false;
        }

        // تهيئة الصفحة
        document.addEventListener('DOMContentLoaded', () => {
            createStars();
            animate();

            // إضافة المستمعين للأحداث
            document.addEventListener('mousewheel', handleWheel);
            document.addEventListener('mousedown', handleMouseDown);
            document.addEventListener('mousemove', handleMouseMove);
            document.addEventListener('mouseup', handleMouseUp);
            document.addEventListener('touchstart', handleMouseDown);
            document.addEventListener('touchmove', handleMouseMove);
            document.addEventListener('touchend', handleMouseUp);
        });
    </script>

</body>
</html>
