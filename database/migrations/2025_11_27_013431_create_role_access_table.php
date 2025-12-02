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
        Schema::create('role_access', function (Blueprint $table) {
            $table->id();
            $table->enum('role', ['Direktur', 'Pegawai', 'Admin', 'Resepsionis']);
            $table->unsignedBigInteger('ruangan_id')->nullable();
            $table->boolean('can_book')->default(true);
            $table->boolean('can_approve')->default(false);
            $table->boolean('can_cancel')->default(false);
            $table->timestamps();

            $table->foreign('ruangan_id')->references('id')->on('rooms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_access');
    }
};
