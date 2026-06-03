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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained(
                table: 'users',
                indexName: 'students_user_id', 
            )->cascadeOnDelete();
            $table->unsignedInteger('nis')->unique();
            $table->string('name');
            $table->string('birth_place');
            $table->date('birth_date');
            $table->string('phone', 15)->nullable();
            $table->string('guardian_name');
            $table->string('guardian_phone', 15)->nullable();
            $table->foreignId('group_id')->nullable()->constrained(
                table: 'groups',
                indexName: 'students_group_id', 
            )->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
