<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Http\Controllers\Admin\OrdersController;
use App\Http\Controllers\Admin\CustomersController;
use App\Http\Controllers\Admin\RevenueController;
use App\Http\Controllers\Admin\ProductsController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\HomePageController;
use App\Http\Controllers\Customer\PaymentController;
use App\Http\Controllers\Customer\ProductsController as CustomerProductsController;
use App\Http\Controllers\Customer\DesignerController;
use App\Models\DesignTemplate;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;


// Home Page - Main Landing with 3D Product Showcase
Route::get('/', [HomePageController::class, 'index'])->name('home');

// Keep backward compatibility for legacy URLs
Route::redirect('/product', '/');
Route::redirect('/t-shirt', '/');

// Customer Dashboard Routes
Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/customer/dashboard', [CustomerDashboardController::class, 'index'])->name('customer.dashboard');
});

// Customer Login Route
Route::get('/customer/login', function () {
    return redirect()->route('login', ['customer' => true]);
})->name('customer.login');

Route::post('/locale', function (Request $request) {
    $locale = $request->input('locale');
    if (!in_array($locale, ['ar', 'en'])) {
        $locale = config('app.locale');
    }
    session()->put('locale', $locale);
    app()->setLocale($locale);
    return back();
})->name('locale.switch');

Route::get('/gallery/templates', function (Request $request) {
    $query = DesignTemplate::query()->with(['productType:id,slug,name']);

    if ($request->has('product_type')) {
        $query->where('product_type_id', $request->product_type);
    }

    if ($request->has('category')) {
        $query->where('category', $request->category);
    }

    if ($request->has('is_premium')) {
        $query->where('is_premium', $request->is_premium);
    }

    if ($request->has('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    if ($request->boolean('my_templates')) {
        if (auth()->check()) {
            $query->where('created_by', auth()->id());
        } else {
            $query->whereRaw('1 = 0');
        }
    }

    $templates = $query->orderBy('name')->paginate($request->get('per_page', 50));

    return response()->json([
        'data' => $templates->items(),
        'meta' => [
            'current_page' => $templates->currentPage(),
            'last_page' => $templates->lastPage(),
            'per_page' => $templates->perPage(),
            'total' => $templates->total(),
        ],
    ]);
})->name('gallery.templates');

Route::get('/privacy-policy', function () {
    return Inertia::render('Customer/PrivacyPolicy');
})->name('privacy-policy');

Route::get('/terms-of-use', function () {
    return Inertia::render('Customer/TermsOfUse');
})->name('terms-of-use');

Route::get('/about', function () {
    return Inertia::render('Customer/About');
})->name('about');

Route::get('/gallery', function () {
    return Inertia::render('Customer/Gallery');
})->name('gallery');

Route::get('/contact', function () {
    return Inertia::render('Customer/Contact');
})->name('contact');

Route::get('/faq', function () {
    return Inertia::render('Customer/FAQ');
})->name('faq');

Route::get('/shipping-policy', function () {
    return Inertia::render('Customer/ShippingPolicy');
})->name('shipping-policy');

Route::get('/returns', function () {
    return Inertia::render('Customer/Returns');
})->name('returns');

Route::get('/cookie-policy', function () {
    return Inertia::render('Customer/CookiePolicy');
})->name('cookie-policy');

// Product Routes - Enhanced 3D Experience
Route::redirect('/product', '/');

// Keep backward compatibility
Route::redirect('/t-shirt', '/');
Route::get('/t-shirt/{slug}', [CustomerProductsController::class, 'tshirtPage'])->name('tshirt.page');

// Generalized product routes with 3D viewer
Route::get('/products', [CustomerProductsController::class, 'index'])->name('products.index');
Route::get('/product/{slug}', [CustomerProductsController::class, 'tshirtPage'])->name('product.page');

Route::get('/cart', [CartController::class, 'cartPage'])->name('cart');
Route::post('/cart', [CartController::class, 'addToCart'])->name('cart.add');
Route::delete('/cart', [CartController::class, 'removeFromCart'])->name('cart.remove');
Route::post('/cart/increaseQuantity', [CartController::class, 'increaseQuantity'])->name('cart.increaseQuantity');
Route::post('/cart/decreaseQuantity', [CartController::class, 'decreaseQuantity'])->name('cart.decreaseQuantity');

Route::post('/cart/checkout', [PaymentController::class, 'checkout'])->name('cart.checkout');
Route::post('/cart/checkout-bank-transfer', [PaymentController::class, 'processBankTransfer'])->name('cart.bank.transfer');
Route::get('/thank-you', [PaymentController::class, 'thankYouPage'])->name('thankYou');
Route::get('/failed-payment', [PaymentController::class, 'failedPaymentPage'])->name('failedPayment');
Route::post('/webhook', [PaymentController::class, 'webhook'])->name('webhook');

// ############################### Admin Routes ###############################
Route::prefix('admin')->middleware(['auth', 'role:admin,staff'])->group(function () {
    // Dashboard
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('analytics', [\App\Http\Controllers\Admin\DashboardController::class, 'analytics'])->name('admin.analytics');

    
    // Users
    Route::get('users', [\App\Http\Controllers\Admin\UserManagementController::class, 'index'])->name('admin.users.index');
    Route::get('users/create', [\App\Http\Controllers\Admin\UserManagementController::class, 'create'])->name('admin.users.create');
    Route::post('users', [\App\Http\Controllers\Admin\UserManagementController::class, 'store'])->name('admin.users.store');
    Route::get('users/{user}', [\App\Http\Controllers\Admin\UserManagementController::class, 'show'])->name('admin.users.show');
    Route::get('users/{user}/edit', [\App\Http\Controllers\Admin\UserManagementController::class, 'edit'])->name('admin.users.edit');
    Route::put('users/{user}', [\App\Http\Controllers\Admin\UserManagementController::class, 'update'])->name('admin.users.update');
    Route::delete('users/{user}', [\App\Http\Controllers\Admin\UserManagementController::class, 'destroy'])->name('admin.users.destroy');
    Route::patch('users/{user}/toggle-status', [\App\Http\Controllers\Admin\UserManagementController::class, 'toggleStatus'])->name('admin.users.toggle-status');
    Route::post('users/bulk-assign-role', [\App\Http\Controllers\Admin\UserManagementController::class, 'bulkAssignRole'])->name('admin.users.bulk-assign-role');
    Route::post('users/bulk-remove-role', [\App\Http\Controllers\Admin\UserManagementController::class, 'bulkRemoveRole'])->name('admin.users.bulk-remove-role');
    
    // Designs
    Route::get('designs', [\App\Http\Controllers\Admin\DesignManagementController::class, 'index'])->name('admin.designs.index');
    Route::get('designs/{design}', [\App\Http\Controllers\Admin\DesignManagementController::class, 'show'])->name('admin.designs.show');
    Route::delete('designs/{design}', [\App\Http\Controllers\Admin\DesignManagementController::class, 'destroy'])->name('admin.designs.destroy');
    Route::patch('designs/{design}/toggle-visibility', [\App\Http\Controllers\Admin\DesignManagementController::class, 'toggleVisibility'])->name('admin.designs.toggle-visibility');
    Route::patch('designs/{design}/make-featured', [\App\Http\Controllers\Admin\DesignManagementController::class, 'makeFeatured'])->name('admin.designs.make-featured');
    
    // Orders
    Route::get('orders', [\App\Http\Controllers\Admin\OrdersController::class, 'index'])->name('admin.orders.index');
    Route::get('orders/{order}', [\App\Http\Controllers\Admin\OrdersController::class, 'show'])->name('admin.orders.show');
    Route::put('orders/{order}', [\App\Http\Controllers\Admin\OrdersController::class, 'update'])->name('admin.orders.update');
    Route::post('orders/{order}/upload-receipt', [\App\Http\Controllers\Admin\OrdersController::class, 'uploadReceipt'])->name('admin.orders.upload_receipt');

    // Customers
    Route::get('customers', [\App\Http\Controllers\Admin\CustomersController::class, 'index'])->name('admin.customers.index');

    // Products - Updated to be more generic
    Route::get('products', [\App\Http\Controllers\Admin\ProductsController::class, 'index'])->name('admin.products.index');
    Route::get('products/create', [\App\Http\Controllers\Admin\ProductsController::class, 'create'])->name('admin.products.create');
    Route::post('products', [\App\Http\Controllers\Admin\ProductsController::class, 'store'])->name('admin.products.store');
    Route::get('products/{product}', [\App\Http\Controllers\Admin\ProductsController::class, 'show'])->name('admin.products.show');
    Route::get('products/{product}/edit', [\App\Http\Controllers\Admin\ProductsController::class, 'edit'])->name('admin.products.edit');
    Route::match(['put', 'patch'], 'products/{product}', [\App\Http\Controllers\Admin\ProductsController::class, 'update'])->name('admin.products.update');
    Route::delete('products/{product}', [\App\Http\Controllers\Admin\ProductsController::class, 'destroy'])->name('admin.products.destroy');

    // Revenue
    Route::get('revenue', [\App\Http\Controllers\Admin\RevenueController::class, 'index'])->name('admin.revenue.index');
    
    // Roles
    Route::get('roles', [\App\Http\Controllers\Admin\RolesController::class, 'index'])->name('admin.roles.index');
    Route::post('roles', [\App\Http\Controllers\Admin\RolesController::class, 'store'])->name('admin.roles.store');
    Route::put('roles/{role}', [\App\Http\Controllers\Admin\RolesController::class, 'update'])->name('admin.roles.update');
    Route::delete('roles/{role}', [\App\Http\Controllers\Admin\RolesController::class, 'destroy'])->name('admin.roles.destroy');
    Route::patch('roles/{role}/toggle-status', [\App\Http\Controllers\Admin\RolesController::class, 'toggleStatus'])->name('admin.roles.toggle-status');
    Route::post('roles/{role}/grant-permissions', [\App\Http\Controllers\Admin\RolesController::class, 'grantPermissions'])->name('admin.roles.grant-permissions');
    Route::delete('roles/{role}/revoke-permission/{permission}', [\App\Http\Controllers\Admin\RolesController::class, 'revokePermission'])->name('admin.roles.revoke-permission');
    Route::delete('roles/{role}/revoke-all-permissions', [\App\Http\Controllers\Admin\RolesController::class, 'revokeAllPermissions'])->name('admin.roles.revoke-all-permissions');
    
    // Permissions
    Route::get('permissions', [\App\Http\Controllers\Admin\PermissionsController::class, 'index'])->name('admin.permissions.index');
    Route::post('permissions', [\App\Http\Controllers\Admin\PermissionsController::class, 'store'])->name('admin.permissions.store');
    Route::put('permissions/{permission}', [\App\Http\Controllers\Admin\PermissionsController::class, 'update'])->name('admin.permissions.update');
    Route::delete('permissions/{permission}', [\App\Http\Controllers\Admin\PermissionsController::class, 'destroy'])->name('admin.permissions.destroy');
    Route::patch('permissions/{permission}/toggle-status', [\App\Http\Controllers\Admin\PermissionsController::class, 'toggleStatus'])->name('admin.permissions.toggle-status');
    
    // Templates
    Route::get('templates', [\App\Http\Controllers\Admin\TemplatesController::class, 'index'])->name('admin.templates.index');
    Route::post('templates', [\App\Http\Controllers\Admin\TemplatesController::class, 'store'])->name('admin.templates.store');
    Route::delete('templates/{template}', [\App\Http\Controllers\Admin\TemplatesController::class, 'destroy'])->name('admin.templates.destroy');
    
    // Designer
    Route::get('designer/create', [\App\Http\Controllers\Admin\DesignManagementController::class, 'create'])->name('admin.designer.create');
    
    // Settings
    Route::get('settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('admin.settings.index');
    Route::put('settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('admin.settings.update');
});
Route::redirect('/dashboard', '/admin')->name('dashboard');

// ############################### Designer Routes ###############################
Route::prefix('designer')->group(function () {
    Route::get('select-product-type', [\App\Http\Controllers\Designer\DesignerController::class, 'selectProductType'])->name('designer.select-product-type');
    Route::get('my-designs', [\App\Http\Controllers\Designer\DesignerController::class, 'myDesigns'])->name('designer.my-designs');
    Route::get('edit/{design}', [\App\Http\Controllers\Designer\DesignerController::class, 'edit'])->name('designer.edit');
    
    // Template management routes
    Route::get('templates', function() {
        return \App\Http\Controllers\Api\TemplateController::class;
    })->name('designer.templates.api');
    
    Route::get('{productType}/{product?}', [\App\Http\Controllers\Designer\DesignerController::class, 'create'])->name('designer.create');
});

// ############################### Admin Designer Routes ###############################
Route::prefix('admin')->middleware(['auth', 'role:admin,staff'])->group(function () {
    Route::prefix('designer')->group(function () {
        Route::get('my-designs', function () {
            // Admin can view all designs
            $query = \App\Models\SavedDesign::with(['user', 'productType'])->latest();
            
            $designs = $query->paginate(20);
            
            return Inertia\Inertia::render('Customer/Designer/MyDesigns', [
                'designs' => $designs,
            ]);
        })->name('admin.designer.my-designs');
    });
});

// ################################ Auth Routes ################################
require __DIR__ . '/auth.php';

// Customer Profile Routes (for customer guard)
Route::middleware(['auth:web,customer'])->group(function () {
    Route::get('/customer/profile', [\App\Http\Controllers\Customer\ProfileController::class, 'edit'])->name('customer.profile.edit');
    Route::patch('/customer/profile', [\App\Http\Controllers\Customer\ProfileController::class, 'update'])->name('customer.profile.update');
});

// API route for getting CSRF token
Route::get('/api/csrf-token', function () {
    return response()->json(['token' => csrf_token()]);
})->middleware('web')->name('api.csrf.token');

// Saved Designs API Routes
Route::middleware(['auth:web,customer'])->group(function () {
    Route::post('/api/designs', [\App\Http\Controllers\Customer\SavedDesignController::class, 'store'])->name('api.designs.store');
    Route::put('/api/designs/{design}', [\App\Http\Controllers\Customer\SavedDesignController::class, 'update'])->name('api.designs.update');
    Route::delete('/api/designs/{design}', [\App\Http\Controllers\Customer\SavedDesignController::class, 'destroy'])->name('api.designs.destroy');
});
