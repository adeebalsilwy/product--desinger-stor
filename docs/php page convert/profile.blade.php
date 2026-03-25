<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>الملف الشخصي</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    :root{
      --bg:#e0e5ec;
      --text:#4a5568;
      --muted:#6d7587;
      --primary:#4a7eff;
      --danger:#ff6b6b;
      --success:#4caf50;
      --shadow-d:#a3b1c6;
      --shadow-l:#ffffff;
      --radius:20px;
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0; padding:20px;
      font-family:Arial, sans-serif;
      background:var(--bg); color:var(--text);
      display:flex; align-items:center; justify-content:center;
      min-height:100vh;
    }

    /* الحاوية */
    .container{
      width:100%;
      max-width:520px;                 /* كان 350px → الآن أكثر احترافية */
      background:var(--bg);
      border-radius:var(--radius);
      padding:24px;
      box-shadow: 8px 8px 15px var(--shadow-d), -8px -8px 15px var(--shadow-l);
    }
    @media (min-width:480px){ .container{ padding:28px; } }
    @media (min-width:640px){ .container{ padding:32px; } }

    /* أزرار نيومورفزم موحّدة */
    .btn{
      border:0; cursor:pointer; user-select:none;
      padding:11px 18px; border-radius:15px; font-size:14px;
      color:var(--muted); background:var(--bg);
      box-shadow: 5px 5px 10px var(--shadow-d), -5px -5px 10px var(--shadow-l);
      transition: box-shadow .2s ease, transform .2s ease;
      display:inline-flex; align-items:center; gap:8px;
    }
    .btn:hover{ box-shadow: inset 5px 5px 10px var(--shadow-d), inset -5px -5px 10px var(--shadow-l); }
    .btn:active{ transform: translateY(1px); }
    .btn-primary{ color:var(--primary); }
    .btn-danger{ color:var(--danger); }
    .btn-success{ color:var(--success); }

    /* الهيدر */
    .profile-header{ text-align:center; margin-bottom:20px; }
    .profile-name{ font-size:22px; margin:10px 0 6px; color:var(--text); }
    .profile-email{ color:var(--muted); margin:0 0 14px; font-size:13px; }

    /* صورة المستخدم (مربّعة مع حافة لطيفة) */
    .profile-image-wrap{
      position:relative; width:160px; height:160px; margin:0 auto 14px;
    }
    .profile-image{
      width:100%; height:100%; object-fit:cover; border-radius:16px;
      box-shadow: 8px 8px 15px var(--shadow-d), -8px -8px 15px var(--shadow-l);
      background:#ddd;
      display:block;
    }
    .camera-icon{
      position:absolute; bottom:10px; left:10px;
      width:38px; height:38px; border-radius:50%;
      display:flex; align-items:center; justify-content:center;
      background:var(--primary); color:#fff; cursor:pointer;
      box-shadow: 0 2px 5px rgba(0,0,0,.18);
    }
    input[type="file"].hidden-input{ display:none; }

    /* نموذج التعديل */
    .edit-form{
      margin-top:18px; padding:18px; border-radius:15px; background:var(--bg);
      box-shadow: inset 5px 5px 10px var(--shadow-d), inset -5px -5px 10px var(--shadow-l);
    }
    .edit-form[hidden]{ display:none !important; }

    .form-group{ margin-bottom:14px; }
    .form-group label{ display:block; margin-bottom:6px; color:var(--text); font-weight:bold; font-size:13px; }
    .form-group input{
      width:100%; padding:11px 12px; border:0; border-radius:12px; background:var(--bg);
      box-shadow: inset 3px 3px 6px var(--shadow-d), inset -3px -3px 6px var(--shadow-l);
      font-size:14px;
    }
    .form-actions{ display:flex; gap:10px; flex-wrap:wrap; }

    /* قائمة التصاميم */
    .section-title{
      margin:22px 0 12px; font-size:16px; font-weight:bold; color:var(--text);
      position:relative; padding-right:10px;
    }
    .section-title::after{
      content:""; position:absolute; bottom:-8px; right:0; width:60px; height:3px;
      background:var(--primary); border-radius:8px;
    }

    .designs-list{ margin-top:16px; display:grid; gap:10px; }
    .design-item{
      background:var(--bg); border-radius:15px; padding:12px 14px;
      box-shadow: 5px 5px 10px var(--shadow-d), -5px -5px 10px var(--shadow-l);
      display:flex; align-items:center; justify-content:space-between; gap:12px;
      transition: box-shadow .2s ease;
    }
    .design-item:hover{ box-shadow: inset 5px 5px 10px var(--shadow-d), inset -5px -5px 10px var(--shadow-l); }
    .design-title{ font-size:14px; color:var(--text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .design-actions{ display:flex; gap:8px; flex-wrap:wrap; }

    /* تحسينات بسيطة للشاشات الصغيرة */
    @media (max-width:380px){
      .profile-image-wrap{ width:140px; height:140px; }
      .btn{ padding:10px 14px; font-size:13px; }
      .design-title{ max-width: 140px; }
    }
  </style>
</head>
<body>

  <div class="container">
    <header class="profile-header">
      <div class="profile-image-wrap">
        <img id="profileImage" class="profile-image" src="{{ asset('imgs/man.jpg') }}" alt="صورة المستخدم">
        <button class="camera-icon" type="button" onclick="document.getElementById('imageInput').click()" aria-label="تغيير الصورة">
          <i class="fas fa-camera"></i>
        </button>
        <input id="imageInput" class="hidden-input" type="file" accept="image/*" onchange="previewImage(this)">
      </div>

      <h1 class="profile-name" id="profileName">أحمد محمد</h1>
      <p class="profile-email" id="profileEmail">ahmed@example.com</p>

      <button class="btn btn-primary" id="editProfileBtn">
        <i class="fas fa-edit"></i> تعديل الملف
      </button>
    </header>

    <section id="editForm" class="edit-form" hidden>
      <div class="form-group">
        <label for="nameInput">الاسم</label>
        <input id="nameInput" type="text" value="أحمد محمد">
      </div>
      <div class="form-group">
        <label for="emailInput">البريد الإلكتروني</label>
        <input id="emailInput" type="email" value="ahmed@example.com">
      </div>
      <div class="form-actions">
        <button class="btn btn-success" id="saveBtn"><i class="fas fa-save"></i> حفظ</button>
        <button class="btn btn-danger" id="cancelBtn"><i class="fas fa-times"></i> إلغاء</button>
      </div>
    </section>

    <h2 class="section-title">التصاميم المحفوظة</h2>
    <div class="designs-list" id="designsList">
      <div class="design-item" data-design-id="1">
        <span class="design-title">تصميم فستان زفاف 2023</span>
        <div class="design-actions">
          <button class="btn btn-primary" onclick="editDesign(1)"><i class="fas fa-edit"></i> تعديل</button>
          <button class="btn btn-danger" onclick="deleteDesign(1)"><i class="fas fa-trash"></i> حذف</button>
        </div>
      </div>
      <!-- يمكنك تكرار العناصر هنا -->
    </div>
  </div>

  <script>
    // عناصر DOM
    const editProfileBtn = document.getElementById('editProfileBtn');
    const editForm      = document.getElementById('editForm');
    const saveBtn       = document.getElementById('saveBtn');
    const cancelBtn     = document.getElementById('cancelBtn');
    const nameInput     = document.getElementById('nameInput');
    const emailInput    = document.getElementById('emailInput');
    const profileName   = document.getElementById('profileName');
    const profileEmail  = document.getElementById('profileEmail');
    const profileImage  = document.getElementById('profileImage');

    // فتح/إغلاق نموذج التعديل
    editProfileBtn.addEventListener('click', ()=> { editForm.hidden = false; });
    cancelBtn.addEventListener('click', ()=> { editForm.hidden = true; });

    // حفظ التغييرات
    saveBtn.addEventListener('click', ()=>{
      profileName.textContent  = (nameInput.value || '').trim() || 'بدون اسم';
      profileEmail.textContent = (emailInput.value || '').trim() || 'no-email@example.com';
      editForm.hidden = true;
    });

    // معاينة صورة الملف
    function previewImage(input){
      const file = input.files && input.files[0];
      if(!file) return;
      const reader = new FileReader();
      reader.onload = e => { profileImage.src = e.target.result; };
      reader.readAsDataURL(file);
    }

    // تعديل/حذف التصميم
    function editDesign(id){
      alert(`سيتم فتح التصميم ${id} في صفحة العمل`);
      localStorage.setItem('currentDesign', String(id));
      window.location.href = "{{ route('dashboard') }}";
    }
    function deleteDesign(id){
      if(confirm(`هل تريد حذف التصميم ${id}؟`)){
        alert(`تم حذف التصميم ${id}`);
        // هنا نفذ طلب الحذف الفعلي من الخادم إن رغبت
        const el = document.querySelector(`.design-item[data-design-id="${id}"]`);
        if(el) el.remove();
      }
    }
  </script>

</body>
</html>
