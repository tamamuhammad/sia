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
        Schema::create('presences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained(
                table: 'students',
                indexName: 'presences_student_id', 
            )->cascadeOnDelete();
            $table->date('presence_date');
            $table->foreignId('period_id')->constrained(
                table: 'periods',
                indexName: 'presences_period_id', 
            )->cascadeOnDelete();
            $table->enum('status', ['Hadir', 'Alfa', 'Sakit', 'Izin']);
            $table->foreignId('group_id')->constrained(
                table: 'groups',
                indexName: 'presences_group_id', 
            )->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presences');
    }
};
