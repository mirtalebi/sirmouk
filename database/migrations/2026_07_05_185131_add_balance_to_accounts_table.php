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
        Schema::table('accounts', function (Blueprint $table) {
            // Cache for fast balance queries: Sum(debit) - Sum(credit)
            if (!Schema::hasColumn('accounts', 'balance')) {
                $table->bigInteger('balance')->default(0)->comment('Cached balance: debit - credit');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            if (Schema::hasColumn('accounts', 'balance')) {
                $table->dropColumn('balance');
            }
        });
    }
};
