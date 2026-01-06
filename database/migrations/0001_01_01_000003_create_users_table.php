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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('reset_token')->nullable();
            $table->unsignedBigInteger('vacancy_id')->nullable();
            $table->unsignedBigInteger('post_id')->nullable();
            $table->enum('role', ['admin', 'applicant'])->default('applicant');
            $table->boolean('enabled')->default(true);
            $table->enum('registration_status', ['draft', 'submitted', 'final'])->default('draft');
            $table->rememberToken();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('vacancy_id')->references('id')->on('vacancies')->onDelete('cascade');
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');

            // Composite unique index
            $table->unique(['vacancy_id', 'post_id', 'email']);
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('user_id')->primary();
            $table->string('email');
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
