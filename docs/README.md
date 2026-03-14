هذا إصدار **README** مُعاد صياغته ليكون **تعليمات تشغيل المشروع بالكامل** بدل الملخص الحالي، وبصياغة مناسبة لمشروع **Laravel + Node.js + Vue.js**، مع الإشارة إلى أن المطور هو **أديب الصلوي**.

يمكنك نسخه مباشرةً ووضعه مكان ملف `README.md` الحالي:

---

# Ahlam's Girls - تشغيل المشروع بالكامل

مشروع **Ahlam's Girls** مبني باستخدام **Laravel 11 + Vue 3 + Inertia.js + Vite + Tailwind CSS**، ويعتمد على **MySQL** كقاعدة بيانات. ملفات المشروع تُظهر أن الواجهة الأمامية تستخدم Vue 3 وVite، وأن التشغيل المحلي يمكن أن يتم عبر `npm run dev` أو عبر أمر `composer dev` الذي يشغّل السيرفر والـ queue والـ logs والـ Vite معًا. ([GitHub][1])

## معلومات المشروع

* **اسم المشروع:** Ahlam's Girls
* **التقنيات:** Laravel 11, PHP 8.2+, Vue 3, Inertia.js, Vite, Tailwind CSS, MySQL. ([GitHub][1])
* **المطور:** **أديب الصلوي**
* **المستودع:** مشروع Laravel/Vue يحتوي على ملفات `artisan`, `composer.json`, `package.json`, `vite.config.js`, و`tailwind.config.js`. ([GitHub][2])

---

## المتطلبات

قبل تشغيل المشروع، تأكد من توفر ما يلي على جهازك:

* **PHP 8.2 أو أحدث**. ملف `composer.json` يطلب `php: ^8.2`. ([GitHub][3])
* **Composer**
* **Node.js + npm**
* **MySQL**
* يفضّل تفعيل إضافات PHP المعتادة لمشاريع Laravel مثل:

  * `pdo_mysql`
  * `mbstring`
  * `openssl`
  * `tokenizer`
  * `xml`
  * `ctype`
  * `json`
  * `fileinfo`

---

## استنساخ المشروع

```bash
git clone https://github.com/adeebalsilwy/product--desinger-stor.git
cd product--desinger-stor
```

---

## تثبيت الحزم

### 1) تثبيت حزم PHP

```bash
composer install
```

يعتمد المشروع على Laravel 11 وحزم مثل Inertia Laravel وLaravel Breeze وSanctum وCashier وZiggy وغيرها. ([GitHub][3])

### 2) تثبيت حزم Node.js

```bash
npm install
```

الواجهة الأمامية تعتمد على Vue 3 وVite وTailwind CSS وInertia.js، بالإضافة إلى مكتبات مثل PrimeVue وApexCharts وKonva وFabric وThree.js. ([GitHub][1])

---

## إعداد ملف البيئة

انسخ ملف البيئة:

```bash
cp .env.example .env
```

في Windows PowerShell:

```powershell
copy .env.example .env
```

ملف `.env.example` يوضح أن الإعداد الافتراضي يستخدم:

* `APP_URL=http://localhost`
* `DB_CONNECTION=mysql`
* `DB_HOST=127.0.0.1`
* `DB_PORT=3306`
* `DB_DATABASE=d_shirts`
* `DB_USERNAME=root`
* `DB_PASSWORD=`
  كما أن `SESSION_DRIVER=database` و`QUEUE_CONNECTION=database` و`CACHE_STORE=database`. ([GitHub][4])

---

## توليد مفتاح التطبيق

```bash
php artisan key:generate
```

يوجد في `composer.json` ما يشير إلى أن Laravel يستخدم هذا الأمر أثناء إعداد المشروع الجديد أيضًا. ([GitHub][3])

---

## إعداد قاعدة البيانات

أنشئ قاعدة بيانات جديدة في MySQL باسم:

```sql
CREATE DATABASE d_shirts;
```

