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
        Schema::create('user_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('document_type', ['photo', 'signature', 'category', 'payment','secondary','sr_secondary','graduation','post_graduation','phd','govt','private','temporary','deputation','ad_hoc','full_time','other','iti']);
            $table->enum('document_group', ['identity', 'payment', 'education', 'experience']);
            $table->string('document_path');
            $table->boolean('document_verified')->default(false);
            $table->timestamps();

            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_documents');
    }
};
