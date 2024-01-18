<?php

namespace Database\Seeders;

use App\Enums\OTPDigitLimitEnum;
use App\Enums\OTPExpireTimeEnum;
use App\Enums\OTPTypeEnum;
use Illuminate\Database\Seeder;
use Smartisan\Settings\Facades\Settings;

class OTPTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Settings::group('otp')->set([
            'otp_type'        => (string)OTPTypeEnum::BOTH,
            'otp_digit_limit' => (string)OTPDigitLimitEnum::FOUR,
            'otp_expire_time' => (string)OTPExpireTimeEnum::TEN,
        ]);
    }
}
