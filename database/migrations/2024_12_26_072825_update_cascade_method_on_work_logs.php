<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCascadeMethodOnWorkLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_logs', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['task_id']);

            // Recreate the foreign key with cascading delete
            $table->foreign('task_id')
                  ->references('id')
                  ->on('tasks')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('work_logs', function (Blueprint $table) {
            // Drop the cascading delete foreign key
            $table->dropForeign(['task_id']);

            // Recreate the original foreign key without cascading delete
            $table->foreign('task_id')
                  ->references('id')
                  ->on('tasks');
        });
    }
}
