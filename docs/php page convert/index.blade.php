
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

<link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/all.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/style-sections.css') }}">
   
    <style>
        :root {
            --day-bg: linear-gradient(135deg, #e2e2e2 0%, #c9d6ff 100%);
            --night-bg: linear-gradient(135deg, #1e1e1e 0%, #3a3a5a 100%);
            --text-day: #333;
            --text-night: #fff;
            --neumorph-light: #ffffff;
            --neumorph-dark: #a3b1c6;
            --primary-color: #e0e5ec;
            --button-text-day: #6d7587;
            --button-text-night: #ffffff;
        }
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: var(--day-bg);
            color: var(--text-day);
            transition: all 0.5s ease;
            margin: 0;
            padding: 0;
            min-height: 100vh;
              justify-content: center;
            align-items: center;
            font-family: 'Dancing sript' ,cursive;
            overflow-x: hidden;
        }

        body.night-mode {
            background: var(--night-bg);
            color: var(--text-night);
        }

        /* ========== أزرار Neumorphism الموحدة ========== */
         .neumorphic-box {
            background: var(--primary-color);
            border: none;
            padding: 15px 30px;
            border-radius: 15px;
            box-shadow:
                8px 8px 15px var(--neumorph-dark),
                -8px -8px 15px var(--neumorph-light);
            color: var(--button-text-day);
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 10px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100px;
            width: 140px;
        }

         .neumorphic-box:hover {
            box-shadow:
                inset 4px 4px 8px var(--neumorph-dark),
                inset -4px -4px 8px var(--neumorph-light);
        }

        /* ========== بقية العناصر ========== */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 50px;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #4a00e0;
        }

        .nav-icons {
            display: flex;
            gap: 25px;
            align-items: center;
        }

        .search-container {
            display: flex;
            align-items: center;
        }

        .search-bar {
            width: 0;
            overflow: hidden;
            transition: width 0.3s ease;
            padding: 8px 0;
            background: var(--primary-color);
            border: none;
            border-radius: 20px;
            box-shadow:
                inset 3px 3px 5px var(--neumorph-dark),
                inset -3px -3px 5px var(--neumorph-light);
        }

        .search-bar.active {
            width: 200px;
            padding: 8px 15px;
        }

        .main-content {
            display: flex;
            padding: 50px;
            gap: 50px;
            position: relative;
            z-index: 2;
        }

        .welcome-section {
            flex: 1;
            padding-top: 50px;
        }

        .welcome-section h1 {
            font-size: 48px;
            margin-bottom: 20px;
        }

        .auth-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .categories {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            width: 400px;
        }

        .icon {
            font-size: 24px;
            margin-bottom: 10px;
            color: var(--button-text-day);
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background: var(--primary-color);
            min-width: 200px;
            box-shadow:
                8px 8px 15px var(--neumorph-dark),
                -8px -8px 15px var(--neumorph-light);
            z-index: 1;
            border-radius: 5px;
            overflow: hidden;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .dropdown-item {
            padding: 12px 16px;
            display: block;
            color: var(--button-text-day);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background: rgba(163, 177, 198, 0.3);
        }

        .theme-icon {
            cursor: pointer;
            transition: all 0.3s ease;
        }
        body.nigh-mode .dropdown-item,
        body.nigh-mode .icon{
            color: var(--button-text-day);
        }

        .ind h1 {
          font-size: 30px;
          color: #c9d6ff;
          text-transform: uppercase;
          text-shadow: 1px 1px 1px #919191,
                       1px 2px 1px #919191,
                       1px 3px 1px #919191,
                       1px 4px 1px #919191,
                       1px 5px 1px #919191,
                       1px 6px 1px #919191,
                       1px 7px 1px #919191,
                       1px 8px 1px #919191,
                       1px 9px 1px #919191,
                       1px 10px 1px #919191,
                       1px 18px 6px rgba(16,16,16,0.4),
                       1px 22px 10px rgba(16,16,16,0.2),
                       1px 25px 35px rgba(16,16,16,0.2),
                       1px 30px 60px rgba(16,16,16,0.4);
          transition: all 0.4s ease;
     }
     .ind h1:hover{
        color: #c9d6ff;
        transform: scale(1.2);
     }
     .auth-buttons{
       gap: 10px;
     }
     .neumorphic-btn{
       width: 90px ;
       height: 34px;
       border: none;
       border-radius: 17px;
       font-size: 11px ;
       font-weight: bold;
       cursor: pointer;
       transition: all 0.3s ease;
       position: relative;
       overflow: hidden;
       display: flex;
       align-items: center;
       justify-content: center;
         color: var(--button-text-day);
          background: var(--primary-color);
     }
     body.night-mode .neumorphic-box {
        color: var(--button-text-night) !important;
     }
       .neumorphic-btn::before{
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to right,
                              rgba(255, 255, 255,0.3),
                               rgba(255, 255, 255,0.1));
         transform: translateX(-100%);
         transition: transform 0.4s ease;
       }
        .neumorphic-btn:hover{
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0, 0, 0.15);
        }
         .neumorphic-btn:active{
            transform: translateY(0);
         }
          .neumorphic-btn :hover:after{
            opacity: 1;
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

        body.night-mode .star {
            background-color: #03dac6;
        }
        /* اجعل الهيدر فوق الـ main-content */
header {
  position: relative;
  z-index: 10; /* أعلى من main-content (2) */
}

/* ارفع طبقة القائمة المنسدلة فوق الجميع */
.dropdown {
  position: relative;      /* لتثبيت الإحداثيات للـ absolute بداخلها */
  z-index: 1000;
}

.dropdown-content {
  position: absolute;
  top: 100%;               /* تحت الأيقونة مباشرة */
  right: 0;
  z-index: 9999;           /* أعلى من أي عناصر أخرى */
}

    </style>

</head>
<body>
        <!-- النجوم المتحركة -->
    <div class="stars-container" id="stars-container"></div>

    <header>
         <div class="auth-buttons">
                <a href="{{ route('login') }}" class="neumorphic-btn">تسجيل دخول</a>
                <a href="{{ route('register') }}" class="neumorphic-btn">إنشاء حساب</a>


        </div>
        <div class="nav-icons">
            <div class="search-container">
                <i class="fas fa-search icon" id="search-icon"></i>
                <input type="text" class="search-bar" placeholder="ابحث...">
            </div>



            <i class="fas fa-moon icon theme-icon" id="theme-toggle"></i>

              <div class="dropdown">
                <i class="fas fa-user-cog icon"></i>
                <div class="dropdown-content">
                    <a href="{{ route('order') }}" class="dropdown-item">الطلبات</a>
                    <a href="{{ route('contact') }}" class="dropdown-item">تواصل معنا </a>
                    <a href="{{ route('profile') }}" class="dropdown-item">الملف الشخصي</a>


                    <a href="#" class="dropdown-item">تسجيل خروج</a>
                </div>
            </div>
        </div>
    </header>

    <div class="main-content">
        <div class="welcome-section">
            <h1>مرحباً بك في عالم الأزياء</h1>
            <p>اكتشف أحدث التصاميم والعروض الحصرية لدينا. تصفح معرضنا أو ابدأ في تصميم ملابسك الخاصة!</p>

            <div class="ind">
                <h1>DESIGNS</h1>
            </div>
        </div>

        <div class="categories">
            <a href="{{ route('gallary') }}" class="neumorphic-box">
                <div class="icon"><i class="fas fa-images"></i></div>
                <h3>المعرض</h3>
            </a>

            <a href="{{ route('dashboard') }}" class="neumorphic-box">
                <div class="icon"><i class="fas fa-pencil-ruler"></i></div>
                <h3>صفحة العمل</h3>
            </a>
            <a href="{{ route('categories') }}" class="neumorphic-box">
                <div class="icon"><i class="fas fa-tags"></i></div>
                <h3>الأقسام</h3>
            </a>
            <a href="#" class="neumorphic-box">
                <div class="icon"><i class="fas fa-home"></i></div>
                <h3>الرئيسية</h3>
            </a>
        </div>
    </div>

    <script>
        // إنشاء النجوم
        function createStars() {
            const container = document.getElementById('stars-container');
            const starCount = 100;

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

        // تبديل شريط البحث
        const searchIcon = document.getElementById('search-icon');
        const searchBar = document.querySelector('.search-bar');

        searchIcon.addEventListener('click', () => {
            searchBar.classList.toggle('active');
            if (searchBar.classList.contains('active')) {
                searchBar.focus();
            }
        });

        // تبديل الوضع الليلي
        const themeToggle = document.getElementById('theme-toggle');

        themeToggle.addEventListener('click', () => {
            document.body.classList.toggle('night-mode');

            if (document.body.classList.contains('night-mode')) {
                themeToggle.classList.remove('fa-moon');
                themeToggle.classList.add('fa-sun');

                document.documentElement.style.setProperty('--primary-color', '#2d3748');
                document.documentElement.style.setProperty('--neumorph-light', '#4a5568');
                document.documentElement.style.setProperty('--neumorph-dark', '#1a202c');
            } else {
                themeToggle.classList.remove('fa-sun');
                themeToggle.classList.add('fa-moon');

                document.documentElement.style.setProperty('--primary-color', '#e0e5ec');
                document.documentElement.style.setProperty('--neumorph-light', '#ffffff');
                document.documentElement.style.setProperty('--neumorph-dark', '#a3b1c6');
            }
        });

        // تأثير التحميل (الظهور التدريجي)
        document.addEventListener('DOMContentLoaded', () => {
            createStars();

            const elements = document.querySelectorAll('header, .main-content');
            elements.forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
            });

            setTimeout(() => {
                elements.forEach(el => {
                    el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                });
            }, 100);
        });
    </script>

</body>
</html>
