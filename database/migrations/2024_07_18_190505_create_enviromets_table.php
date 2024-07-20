<?php

use App\Models\Enviromet;
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
        Schema::create('enviromets', function (Blueprint $table) {
            $table->id();
            $table->integer('user');
            $table->string('path');
            $table->string('name');
            $table->boolean('status')->default(0);
            $table->integer('queue')->default(1);
            $table->timestamps();
        });

        Enviromet::create([
            'path' => base_path(),
            'user' => 1,
            'name' => "Default base path",
            'status' => 1
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enviromets');
    }
};
