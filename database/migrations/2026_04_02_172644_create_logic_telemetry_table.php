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
        $tableName = config('logic-as-data.tables.telemetry');

        Schema::create($tableName, function (Blueprint $table) {
            $table->id();

            $table->string('hook')->index();

            $table->string('session_id')->nullable()->index();
            $table->string('request_id')->index();
            $table->nullableMorphs('causer');

            $table->json('subjects')->nullable();
            $table->json('context')->nullable();

            // Total time(in milliseconds) took for the execution of a whole hook
            $table->float('total_duration', 8, 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableName = config('logic-as-data.tables.telemetry');

        Schema::dropIfExists($tableName);
    }
};
