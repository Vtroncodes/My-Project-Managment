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
    Schema::table('attachments', function (Blueprint $table) {
        $table->string('file_type', 255)->change(); // Adjust length as necessary
    });
}

    /**
     * Reverse the migrations.
     */
    public function down()
{
    Schema::table('attachments', function (Blueprint $table) {
        $table->string('file_type', 100)->change(); // Revert length if needed
    });
}
};
