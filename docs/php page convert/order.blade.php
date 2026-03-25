<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
     <title>Document</title>
              <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #e0e5ec;
            --neumorph-light: #ffffff;
            --neumorph-dark: #a3b1c6;
            --accent-color: #4a00e0;
        }
        
        body {
            font-family: 'Tajawal', Arial, sans-serif;
            background: var(--primary-color);
            color: #333;
            margin: 0;
        }        
        .container {
            max-width: 350px;
            margin: 1px auto;
            padding: 20px;
            background: var(--primary-color);
            border-radius: 20px;
            box-shadow: 
                4px 4px 8px var(--neumorph-dark),
                -4px -4px 8px var(--neumorph-light);
        }
        
        h1 {
            color: var(--accent-color);
            text-align: center;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 10px;
        }
        
        label {
            display: block;
            margin-bottom: 4px;
            font-weight: bold;
        }
        
        input, textarea, select {
            width: 100%;
            padding: 8px 10px;
            border: none;
            border-radius: 10px;
            background: var(--primary-color);
            box-shadow: 
                inset 3px 3px 5px var(--neumorph-dark),
                inset -3px -3px 5px var(--neumorph-light);
            font-family: inherit;
        }
        
        textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .color-options {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .color-option {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid transparent;
            transition: transform 0.3s;
        }
        
        .color-option.selected {
            transform: scale(1.1);
            border-color: var(--accent-color);
        }
        
        .file-upload {
            position: relative;
            margin-top: 10px;
        }
        
        .file-upload input {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        .file-label {
            display: block;
            padding: 12px;
            background: var(--primary-color);
            border-radius: 10px;
            box-shadow: 
                3px 3px 5px var(--neumorph-dark),
                -3px -3px 5px var(--neumorph-light);
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .file-label:hover {
            box-shadow: 
                inset 3px 3px 5px var(--neumorph-dark),
                inset -3px -3px 5px var(--neumorph-light);
        }
        
        .file-name {
            margin-top: 5px;
            font-size: 14px;
            color: #666;
        }
        
        .submit-btn {
            background: var(--accent-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s;
            box-shadow: 
                3px 3px 5px var(--neumorph-dark),
                -3px -3px 5px var(--neumorph-light);
        }
        
        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 
                5px 5px 10px var(--neumorph-dark),
                -5px -5px 10px var(--neumorph-light);
        }
        
        .preview-image {
            max-width: 200px;
            max-height: 200px;
            margin-top: 15px;
            border-radius: 10px;
            display: none;
        }
    </style>
</head>
<body>
    
    <div class="container">
        <h1><i class="fas fa-pencil-ruler"></i> طلب تصميم جديد</h1>
        
        <form id="designForm">
            <div class="form-group">
                <label for="clientName">اسم العميل:</label>
                <input type="text" id="clientName" required placeholder="الرجاء إدخال الاسم الكامل">
            </div>
            
            <div class="form-group">
                <label>اختر اللون:</label>
                <div class="color-options">
                    <div class="color-option selected" style="background: #4a00e0;" data-color="#4a00e0"></div>
                    <div class="color-option" style="background: #ff6b6b;" data-color="#ff6b6b"></div>
                    <div class="color-option" style="background: #57c785;" data-color="#57c785"></div>
                    <div class="color-option" style="background: #f9ca24;" data-color="#f9ca24"></div>
                    <div class="color-option" style="background: #000000;" data-color="#000000"></div>
                    <div class="color-option" style="background: #ffffff; border: 1px solid #ddd;" data-color="#ffffff"></div>
                </div>
                <input type="hidden" id="selectedColor" name="color" value="#4a00e0">
            </div>
            
            <div class="form-group">
                <label for="size">المقاس:</label>
                <select id="size" name="size" required>
                    <option value="">-- اختر المقاس --</option>
                    <option value="S">Small (S)</option>
                    <option value="M">Medium (M)</option>
                    <option value="L">Large (L)</option>
                    <option value="XL">X-Large (XL)</option>
                    <option value="custom">مقاس مخصص</option>
                </select>
            </div>
            
            <div class="form-group" id="customSizeGroup" style="display: none;">
                <label for="customSize">المقاس المخصص (سم):</label>
                <input type="text" id="customSize" placeholder="الطول - العرض - الأكمام">
            </div>
            
            <div class="form-group">
                <label for="description">الوصف التفصيلي:</label>
                <textarea id="description" required placeholder="صف التصميم الذي تريده بالتفصيل..."></textarea>
            </div>
            
            <div class="form-group">
                <label>رفع صورة مرجعية (اختياري):</label>
                <div class="file-upload">
                    <label class="file-label">
                        <i class="fas fa-cloud-upload-alt"></i> انقر لرفع صورة
                        <input type="file" id="designImage" accept="image/*">
                    </label>
                    <div class="file-name" id="fileName"></div>
                    <img id="imagePreview" class="preview-image" alt="معاينة الصورة">
                </div>
            </div>
            
            <button type="submit" class="submit-btn">إرسال الطلب <i class="fas fa-paper-plane"></i></button>
        </form>
    </div>

    <script>
        // اختيار الألوان
        document.querySelectorAll('.color-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.color-option').forEach(opt => {
                    opt.classList.remove('selected');
                });
                this.classList.add('selected');
                document.getElementById('selectedColor').value = this.dataset.color;
            });
        });
        
        // إظهار/إخفاء حقل المقاس المخصص
        document.getElementById('size').addEventListener('change', function() {
            const customSizeGroup = document.getElementById('customSizeGroup');
            customSizeGroup.style.display = this.value === 'custom' ? 'block' : 'none';
        });
        
        // معاينة الصورة المرفوعة
        document.getElementById('designImage').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const fileName = document.getElementById('fileName');
            const preview = document.getElementById('imagePreview');
            
            if (file) {
                fileName.textContent = file.name;
                
                const reader = new FileReader();
                reader.onload = function(event) {
                    preview.src = event.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                fileName.textContent = '';
                preview.style.display = 'none';
            }
        });
        
        // إرسال النموذج
        document.getElementById('designForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // هنا يمكنك إضافة كود الإرسال إلى السيرفر
            const formData = {
                name: document.getElementById('clientName').value,
                color: document.getElementById('selectedColor').value,
                size: document.getElementById('size').value === 'custom' 
                    ? document.getElementById('customSize').value 
                    : document.getElementById('size').value,
                description: document.getElementById('description').value,
                image: document.getElementById('designImage').files[0]?.name || 'لا يوجد'
            };
            
            console.log('تم إرسال الطلب:', formData);
            alert('تم استلام طلبك بنجاح! سنتواصل معك قريباً.');
            this.reset();
            document.getElementById('imagePreview').style.display = 'none';
            document.getElementById('fileName').textContent = '';
        });
    </script>
</body>
</html>