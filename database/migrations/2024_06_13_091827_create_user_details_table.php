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
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->unique();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->enum('marital_status', ['single', 'married', 'widowed', 'divorced'])->nullable();
            $table->string('spouse_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('aadhaar_number')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->enum('user_category', ['general', 'ews', 'obc', 'sbc', 'sc', 'st', 'other'])->nullable();
            $table->enum('applied_category', ['general', 'ews', 'obc', 'sbc', 'sc', 'st', 'other'])->nullable();
            $table->boolean('pwd_category')->nullable();
            $table->enum('pwd_category_option', ['pwd_category_a', 'pwd_category_b', 'pwd_category_c', 'pwd_category_d', 'pwd_category_e'])->nullable();
            $table->boolean('ex_service_man')->nullable();
            $table->boolean('istype_speed_req')->nullable();
            $table->string('correspondence_address')->nullable();
            $table->string('permanent_address')->nullable();
            $table->boolean('addresses_are_same')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_details');
    }
};
