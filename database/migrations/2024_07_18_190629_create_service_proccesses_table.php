<?php

use App\Models\ServiceProccess;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('service_proccesses', function (Blueprint $table) {
            $table->id();
            /**
             * Enviroment ID
             */
            $table->integer('env');
            $table->boolean('status')->default(1);
            $table->integer('pid');
            $table->string('tag')->nullable();
            $table->string('uuid');
            $table->string('command');
            /**
             * command is restartable (kill command and start command)
             */
            $table->boolean('restartable')->default(0);
            /**
             * interval in seconds
             */
            $table->integer('interval');
            /**
             * Date/time from last execution
             */
            $table->timestamp('last_execution');
            /**
             * Output from command is 
             * loggable in database
             */
            $table->boolean('loggable')->default(1);
            $table->timestamps();
        });

        ServiceProccess::create([
            'env' => 1,
            'status' => 1,
            'pid' => 0,
            'tag' => "list-command",
            'uuid' => Str::uuid(),
            'restartable' => 0,
            'interval' => 60,
            'last_execution' => now(),
            'command' => "ls -la",
            'loggable' => 1
        ]);


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_proccesses');
    }
};
