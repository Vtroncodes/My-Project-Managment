<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_name');
            $table->text('description');
            $table->unsignedBigInteger('file_attachment_id')->nullable();
            $table->unsignedBigInteger('owner_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['pending', 'in-progress', 'completed', 'new'])->default('new');
            $table->timestamps();
            $table->string('email_url')->nullable();
    
            // Foreign key constraints
            $table->foreign('file_attachment_id')->references('id')->on('attachments')->onDelete('set null');
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
