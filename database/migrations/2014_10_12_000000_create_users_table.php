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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->enum('role', ['manager', 'developer', 'client','admin'])->default('developer');  // Enum for roles
        });
    }
// $user = new \App\Models\User();
// $user->name = 'Admin User';
// $user->email = 'admin@domain.com';
// $user->password = bcrypt('12345');  // Make sure to hash the password 
// $user->role = 'admin';  // Assign the admin role
// $user->save();

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
