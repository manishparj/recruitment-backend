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
        Schema::create('user_experience_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('experience_type', ['govt', 'private', 'temporary', 'deputation', 'ad_hoc', 'full_time', 'other']);
            $table->string('signatury_designation');
            $table->string('employer');
            $table->string('designation');
            $table->string('department');
            $table->string('nature_of_duties', 255);
            $table->date('from_date');
            $table->date('to_date')->nullable();
            $table->string('year_of_experience')->nullable();
            $table->boolean('presently_working')->defaultFalse();
            $table->string('document_path');
            $table->boolean('document_verified')->default(false);
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_experience_details');
    }
};
