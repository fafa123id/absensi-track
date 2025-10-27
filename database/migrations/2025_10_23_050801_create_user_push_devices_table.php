<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_user_push_devices_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('user_push_devices', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('push_subscription_id');
      $table->unsignedBigInteger('user_id');
      $table->string('device_name')->nullable();
      $table->string('device_id')->nullable();
      $table->string('ip')->nullable();
      $table->timestamps();

      $table->foreign('push_subscription_id')->references('id')->on('push_subscriptions')->onDelete('cascade');
      $table->index(['user_id', 'device_id']);
    });
  }
  public function down(): void { Schema::dropIfExists('user_push_devices'); }
};

