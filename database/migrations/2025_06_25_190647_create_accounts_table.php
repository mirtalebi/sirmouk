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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            
            // کد حسابداری (مثلا ۱ برای دارایی جاری، ۱۰۱۰۱ برای معین بانک)
            $table->string('code', 50)->nullable()->unique();
            
            // نام حساب (مثلا: درآمد فروش، مشتریان دفتری، بانک ملی)
            $table->string('name');
            
            // ماهیت استاندارد حساب در حسابداری کلان
            $table->enum('type', ['asset', 'liability', 'equity', 'revenue', 'expense']);
            
            // ارتباط درختی (پدر - فرزندی) برای پیاده‌سازی سطوح: گروه -> معین -> تفصیلی
            $table->foreignId('parent_id')
                ->nullable()
                ->comment('References id on accounts table for hierarchical structure')
                ->constrained('accounts')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            
            // رابطه Polymorphic (Sub-Ledger) برای متصل کردن حساب به مدل مشتری، تامین‌کننده، پیک و...
            $table->nullableMorphs('accountable');
            
            // فیلد برای ذخیره اطلاعات متفرقه یا تنظیمات خاص به صورت JSON
            $table->json('meta_data')->nullable();
            
            $table->timestamps();

            // ایجاد ایندکس ترکیبی روی فیلد مورفیک برای سرعت بالای کوئری‌ها
            $table->index(['accountable_type', 'accountable_id'], 'idx_accounts_sub_ledger');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
