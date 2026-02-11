<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['stock', 'crypto']);
            $table->string('symbol');
            $table->string('name')->nullable();
            $table->string('external_id')->nullable();
            $table->decimal('quantity', 18, 8)->default(0);
            $table->decimal('average_price', 18, 8)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->timestamps();

            $table->index(['team_id', 'type']);
            $table->unique(['team_id', 'type', 'symbol']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investments');
    }
};
