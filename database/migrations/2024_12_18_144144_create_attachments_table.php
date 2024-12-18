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
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->string('attachmentable_type');
            $table->unsignedBigInteger('attachmentable_id');
            $table->string('file_url');
            $table->enum('file_type', ['pdf', 'jpg', 'png', 'jpeg', 'xlsx', 'docs']);
            $table->timestamps();
    
            $table->index(['attachmentable_type', 'attachmentable_id']);
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
