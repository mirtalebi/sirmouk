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
        Schema::table('recipe_items', function (Blueprint $table) {
            if (! Schema::hasColumn('recipe_items', 'name')) {
                $table->string('name');
            }
            if (! Schema::hasColumn('recipe_items', 'value_type')) {
                $table->enum('value_type', ['static', 'dynamic'])->default('static')->after('name');
            }
            if (! Schema::hasColumn('recipe_items', 'value')) {
                $table->decimal('value', 15, 2)->default(0)->after('value_type');
            }
            if (! Schema::hasColumn('recipe_items', 'components')) {
                $table->json('components')->nullable()->after('value');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recipe_items', function (Blueprint $table) {
            if (Schema::hasColumn('recipe_items', 'components')) {
                $table->dropColumn('components');
            }
            if (Schema::hasColumn('recipe_items', 'value')) {
                $table->dropColumn('value');
            }
            if (Schema::hasColumn('recipe_items', 'value_type')) {
                $table->dropColumn('value_type');
            }
            if (Schema::hasColumn('recipe_items', 'name')) {
                $table->dropColumn('name');
            }
        });
    }
};
