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
        Schema::create('homes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('address',255)->nullable();
            $table->text('shop_descp')->nullable();
            $table->text('parking_descp')->nullable();
            $table->integer('created_by')->nullable();
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
        Schema::dropIfExists('homes');
    }
};
