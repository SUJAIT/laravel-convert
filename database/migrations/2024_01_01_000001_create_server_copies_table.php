<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('server_copies', function (Blueprint $table) {
            $table->id();
            $table->string('nid', 17)->index();
            $table->string('dob', 20)->index();

            // Names
            $table->string('name')->nullable();        // Bengali
            $table->string('name_en')->nullable();     // English

            // IDs
            $table->string('pin', 17)->nullable();

            // Personal info
            $table->string('father')->nullable();
            $table->string('father_nid', 17)->nullable();
            $table->string('mother')->nullable();
            $table->string('mother_nid', 17)->nullable();
            $table->string('spouse')->nullable();
            $table->string('blood_group', 5)->nullable();
            $table->string('gender', 20)->nullable();
            $table->string('birth_place')->nullable();
            $table->string('religion', 50)->nullable();
            $table->string('occupation')->nullable();
            $table->string('mobile', 20)->nullable();

            // Voter info
            $table->string('voter_no')->nullable();
            $table->string('voter_area')->nullable();
            $table->unsignedInteger('voter_area_code')->nullable();
            $table->unsignedInteger('sl_no')->nullable();

            // Addresses (pre-built strings)
            $table->text('pre_address_line')->nullable();
            $table->text('per_address_line')->nullable();

            // Photo URL
            $table->text('photo')->nullable();

            // Full raw API response (for future reference)
            $table->json('raw_data')->nullable();

            $table->timestamps();

            // Composite index for fast lookup
            $table->index(['nid', 'dob']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('server_copies');
    }
};
