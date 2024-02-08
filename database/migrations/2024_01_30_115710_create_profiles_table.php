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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->date('dob')->nullable();
            $table->string('previous_name',191)->nullable();
            $table->string('national_insurance_number',50)->nullable();
            $table->text('address')->nullable();
            $table->string('education',191)->nullable();
            $table->string('prev_emp_1',255)->nullable();
            $table->string('prev_emp_2',255)->nullable();
            $table->string('reference_1',191)->nullable();
            $table->string('reference_2',191)->nullable();
            $table->date('date_sign')->nullable();
            $table->tinyInteger('is_criminal')->default(0)->comment('1=> Yes, 0=>No');
            $table->tinyInteger('is_rehabilite')->default(0)->comment('1=> Yes, 0=>No');
            $table->tinyInteger('is_enquire')->default(0)->comment('1=> Yes, 0=>No');
            $table->tinyInteger('is_health_issue')->default(0)->comment('1=> Yes, 0=>No');
            $table->tinyInteger('is_statement')->default(0)->comment('1=> Yes, 0=>No');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
