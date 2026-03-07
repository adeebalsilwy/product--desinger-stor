<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Tshirt;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::all();
        $tshirts = Tshirt::all();
        $faker = \Faker\Factory::create();

        for ($i = 1; $i <= 88; $i++) {
            $customer = $customers->random();
            $numberOfItems = rand(1, 3);
            $selectedTshirts = $tshirts->random($numberOfItems);
            $date = $this->getRandomDate();

            // Generate order and payment status together
            $orderStatusData = $this->generateOrderStatusAndPayment();

            $order = Order::create([
                'customer_id' => $customer->id,
                'status' => $orderStatusData['status'],
                'tracking_number' => rand(10000000, 99999999),
                'payment_status' => $orderStatusData['payment_status'],
                'payment_id' => $orderStatusData['payment_id'],
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            foreach ($selectedTshirts as $tshirt) {
                $order->tshirts()->attach($tshirt->id, [
                    'size' => $faker->randomElement(['S', 'M', 'L', 'XL']),
                    'quantity' => rand(1, 2),
                    'price' => $tshirt->price,
                    'created_at' => $date,
                ]);
            }

            $order->total_tshirts = $order->getTotalTshirts();
            $order->total_amount = $order->getTotalAmount();
            $order->save();
        }
    }

    /**
     * Generate a random date for orders based on different time periods.
     */
    private function getRandomDate(): \Carbon\Carbon
    {
        $period = rand(1, 4);

        switch ($period) {
            case 1: // All Time (last 5 years)
                return now()->subYears(rand(1, 5))->subMonths(rand(0, 11))->subDays(rand(0, 30));
            case 2: // This Year (last 12 months)
                return now()->subMonths(rand(0, 11))->subDays(rand(0, 30));
            case 3: // This Month (last 30 days)
                return now()->subDays(rand(0, 30));
            case 4: // Last Year (specific to the previous calendar year)
                return now()->subYear()->subMonths(rand(0, 11))->subDays(rand(0, 30));
            default:
                return now();
        }
    }

    /**
     * Get a random order status.
     */
    private function generateOrderStatusAndPayment(): array
    {
        // Define logical combinations of order and payment statuses
        $statusCombinations = [
            // Pending orders can be paid or unpaid
            'pending' => [
                ['payment_status' => 'unpaid', 'payment_id' => null],
                ['payment_status' => 'paid', 'payment_id' => $this->generatePaymentId()]
            ],
            // Processing orders should typically be paid
            'processing' => [
                ['payment_status' => 'paid', 'payment_id' => $this->generatePaymentId()]
            ],
            // Delivered orders must be paid
            'delivered' => [
                ['payment_status' => 'paid', 'payment_id' => $this->generatePaymentId()]
            ],
            // Cancelled orders can be paid or unpaid
            'cancelled' => [
                ['payment_status' => 'unpaid', 'payment_id' => null],
                ['payment_status' => 'paid', 'payment_id' => $this->generatePaymentId()]
            ]
        ];

        // Select a random order status
        $status = array_rand($statusCombinations);

        // Select a random payment configuration for that status
        $paymentConfig = $statusCombinations[$status][array_rand($statusCombinations[$status])];

        return [
            'status' => $status,
            'payment_status' => $paymentConfig['payment_status'],
            'payment_id' => $paymentConfig['payment_id']
        ];
    }
    private function generatePaymentId(): ?string
    {
        // Only generate payment ID if the order is paid
        return 'cs_test_' . str()->random(24);
    }
}
