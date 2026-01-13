<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->date('date')->index();
            $table->string('base', 3)->default('EUR');
            $table->string('currency', 3)->index();
            $table->decimal('rate', 16, 8);
            $table->timestamps();
            $table->unique(['date','currency']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
