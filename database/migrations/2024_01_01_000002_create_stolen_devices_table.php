<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stolen_devices', function (Blueprint $table) {
            $table->id();
            $table->string('imei', 15)->unique();
            $table->string('serial_number', 50)->nullable();
            $table->string('brand', 50);
            $table->string('model', 100);
            $table->string('color', 30)->nullable();
            $table->enum('status', ['missing', 'reported', 'investigating', 'found', 'recovered'])->default('missing');
            $table->date('lost_date');
            $table->text('description')->nullable();
            $table->string('loss_location')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->foreignId('reported_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('found_at')->nullable();
            $table->text('notes')->nullable();
            $table->json('images')->nullable();
            $table->decimal('reward_amount', 10, 2)->nullable();
            $table->text('contact_info')->nullable();
            $table->tinyInteger('priority')->default(1); // 1=Low, 2=Medium, 3=High, 4=Critical
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['imei']);
            $table->index(['status', 'created_at']);
            $table->index(['brand', 'model']);
            $table->index(['priority', 'status']);
            $table->index(['reported_by', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stolen_devices');
    }
};
