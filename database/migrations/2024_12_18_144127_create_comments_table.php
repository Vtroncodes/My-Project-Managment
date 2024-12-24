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
    Schema::create('comments', function (Blueprint $table) {
        $table->id(); // Primary key
        $table->morphs('commentable'); // Polymorphic relation (commentable_type, commentable_id)
        $table->unsignedBigInteger('user_id'); // User who made the comment
        $table->text('content'); // Content of the comment
        $table->timestamps(); // Created and updated timestamps

        // Define foreign key constraint for user_id referencing the users table
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
