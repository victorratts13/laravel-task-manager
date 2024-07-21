<?php

use App\Console\Commands\updater as CommandsUpdater;
use App\Http\Controllers\BackEndController;
use App\Models\Updater;
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
        Schema::create('updaters', function (Blueprint $table) {
            $table->id();
            $table->string('version');
            $table->string('node');
            $table->string('repository');
            $table->string('hash');
            $table->string('url');
            $table->timestamps();
        });

        $last = BackEndController::GetLastVersion(CommandsUpdater::$repository);

        Updater::create([
            'version' => $last->tag_name,
            'node' => $last->node_id,
            'repository' => $last->html_url,
            'hash' => sha1($last->node_id),
            'url' => $last->zipball_url
        ]);
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('updaters');
    }
};
