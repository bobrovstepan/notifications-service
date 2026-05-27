<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreignId('channel_id')->constrained('channels');
            $table->string('recipient');
            $table->string('message', 500);
            $table->enum('status', ['processing', 'sent', 'error'])->default('processing');
            $table->timestamps();

            $table->index(['user_id', 'status', 'channel_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
