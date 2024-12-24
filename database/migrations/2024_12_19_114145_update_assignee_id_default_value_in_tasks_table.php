<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAssigneeIdDefaultValueInTasksTable extends Migration
{
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Modify the assignee_id column to set default value to 1
            $table->unsignedBigInteger('assignee_id')->default(1)->change();
        });
    }

    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Revert the assignee_id column to not have a default value
            $table->unsignedBigInteger('assignee_id')->nullable()->change();
        });
    }
}
