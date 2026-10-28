<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambah kolom profil ke tabel `admin` (hanya yang belum ada).
     */
    public function up(): void
    {
        Schema::table('admin', function (Blueprint $table) {
            if (! Schema::hasColumn('admin', 'nama')) {
                $table->string('nama', 100)->nullable();
            }
            if (! Schema::hasColumn('admin', 'username')) {
                $table->string('username', 50)->nullable()->unique();
            }
            if (! Schema::hasColumn('admin', 'email')) {
                $table->string('email', 150)->nullable()->unique();
            }
            if (! Schema::hasColumn('admin', 'foto')) {
                $table->string('foto')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     * Sengaja dikosongkan agar data admin tidak ikut terhapus.
     */
    public function down(): void
    {
        //
    }
};
