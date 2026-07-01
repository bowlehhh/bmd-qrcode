<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->index('created_at', 'assets_created_at_idx');
            $table->index('condition', 'assets_condition_idx');
            $table->index('location', 'assets_location_idx');
            $table->index('last_printed_at', 'assets_last_printed_at_idx');
            $table->index('register_number', 'assets_register_number_idx');
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->index('created_at', 'activity_logs_created_at_idx');
            $table->index('action', 'activity_logs_action_idx');
            $table->index(['subject_type', 'subject_id'], 'activity_logs_subject_idx');
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropIndex('assets_created_at_idx');
            $table->dropIndex('assets_condition_idx');
            $table->dropIndex('assets_location_idx');
            $table->dropIndex('assets_last_printed_at_idx');
            $table->dropIndex('assets_register_number_idx');
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex('activity_logs_created_at_idx');
            $table->dropIndex('activity_logs_action_idx');
            $table->dropIndex('activity_logs_subject_idx');
        });
    }
};
