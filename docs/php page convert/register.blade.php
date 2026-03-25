<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>إنشاء حساب</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    :root{ --bg:#e0e5ec; --text:#4a5568; --primary:#4a7eff; --shadow-d:#a3b1c6; --shadow-l:#ffffff; }
    *{box-sizing:border-box} html,body{height:100%}
    body{ margin:0;padding:20px;font-family:Arial,sans-serif;color:var(--text);background:var(--bg);
      display:flex;align-items:center;justify-content:center; }

    .card{
      width:100%;max-width:920px;min-height:500px;
      background:var(--bg);border-radius:24px;box-shadow:8px 8px 15px var(--shadow-d), -8px -8px 15px var(--shadow-l);
      overflow:hidden;display:flex;direction:ltr;
    }
    .illustration{
      flex:1 1 48%;
      background:url("{{ asset('imgs/auth-side.jpg') }}") center/cover no-repeat;
      position:relative;
    }
    .illustration::after{content:"";position:absolute;inset:0;background:linear-gradient(135deg, rgba(255,255,255,0.10), rgba(74,126,255,0.10));}
    .form-pane{
      flex:1 1 52%; padding:28px 28px; position:relative; direction:rtl;
      display:flex;flex-direction:column;justify-content:center;
    }

    .register-title{font-size:22px;margin:0 0 14px;position:relative;z-index:2;color:var(--text);}
    .register-title::after{content:"";position:absolute;bottom:-10px;right:0;width:60px;height:3px;background:var(--primary);border-radius:10px;}

    .input-group{margin:16px 0;position:relative;z-index:2;}
    .input-group label{display:block;margin-bottom:6px;font-weight:bold;}
    .input-group input{
      width:100%;font-size:14px;padding:12px 14px;border:none;border-radius:14px;background:var(--bg);
      box-shadow:inset 5px 5px 10px var(--shadow-d), inset -5px -5px 10px var(--shadow-l);transition:box-shadow .25s ease;
    }
    .input-group input:focus{outline:0;box-shadow:inset 3px 3px 6px var(--shadow-d), inset -3px -3px 6px var(--shadow-l);}

    .btn{border:none;cursor:pointer;border-radius:14px;font-size:14px;padding:12px 16px;color:#6d7587;background:var(--bg);
      box-shadow:5px 5px 10px var(--shadow-d), -5px -5px 10px var(--shadow-l);transition:box-shadow .2s, transform .2s;position:relative;z-index:2;}
    .btn:hover{box-shadow:inset 5px 5px 10px var(--shadow-d), inset -5px -5px 10px var(--shadow-l);}
    .btn:active{transform:translateY(1px);}
    .btn-primary{width:100%;color:var(--primary);margin-top:12px;}

    .social-login{margin:22px 0 14px;position:relative;z-index:2;}
    .social-buttons{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;}
    .social-btn{display:inline-flex;align-items:center;gap:8px;padding:10px 16px;border-radius:14px;border:0;background:var(--bg);
      box-shadow:5px 5px 10px var(--shadow-d), -5px -5px 10px var(--shadow-l);cursor:pointer;transition:box-shadow .2s;color:inherit;}
    .social-btn:hover{box-shadow:inset 5px 5px 10px var(--shadow-d), inset -5px -5px 10px var(--shadow-l);}
    .google-btn{color:#DB4437;} .facebook-btn{color:#4267B2;}
    .divider{display:flex;align-items:center;gap:10px;margin:18px 0;}
    .divider::before,.divider::after{content:"";flex:1;border-bottom:1px solid #d1d9e6;}
    .divider-text{color:#6d7587;font-size:13px;}

    .register-links{text-align:center;margin-top:14px;}
    .register-links a{color:var(--primary);text-decoration:none;}
    .register-links a:hover{text-decoration:underline;}

    .bubbles-container{position:absolute;inset:0;z-index:1;pointer-events:none;border-radius:24px;overflow:hidden;}
    .bubble{position:absolute;bottom:-100px;background:rgba(74,126,255,.18);border-radius:50%;animation:bubble 14s linear infinite;opacity:.6;}
    @keyframes bubble{0%{transform:translateY(0) rotate(0);opacity:.6;border-radius:50%}100%{transform:translateY(-900px) rotate(720deg);opacity:0;border-radius:42%}}
    @media(prefers-reduced-motion:reduce){.bubble{animation:none;display:none}}

    @media (max-width: 820px){
      .card{flex-direction:column;direction:rtl;}
      .illustration{min-height:200px;}
      .form-pane{padding:22px 20px;}
    }
  </style>
</head>
<body>
  <div class="card">
    <div class="illustration" aria-hidden="true"></div>

    <div class="form-pane">
      <div class="bubbles-container" id="bubblesContainer"></div>

      <h1 class="register-title">إنشاء حساب جديد</h1>

      <div class="social-login">
        <div class="social-buttons">
          <button class="social-btn google-btn" type="button" onclick="registerWithGoogle()"><i class="fab fa-google"></i> جوجل</button>
          <button class="social-btn facebook-btn" type="button" onclick="registerWithFacebook()"><i class="fab fa-facebook-f"></i> فيسبوك</button>
        </div>
        <div class="divider"><span class="divider-text">أو</span></div>
      </div>

      <form id="registerForm" novalidate>
        <div class="input-group">
          <label for="fullName">الاسم الكامل</label>
          <input id="fullName" type="text" required placeholder="أدخل اسمك الكامل" autocomplete="name" />
        </div>
        <div class="input-group">
          <label for="email">البريد الإلكتروني</label>
          <input id="email" type="email" required placeholder="أدخل بريدك الإلكتروني" autocomplete="email" />
        </div>
        <div class="input-group">
          <label for="password">كلمة المرور</label>
          <input id="password" type="password" required placeholder="أدخل كلمة المرور" autocomplete="new-password" />
        </div>
        <div class="input-group">
          <label for="confirmPassword">تأكيد كلمة المرور</label>
          <input id="confirmPassword" type="password" required placeholder="أعد إدخال كلمة المرور" autocomplete="new-password" />
        </div>

        <button class="btn btn-primary" type="submit"><i class="fas fa-user-plus"></i> إنشاء حساب</button>
      </form>

      <div class="register-links">
        <p>هل لديك حساب بالفعل؟ <a href="{{ route('login') }}">سجل الدخول الآن</a></p>
      </div>
    </div>
  </div>

  <script>
    function createBubbles(){
      const c=document.getElementById('bubblesContainer'); const n=14;
      for(let i=0;i<n;i++){
        const b=document.createElement('div'); b.className='bubble';
        const size=Math.random()*60+22;
        b.style.width=b.style.height=size+'px';
        b.style.left=(Math.random()*100)+'%';
        b.style.animationDuration=(Math.random()*8+10)+'s';
        b.style.animationDelay=(Math.random()*8)+'s';
        c.appendChild(b);
      }
    }
    window.addEventListener('load',createBubbles);

    const f=document.getElementById('registerForm');
    f.addEventListener('submit',e=>{
      e.preventDefault();
      const pass=document.getElementById('password').value;
      const re=document.getElementById('confirmPassword').value;
      if(pass!==re){ alert('كلمات المرور غير متطابقة!'); return; }
      alert('تم إنشاء الحساب بنجاح!');
    });

    function registerWithGoogle(){ alert('سيتم توجيهك إلى جوجل'); }
    function registerWithFacebook(){ alert('سيتم توجيهك إلى فيسبوك'); }
  </script>
</body>
</html>
