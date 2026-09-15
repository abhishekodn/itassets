<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['repair', 'service', 'inspection']);
            $table->text('description')->nullable();
            $table->decimal('cost', 12, 2)->default(0);
            $table->date('performed_at');
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('next_due_at')->nullable();
            $table->enum('status', ['completed', 'scheduled'])->default('completed');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_maintenances');
    }
};
