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
        Schema::create('service_proccesses', function (Blueprint $table) {
            $table->id();
            $table->integer('env');
            $table->integer('pid');
            $table->string('tag');
            $table->string('uuid');
            $table->string('command');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_proccesses');
    }
};
