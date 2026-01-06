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
        Schema::create('user_educational_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('exam_name', ['secondary', 'sr_secondary', 'graduation', 'post_graduation', 'phd', 'other','iti']);
            $table->string('subject_names');
            $table->string('institute_name');
            $table->string('roll_number');
            $table->enum('result_type', ['percentage', 'cgpa', 'grade']);
            $table->string('result')->nullable();
            $table->string('year_of_passed');
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_educational_details');
    }
};
