<?php

namespace Database\Seeders;

use App\Models\Package;
use App\Models\Plan;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Stripe\Exception\ApiErrorException;
use Stripe\Product;
use Stripe\StripeClient;

/**
 * Class PlanSeeder
 * @package Database\Seeders
 */
class PackageSeeder extends Seeder
{
    private Collection $products;

    /**
     * @throws ApiErrorException
     */
    public function __construct()
    {
        if (App::environment() != 'testing') {
            $stripe = new StripeClient(config('services.stripe.secret'));
            $this->stripe = $stripe;
            $products = $stripe->products->all(['active' => true]);
            $this->products = collect($products->data)->map(function ($product) {
                $product->name = strtolower($product->name);
                return $product;
            });
        }
    }

    /**
     * @param $name
     * @return Product
     * @throws ApiErrorException
     */
    private function createOnStripe($name): Product
    {
        $stripe = $this->stripe;
        return $stripe->products->create([
            'name' => $name,
            'statement_descriptor' => "Stabiz $name",
        ]);
    }

    /**
     * Run this one in the testing env
     * Make sure to change the strip product id and plan id if they get changed
     * The reason why we don't use the actual one used in run method is because
     * that calls the stripe api on every test. That makes it very slow and
     * calls the api unnecessary. The actual one needs to run one time in an actual env
     * for the first time to populate the tables.
     */
    public function testingEnv()
    {
        $packages = [
            [
                'name' => Package::PREMIUM,
                'stripe_product_id' => 'prod_JtZ4axiFdUbV2n',
                'plans' => [
                    [
                        'interval' => 'month',
                        'price' => 100,
                        'currency' => 'jpy',
                        'stripe_plan_id' => 'plan_JtZ4n6U5yTbNbA'
                    ]
                ]
            ]
        ];

        foreach ($packages as $package) {
            $new = Package::create([
                'name' => $package['name'],
                'stripe_product_id' => $package['stripe_product_id'],
            ]);
            $plans = $package['plans'];
            foreach ($plans as $plan) {
                Plan::create([
                    'price' => 100,
                    'currency' => 'jpy',
                    'interval' => 'month',
                    'package_id' => $new->id,
                    'stripe_plan_id' => $plan['stripe_plan_id']
                ]);
            }
        }
    }

    /**
     * Run the database seeds.
     *
     * @return void
     * @throws ApiErrorException
     * @throws Exception
     */
    public function run()
    {
        if (App::environment(['testing'])) {
            $this->testingEnv();
            return;
        }

        // only run in an actual env for the first time when populating the tables.
        // don't run this in the testing env as it will call the api on each test
        $packages = [
            [
                'name' => Package::PREMIUM,
                'plans' => [
                    [
                        'interval' => 'month',
                        'price' => 100,
                        'currency' => 'jpy'
                    ]
                ]
            ]
        ];

        array_map(function ($packageArr) {
            $name = $packageArr['name'];
            $package = Package::where('name', 'ilike', $name)->first();
            if (!$package) {
                // products on stripe. Each product on stripe is basically a package name
                $products = $this->products;
                $product = $products->where('name', strtolower($name))->first();
                if (!$product) $product = $this->createOnStripe(ucfirst($name));

                $package = Package::create([
                    'name' => $name,
                    'stripe_product_id' => $product->id
                ]);
            }

            // add plans for the package/product (a plan is for example, monthly plan, yearly plan. You get the idea here)
            $stripeProduct = $package->stripe_product_id;
            if (is_null($stripeProduct)) throw new Exception('stripe product is missing from package');
            $plans = $packageArr['plans'];
            $stripe = $this->stripe;
            // stripe plans for this product
            $stripePlans = $stripe->plans->all(['product' => $stripeProduct]);
            $stripePlans = collect($stripePlans->data);
            foreach ($plans as $plan) {
                $exists = Plan::where($plan)->first();
                if ($exists) continue;
                $currency = strtolower($plan['currency']);
                // check if plan exists in the already existing plan, otherwise it will create duplicate every time
                $stripePlan = $stripePlans->where('amount', $plan['price'])
                    ->where('currency', $currency)
                    ->where('interval', $plan['interval'])
                    ->first();

                if (!$stripePlan) {
                    $stripePlan = $stripe->plans->create([
                        'amount' => $plan['price'],
                        'currency' => $currency,
                        'interval' => $plan['interval'],
                        'product' => $stripeProduct,
                    ]);
                }

                $plan = array_merge($plan, [
                    'package_id' => $package->id,
                    'stripe_plan_id' => $stripePlan->id
                ]);
                Plan::create($plan);
            }

        }, $packages);
    }
}
