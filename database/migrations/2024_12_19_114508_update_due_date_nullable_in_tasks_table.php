<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDueDateNullableInTasksTable extends Migration
{
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Modify the due_date column to allow NULL values
            $table->date('due_date')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Revert the due_date column to not allow NULL values
            $table->date('due_date')->nullable(false)->change();
        });
    }
}
