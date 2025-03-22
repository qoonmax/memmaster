<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();

            $table->string('slug')
                ->unique();

            $table->foreignId('user_id')
                ->constrained();

            $table->text('content');

            $table->integer('stage')
                ->default(1);

            $table->timestamp('next_repeat_at');

            $table->timestamps();
        });

        DB::statement('ALTER TABLE cards ADD CONSTRAINT check_stage CHECK (stage >= 1 AND stage <= 8)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
