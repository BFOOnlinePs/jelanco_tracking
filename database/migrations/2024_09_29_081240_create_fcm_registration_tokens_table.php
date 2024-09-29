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
        Schema::create('fcm_registration_tokens', function (Blueprint $table) {
            $table->increments('frt_id');
            $table->integer('frt_user_id');
            $table->text('frt_registration_token');
            $table->date('frt_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fcm_registration_tokens');
    }
};
