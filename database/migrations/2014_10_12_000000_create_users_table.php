<?php

use App\Enums\StatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('username')->unique();
            $table->string('phone')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('device_token')->nullable();
            $table->string('web_token')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable()->default(0);
            $table->boolean('is_guest')->default(false);
            $table->unsignedDecimal('balance', 13, 2)->default(0);
            $table->unsignedTinyInteger('status')->default(StatusEnum::ACTIVE);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