هذا الاسم هو الافتراضي الموجود داخل `.env.example`. ويمكنك تغييره إذا رغبت، بشرط تعديل القيم داخل ملف `.env`. ([GitHub][4])

ثم عدّل بيانات الاتصال داخل `.env` حسب بيئتك:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=d_shirts
DB_USERNAME=root
DB_PASSWORD=
```

---

## تنفيذ الجداول والبيانات الأولية

بما أن المشروع يستخدم الجداول الخاصة بالجلسات والـ cache والـ queue على قاعدة البيانات، فمن المهم تنفيذ الترحيلات كاملة: ([GitHub][4])

```bash
php artisan migrate
```

وإذا كان المشروع يحتوي على seeders وتريد تعبئة البيانات الأولية:

```bash
php artisan db:seed
```

أو:

```bash
php artisan migrate --seed
```

---

## ربط مجلد التخزين

إذا كان المشروع يعتمد على ملفات مرفوعة وصور داخل `storage/app/public`، فأنشئ الرابط الرمزي التالي:

```bash
php artisan storage:link
```

وجود المسار `storage/app/public/brands/` ضمن بنية المشروع يدل على أن الأصول الخاصة بالعلامة التجارية مخزنة هناك. ([GitHub][2])

---

## تشغيل المشروع

### الطريقة 1: تشغيل كل شيء بأمر واحد

أفضل طريقة أثناء التطوير:

```bash
composer run dev
```

أمر `dev` الموجود داخل `composer.json` يشغّل معًا:

* `php artisan serve`
* `php artisan queue:listen --tries=1`
* `php artisan pail --timeout=0`
* `npm run dev`
  وذلك باستخدام `concurrently`. ([GitHub][3])

### الطريقة 2: تشغيل Laravel وVite يدويًا

افتح نافذتين في الطرفية.

#### النافذة الأولى:

```bash
php artisan serve
```

#### النافذة الثانية:

```bash
npm run dev
```

ملف `package.json` يعرّف:

* `npm run dev` لتشغيل Vite
* `npm run build` لبناء ملفات الإنتاج. ([GitHub][1])

### الطريقة 3: استخدام سكربتات التشغيل الجاهزة

#### Linux / macOS:

```bash
chmod +x start-dev-servers.sh
./start-dev-servers.sh
```

هذا السكربت يشغّل:

* Laravel على `http://127.0.0.1:8000`
* Vite على `http://127.0.0.1:5173`
  ويكتب السجلات في `laravel.log` و`vite.log`. ([GitHub][5])

#### Windows PowerShell:

```powershell
powershell -ExecutionPolicy Bypass -File .\start-dev-servers.ps1
```

السكريبت الخاص بـ PowerShell يشغّل Laravel وVite ويكتب السجلات أيضًا في ملفات log. ([GitHub][6])

---

## الوصول إلى المشروع

بعد التشغيل المحلي، غالبًا ستستخدم:

* **Laravel App:** `http://127.0.0.1:8000`
* **Vite Dev Server:** `http://127.0.0.1:5173` عند التطوير المحلي عبر Vite. ([GitHub][5])

إذا كنت تستخدم `php artisan serve` يدويًا ولم تغيّر المنفذ، فسيعمل التطبيق أيضًا على:

* `http://127.0.0.1:8000`

---

## أوامر مفيدة أثناء التطوير

### تنظيف الكاش

```bash
php artisan optimize:clear
```

### إعادة بناء الواجهة للإنتاج

```bash
npm run build
```

### تشغيل الـ queue worker يدويًا

إذا لم تستخدم `composer run dev`:

```bash
php artisan queue:listen --tries=1
```

### متابعة السجلات

إذا كان لديك Laravel Pail:

```bash
php artisan pail --timeout=0
```

أمر `composer run dev` يشغّل queue وpail تلقائيًا. ([GitHub][3])

---

## مشاكل شائعة وحلولها

