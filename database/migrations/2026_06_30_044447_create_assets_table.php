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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code')->unique();
            $table->string('name');
            $table->string('category');
            $table->string('brand')->nullable();
            $table->year('year_acquired')->nullable();
            $table->string('location');
            $table->string('person_in_charge')->nullable();
            $table->boolean('is_in_use')->default(true);
            $table->enum('condition', ['baik', 'rusak', 'perlu perbaikan'])->default('baik');
            $table->timestamp('last_printed_at')->nullable();
            $table->string('photo_path')->nullable();
            $table->text('description')->nullable();
            $table->string('qr_code_path');
            $table->string('qr_target_url');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
