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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');  // Reference to the user receiving the notification
            $table->string('title');
            $table->text('body');
            $table->boolean('is_read')->default(false);
            $table->string('type')->nullable();  // Type of the notification (e.g., task)
            $table->unsignedBigInteger('type_id')->nullable();  // Associated entity ID (e.g., task ID)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
