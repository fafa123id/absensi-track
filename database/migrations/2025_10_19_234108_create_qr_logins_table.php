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
        Schema::create('qr_logins', function (Blueprint $table) {
            $table->string('id')->primary(); // Gunakan UUID sebagai ID
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('status')->default('pending');
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qr_logins');
    }
};
