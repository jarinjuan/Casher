<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('investment_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investment_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 18, 8);
            $table->string('currency', 3)->default('USD');
            $table->timestamp('recorded_at')->index();
            $table->string('source')->nullable();
            $table->timestamps();

            $table->index(['investment_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investment_prices');
    }
};
