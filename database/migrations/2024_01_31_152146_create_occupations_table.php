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
        Schema::create('occupations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('name')->unique();
            $table->unsignedBigInteger('sub_admin_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->tinyInteger('is_active')->default(1)->comment('1=> active, 0=>deactive');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('sub_admin_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('occupations');
    }
};
