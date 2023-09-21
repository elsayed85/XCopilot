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
        Schema::create('account_invites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('github_accounts')->cascadeOnDelete();

            $table->string('token')->unique();
            $table->integer('max_usages')->default(1);
            $table->integer('usages')->default(0);
            $table->timestamp('expires_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_invites');
    }
};
