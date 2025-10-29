<?php

namespace Database\Seeders;

use App\Models\PaymentPlatform;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentPlatformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PaymentPlatform::create([
            'name'=>'Paypal',
            'image'=> 'img/payment-platforms/paypal.jpg'
        ]);
        PaymentPlatform::create([
            'name'=>'Stripe',
            'image'=> 'img/payment-platforms/stripe.jpg'
        ]);
        // DB::table('tb_produk')->insert([
        //     'name'=>'Paypal',
        //     'image'=> 'img/payment-platforms/paypal.jpg',
        //     'created_at'=> now(),
        // ],
        // [
        //     'name'=>'Stripe',
        //     'image'=> 'img/payment-platforms/stripe.jpg',
        //     'created_at'=> now(),
        // ]);

        // DB::table([
        //     'name'=>'Paypal',
        //     'image'=> 'img/payment-platforms/paypal.jpg'
        // ]);
        // DB::create([
        //     'name'=>'Stripe',
        //     'image'=> 'img/payment-platforms/stripe.jpg'
        // ]);
    }
}
