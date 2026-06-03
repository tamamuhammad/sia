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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained(
                table: 'students',
                indexName: 'reports_student_id', 
            )->cascadeOnDelete();
            $table->string('achievement');
            $table->text('notes');
            $table->unsignedTinyInteger('month');
            $table->foreignId('period_id')->constrained(
                table: 'periods',
                indexName: 'reports_period_id', 
            )->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
