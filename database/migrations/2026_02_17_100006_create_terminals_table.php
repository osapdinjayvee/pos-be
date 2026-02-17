<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('terminals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('license_id')->constrained()->cascadeOnDelete();
            $table->string('device_identifier');
            $table->string('device_name')->nullable();
            $table->string('device_type')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('activated_at');
            $table->timestamp('last_seen_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['license_id', 'device_identifier']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('terminals');
    }
};
