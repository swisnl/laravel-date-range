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
        Schema::create('both_default_name_nullable', function (Blueprint $table) {
            $table->id();

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->timestamps();
        });

        Schema::create('both_other_name_nullable', function (Blueprint $table) {
            $table->id();

            $table->date('foo')->nullable();
            $table->date('bar')->nullable();

            $table->timestamps();
        });

        Schema::create('both_default_name_start_date_required', function (Blueprint $table) {
            $table->id();

            $table->date('start_date');
            $table->date('end_date')->nullable();

            $table->timestamps();
        });

        Schema::create('both_default_name_end_date_required', function (Blueprint $table) {
            $table->id();

            $table->date('start_date')->nullable();
            $table->date('end_date');

            $table->timestamps();
        });

        Schema::create('both_default_name_required', function (Blueprint $table) {
            $table->id();

            $table->date('start_date');
            $table->date('end_date');

            $table->timestamps();
        });

        Schema::create('start_date_only_default_name_nullable', function (Blueprint $table) {
            $table->id();

            $table->date('start_date')->nullable();

            $table->timestamps();
        });

        Schema::create('end_date_only_default_name_nullable', function (Blueprint $table) {
            $table->id();

            $table->date('end_date')->nullable();

            $table->timestamps();
        });

        Schema::create('none', function (Blueprint $table) {
            $table->id();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('both_default_name_nullable');
        Schema::dropIfExists('both_other_name_nullable');
        Schema::dropIfExists('both_default_name_start_date_required');
        Schema::dropIfExists('both_default_name_end_date_required');
        Schema::dropIfExists('both_default_name_both_required');
        Schema::dropIfExists('start_date_only_default_name_nullable');
        Schema::dropIfExists('end_date_only_default_name_nullable');
        Schema::dropIfExists('none');
    }
};
