<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            DO $$ BEGIN
                CREATE TYPE notification_type AS ENUM ('transactional', 'marketing');
            EXCEPTION
                WHEN duplicate_object THEN NULL;
            END $$
        ");

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedTinyInteger('channel_id');
            $table->text('message');
            $table->string('idempotency_key', 64)->unique()->nullable();
            $table->timestamps();

            $table->foreign('channel_id')
                ->references('id')
                ->on('channels')
                ->restrictOnDelete();

            $table->index('channel_id');
            $table->index('created_at');
        });

        DB::statement('ALTER TABLE notifications ADD COLUMN type notification_type NOT NULL');
        DB::statement('CREATE INDEX notifications_type_idx ON notifications (type)');
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        DB::statement('DROP TYPE IF EXISTS notification_type');
    }
};
