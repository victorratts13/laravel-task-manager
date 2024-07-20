<?php

use App\Models\Queues;
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
        Schema::create('queues', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('memory');
            $table->boolean('status')->default(1);
            $table->integer('limit')->default(0);
            $table->timestamps();
        });

        Queues::create([
            'name' => 'task-manager',
            'memory' => 128,
            'status' => 1,
            'limit' => 0
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queues');
    }
};
