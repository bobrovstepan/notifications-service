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
                CREATE TYPE notification_status AS ENUM ('queued', 'sent', 'delivered', 'discarded');
            EXCEPTION
                WHEN duplicate_object THEN NULL;
            END $$
        ");

        Schema::create('notification_recipients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('notification_id');
            $table->string('subscriber_id', 64);
            $table->text('failure_reason')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();

            $table->foreign('notification_id')
                ->references('id')
                ->on('notifications')
                ->cascadeOnDelete();

            $table->index('subscriber_id');
            $table->unique(['notification_id', 'subscriber_id']);
        });

        DB::statement("ALTER TABLE notification_recipients ADD COLUMN status notification_status NOT NULL DEFAULT 'queued'");

        DB::statement('CREATE INDEX notification_recipients_notification_id_status_index ON notification_recipients (notification_id, status)');
        DB::statement('CREATE INDEX notification_recipients_subscriber_id_status_index ON notification_recipients (subscriber_id, status)');
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_recipients');
        DB::statement('DROP TYPE IF EXISTS notification_status');
    }
};
