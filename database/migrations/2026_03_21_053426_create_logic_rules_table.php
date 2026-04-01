<?php

use Illuminate\Database\Migrations\Migration;
use LaraWave\LogicAsData\Enums\RuleStatus;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tableName = config('logic-as-data.table_name', 'logic_rules');

        Schema::create($tableName, function (Blueprint $table) {
            $table->id();

            // Human-readable identifier for the rule (e.g., "Black Friday VIP Discount")
            $table->string('name');

            // Specific event or location in the app this rule attaches to (e.g., "cart.checkout")
            $table->string('hook')->index();

            // The JSON object containing both the 'predicate' (AST conditions) and 'actions'
            $table->json('definition');

            // Execution order when multiple rules match the same hook (higher numbers run first)
            $table->integer('priority')->default(0)->index();

            // Lifecycle state of the rule: draft, testing, active, inactive, archived
            // Defaulting to 'draft' ensures new rules don't accidentally go live immediately
            $table->string('status')->default(RuleStatus::DRAFT)->index();

            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableName = config('logic-as-data.table_name', 'logic_rules');
        
        Schema::dropIfExists($tableName);
    }
};
