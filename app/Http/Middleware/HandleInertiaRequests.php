<?php

namespace App\Http\Middleware;

use App\Models\Customer;
use App\Models\Order;
use App\Models\ProductType;
use App\Models\Tshirt;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Number;
use Inertia\Middleware;


class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $cart = session()->get('cart', []);
        $settings = Setting::getSettings();
        $locale = session('locale', config('app.locale'));
        app()->setLocale($locale);
        $defaultProductType = ProductType::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->value('slug') ?? 't-shirt';
        $translations = [];
        $webUser = Auth::user();
        $customerUser = auth('customer')->user();
        $authUser = $webUser ?: $customerUser;
        $authGuard = $webUser ? 'web' : ($customerUser ? 'customer' : null);

        if (file_exists(resource_path("lang/{$locale}/customer.php"))) {
            $translations = Lang::get('customer');
        }
        $shared = [
            ...parent::share($request),
            'auth' => $authUser ? [
                'user' => $authUser,
                'guard' => $authGuard,
            ] : null,
            'settings' => $settings,
            'cart' => $cart,
            'locale' => $locale,
            'translations' => $translations,
            'defaultProductType' => $defaultProductType,
        ];

        if (Auth::check()) {
            $shared['orders_count'] = Order::count();
            $shared['customers_count'] = Customer::count();
            $shared['products_count'] = Tshirt::count();
            $shared['revenue'] = Number::currency(
                Order::where('payment_status', '=', 'paid')
                    ->where('status', '!=', 'cancelled')
                    ->with('tshirts')
                    ->get()
                    ->sum(function ($order) {
                        return $order->getTotalAmount();
                    })
            );
        }

        return $shared;
    }
}
