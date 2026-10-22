<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->json('value')->nullable();
            $table->timestamps();
        });

        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('level')->nullable(); // contoh: Setara SD
            $table->string('summary', 500)->nullable();
            $table->longText('body')->nullable();
            $table->json('features')->nullable();
            $table->string('duration')->nullable();
            $table->string('image')->nullable();
            $table->string('icon')->default('book');
            $table->boolean('open_registration')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->default('Berita'); // Berita, Pengumuman, Kegiatan
            $table->string('excerpt', 500)->nullable();
            $table->longText('body');
            $table->string('cover')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('gallery_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('image');
            $table->string('caption')->nullable();
            $table->date('taken_at')->nullable();
            $table->timestamps();
        });

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role')->nullable(); // contoh: Lulusan Paket C 2024
            $table->text('quote');
            $table->string('photo')->nullable();
            $table->boolean('visible')->default(true);
            $table->timestamps();
        });

        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('position');
            $table->string('photo')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('program_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('nik', 20)->nullable();
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('gender', 1)->nullable(); // L / P
            $table->string('phone', 30);
            $table->string('email')->nullable();
            $table->text('address');
            $table->string('last_education')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('baru'); // baru, dihubungi, diterima, ditolak
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['registrations', 'staff', 'testimonials', 'gallery_items', 'posts', 'programs', 'settings'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
