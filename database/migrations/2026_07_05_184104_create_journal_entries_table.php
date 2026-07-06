<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            
            // تاریخ ثبت سند (می‌تواند تاریخ روز یا تاریخ دستی حسابدار باشد)
            $table->date('entry_date');
            
            // شرح کلی سند (مثلاً: ثبت فروش تجمیعی مورخ ۱۴۰۵/۰۴/۱۴)
            $table->string('description')->nullable();
            
            // وضعیت سند: draft (موقت و قابل تجمیع) یا posted (قطعی و نهایی شده)
            $table->enum('status', ['draft', 'posted'])->default('draft');
            
            // برای اتصال سند به فاکتورها یا اسناد سیستمی دیگر در صورت نیاز
            $table->nullableMorphs('reference'); 

            $table->timestamps();
            
            // ایندکس برای گزارش‌گیری سریع بر اساس تاریخ و وضعیت
            $table->index(['entry_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};