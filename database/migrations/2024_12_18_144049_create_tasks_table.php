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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->text('description');
            $table->unsignedBigInteger('project_id');
            $table->enum('status', ['to-do', 'in-progress', 'done']);
            $table->unsignedBigInteger('assignee_id');
            $table->enum('priority', ['low', 'medium', 'high']);
            $table->date('due_date');
            $table->timestamps();
            $table->unsignedBigInteger('file_attachment_id')->nullable();
    
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('assignee_id')->references('id')->on('users');
            $table->foreign('file_attachment_id')->references('id')->on('attachments')->onDelete('set null');
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
