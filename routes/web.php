<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Admin\OrdersController;
use App\Http\Controllers\Admin\CustomersController;
use App\Http\Controllers\Admin\RevenueController;
use App\Http\Controllers\Admin\TshirtsController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\HomePageController;
use App\Http\Controllers\Customer\PaymentController;
use App\Http\Controllers\Customer\TshirtsController as CustomerTshirtsController;
use App\Http\Controllers\Customer\DesignerController;


// ################################ Customer Routes ################################
Route::get('/', [HomePageController::class, 'index'])->name('home');

Route::get('/privacy-policy', function () {
    return Inertia::render('Customer/PrivacyPolicy');
})->name('privacy-policy');

Route::get('/terms-of-use', function () {
    return Inertia::render('Customer/TermsOfUse');
})->name('terms-of-use');

Route::redirect('/t-shirt', '/');
Route::get('/t-shirt/{slug}', [CustomerTshirtsController::class, 'tshirtPage'])->name('tshirt.page');

Route::get('/cart', [CartController::class, 'cartPage'])->name('cart');
Route::post('/cart', [CartController::class, 'addToCart'])->name('cart.add');
Route::delete('/cart', [CartController::class, 'removeFromCart'])->name('cart.remove');
Route::post('/cart/increaseQuantity', [CartController::class, 'increaseQuantity'])->name('cart.increaseQuantity');
Route::post('/cart/decreaseQuantity', [CartController::class, 'decreaseQuantity'])->name('cart.decreaseQuantity');

Route::post('/cart/checkout', [PaymentController::class, 'checkout'])->name('cart.checkout');
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
    Route::put('orders/{order}', [\App\Http\Controllers\Admin\OrdersController::class, 'update'])->name('admin.orders.update');

    // Customers
    Route::get('customers', [\App\Http\Controllers\Admin\CustomersController::class, 'index'])->name('admin.customers.index');

    // Products
    Route::get('t-shirts', [\App\Http\Controllers\Admin\TshirtsController::class, 'index'])->name('admin.products.index');
    Route::post('t-shirts', [\App\Http\Controllers\Admin\TshirtsController::class, 'store'])->name('admin.products.store');
    Route::post('t-shirts/{tshirt}', [\App\Http\Controllers\Admin\TshirtsController::class, 'update'])->name('admin.products.update');
    Route::delete('t-shirts/{tshirt}', [\App\Http\Controllers\Admin\TshirtsController::class, 'destroy'])->name('admin.products.destroy');

    // Revenue
    Route::get('revenue', [\App\Http\Controllers\Admin\RevenueController::class, 'index'])->name('admin.revenue.index');
    
    // Roles
    Route::get('roles', [\App\Http\Controllers\Admin\RolesController::class, 'index'])->name('admin.roles.index');
    Route::post('roles', [\App\Http\Controllers\Admin\RolesController::class, 'store'])->name('admin.roles.store');
    Route::put('roles/{role}', [\App\Http\Controllers\Admin\RolesController::class, 'update'])->name('admin.roles.update');
    Route::delete('roles/{role}', [\App\Http\Controllers\Admin\RolesController::class, 'destroy'])->name('admin.roles.destroy');
    Route::patch('roles/{role}/toggle-status', [\App\Http\Controllers\Admin\RolesController::class, 'toggleStatus'])->name('admin.roles.toggle-status');
    
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
});
Route::redirect('/dashboard', '/admin')->name('dashboard');

// ############################### Designer Routes ###############################
Route::prefix('designer')->middleware('auth')->group(function () {
    Route::get('{productType}/{product?}', [DesignerController::class, 'create'])->name('designer.create');
    Route::get('edit/{design}', [DesignerController::class, 'edit'])->name('designer.edit');
    Route::get('my-designs', [DesignerController::class, 'myDesigns'])->name('designer.my-designs');
});

// ################################ Auth Routes ################################
require __DIR__ . '/auth.php';