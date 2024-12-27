<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveFileAttachmentIdFromProjectsAndTasks extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Drop the foreign key constraint if it exists
            $table->dropForeign(['file_attachment_id']);
            // Drop the column
            $table->dropColumn('file_attachment_id');
        });

        Schema::table('tasks', function (Blueprint $table) {
            // Drop the foreign key constraint if it exists
            $table->dropForeign(['file_attachment_id']);
            // Drop the column
            $table->dropColumn('file_attachment_id');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Recreate the column
            $table->unsignedBigInteger('file_attachment_id')->nullable();
            // Recreate the foreign key constraint
            $table->foreign('file_attachment_id')->references('id')->on('attachments')->onDelete('cascade');
        });

        Schema::table('tasks', function (Blueprint $table) {
            // Recreate the column
            $table->unsignedBigInteger('file_attachment_id')->nullable();
            // Recreate the foreign key constraint
            $table->foreign('file_attachment_id')->references('id')->on('attachments')->onDelete('cascade');
        });
    }
}

