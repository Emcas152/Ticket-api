<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country', 2)->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->json('seat_map_config')->nullable();
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            $table->string('category')->nullable()->index();
            $table->string('image_url')->nullable();
            $table->enum('status', ['draft', 'published', 'cancelled', 'completed'])->default('draft')->index();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->json('map_config')->nullable();
            $table->timestamps();
        });

        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained()->cascadeOnDelete();
            $table->string('row_label')->nullable();
            $table->string('seat_number');
            $table->decimal('price', 8, 2)->default(0);
            $table->timestamps();

            $table->unique(['section_id', 'seat_number']);
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->uuid('reference')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['reserved', 'confirmed', 'cancelled', 'expired'])->default('reserved')->index();
            $table->decimal('total', 10, 2)->default(0);
            $table->timestamp('reserved_until')->nullable()->index();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });

        Schema::create('booking_seat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seat_id')->constrained()->cascadeOnDelete();
            $table->decimal('price_snapshot', 8, 2)->default(0);
            $table->timestamps();

            $table->unique(['booking_id', 'seat_id']);
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->string('provider');
            $table->string('provider_reference')->nullable()->unique();
            $table->decimal('amount', 10, 2)->default(0);
            $table->enum('status', ['pending', 'authorized', 'paid', 'failed', 'refunded'])->default('pending')->index();
            $table->json('payload')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seat_id')->constrained()->cascadeOnDelete();
            $table->string('qr_code')->unique();
            $table->string('pdf_path')->nullable();
            $table->enum('status', ['issued', 'used', 'cancelled'])->default('issued')->index();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->unique(['booking_id', 'seat_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('booking_seat');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('seats');
        Schema::dropIfExists('sections');
        Schema::dropIfExists('events');
        Schema::dropIfExists('venues');
    }
};
