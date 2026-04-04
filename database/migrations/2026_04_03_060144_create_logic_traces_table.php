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
        $tableName = config('logic-as-data.tables.traces');
        $rulesTableName = config('logic-as-data.tables.rules');
        $telemetryTableName = config('logic-as-data.tables.telemetry');

        Schema::create($tableName, function (Blueprint $table) use ($rulesTableName, $telemetryTableName) {
            $table->id();

            $table->foreignId('logic_telemetry_id')
                ->constrained($telemetryTableName)
                ->cascadeOnDelete();

            $table->foreignId('logic_rule_id')
                ->nullable()
                ->constrained($rulesTableName)
                ->nullOnDelete();

            $table->string('status')->index();
            $table->text('error')->nullable();
            $table->float('duration', 8, 2)->nullable();

            $table->json('snapshot')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableName = config('logic-as-data.tables.traces');

        Schema::dropIfExists($tableName);
    }
};
