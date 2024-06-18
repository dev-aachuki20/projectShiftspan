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
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('is_agreement')->default(0)->comment('1=> checked_agreement, 0=>unchecked_agreement')->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_agreement');
        });
    }
};
