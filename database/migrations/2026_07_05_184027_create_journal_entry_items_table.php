<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_entry_items', function (Blueprint $table) {
            $table->id();
            
            // اتصال به هدر سند
            $table->foreignId('journal_entry_id')
                ->constrained('journal_entries')
                ->onDelete('cascade');
            
            // اتصال به جدول حساب‌ها (گروه، معین یا تفصیلی)
            $table->foreignId('account_id')
                ->constrained('accounts')
                ->onDelete('restrict');
            
            // فیلد کلیدی شما: اتصال مستقیم سطر مالی به مشتری (بدون ساخت تفصیلی جدید)
            // این فیلد برای مشتریان دفتری پر می‌شود و برای بقیه (مثل خرید برنج) NULL است
            $table->unsignedBigInteger('customer_id')->nullable();
            
            // مبالغ بدهکار و بستانکار
            $table->unsignedBigInteger('debit')->default(0);  // بدهکار
            $table->unsignedBigInteger('credit')->default(0); // بستانکار
            
            // شرح اختصاصی برای هر سطر (مثلاً: بابت فاکتور شماره ۱۲ آقای اکبری)
            $table->string('description')->nullable();

            $table->timestamps();

            // ایندکس‌ها برای سرعت فوق‌العاده بالا در گزارش‌گیری و محاسبه مانده حساب مشتری
            $table->index('customer_id');
            $table->index('account_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entry_items');
    }
};