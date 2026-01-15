<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->decimal('monthly_budget', 14, 2)->nullable()->comment('Monthly budget limit for expenses');
            $table->string('budget_currency', 3)->default('CZK')->comment('Currency for monthly budget');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['monthly_budget', 'budget_currency']);
        });
    }
};