### 1) خطأ في الاتصال بقاعدة البيانات

تأكد من:

* تشغيل MySQL
* صحة اسم القاعدة
* صحة اسم المستخدم وكلمة المرور في `.env`
  القيم الافتراضية في المشروع تشير إلى قاعدة باسم `d_shirts` على `127.0.0.1:3306`. ([GitHub][4])

### 2) الأصول أو التصميم لا يظهران

تأكد من تشغيل:

```bash
npm install
npm run dev
```

لأن المشروع يستخدم Vite وVue 3 وTailwind. ([GitHub][1])

### 3) خطأ APP_KEY

نفّذ:

```bash
php artisan key:generate
```

### 4) الصور أو الملفات المرفوعة لا تعمل

نفّذ:

```bash
php artisan storage:link
```

### 5) مشاكل في الجلسات أو الكيو أو الكاش

بما أن المشروع مضبوط على استخدام قاعدة البيانات لهذه الخدمات، تأكد من تنفيذ جميع الـ migrations. إعدادات `.env.example` توضح:

* `SESSION_DRIVER=database`
* `QUEUE_CONNECTION=database`
* `CACHE_STORE=database` ([GitHub][4])

---

## بناء المشروع للإنتاج

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

ثم تأكد من ضبط:

* `APP_ENV=production`
* `APP_DEBUG=false`

---

## تقنيات المشروع

### Backend

* Laravel Framework `^11.9`
* Inertia Laravel
* Laravel Breeze
* Laravel Sanctum
* Laravel Cashier
* Intervention Image
* Ziggy ([GitHub][3])

### Frontend

* Vue `^3.4.0`
* Vite `^5.0`
* Tailwind CSS `^3.2.1`
* Inertia Vue 3
* PrimeVue
* ApexCharts
* Fabric / Konva / Three.js ([GitHub][1])

---

## ملاحظات مهمة

* المشروع يحتوي على مجلدات منظمة مثل:

  * `docs/`
  * `scripts/`
  * `assets/`
  * `tools/`
  * `storage/`
  * `resources/`
  * `routes/`
  * `database/`
    وهذا ظاهر من بنية المستودع الحالية. ([GitHub][2])
* اسم قاعدة البيانات الافتراضي ما يزال يشير إلى `d_shirts` داخل ملف البيئة، لذلك يُفضّل مراجعته إذا كنت تريد اسمًا يتوافق مع هوية **Ahlam's Girls**. ([GitHub][4])

---

## المطور

**أديب الصلوي**
Repository: `adeebalsilwy/product--desinger-stor` ([GitHub][2])

---

## أمر التشغيل السريع

بعد الاستنساخ، هذه أقل مجموعة أوامر لتشغيل المشروع:

```bash
git clone https://github.com/adeebalsilwy/product--desinger-stor.git
cd product--desinger-stor
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
composer run dev
```

---

إذا رغبت، أقدر أيضًا أعطيك **نسخة README نهائية منسقة بالكامل بستايل GitHub Markdown** ومختصرة وجاهزة للّصق مباشرة دون أي شرح إضافي.

[1]: https://raw.githubusercontent.com/adeebalsilwy/product--desinger-stor/main/package.json "raw.githubusercontent.com"
[2]: https://github.com/adeebalsilwy/product--desinger-stor/ "GitHub - adeebalsilwy/product--desinger-stor · GitHub"
[3]: https://raw.githubusercontent.com/adeebalsilwy/product--desinger-stor/main/composer.json "raw.githubusercontent.com"
[4]: https://raw.githubusercontent.com/adeebalsilwy/product--desinger-stor/main/.env.example "raw.githubusercontent.com"
[5]: https://raw.githubusercontent.com/adeebalsilwy/product--desinger-stor/main/start-dev-servers.sh "raw.githubusercontent.com"
[6]: https://raw.githubusercontent.com/adeebalsilwy/product--desinger-stor/main/start-dev-servers.ps1 "raw.githubusercontent.com"
