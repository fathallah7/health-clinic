<?php

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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained()->onDelete('cascade');

            // Stripe specific fields
            $table->string('stripe_session_id')->nullable(); // Stripe Checkout Session

            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('usd'); // EUR, EGP, etc

            $table->enum('status', [
                'pending',
                'processing',
                'succeeded',
                'failed',
                'refunded',
                'canceled'
            ])->default('pending');

            $table->text('stripe_response')->nullable(); // Store full Stripe response for reference
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
