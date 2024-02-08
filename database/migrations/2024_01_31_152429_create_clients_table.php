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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('address',255)->nullable();
            $table->text('shop_description')->nullable();
            $table->text('parking_description')->nullable();
            $table->unsignedBigInteger('sub_admin_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->tinyInteger('is_active')->default(1)->comment('1=> active, 0=>deactive');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
