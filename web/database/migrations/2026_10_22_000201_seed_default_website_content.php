<?php

use Database\Seeders\WebsiteSeeder;
use Illuminate\Database\Migrations\Migration;

// Isi awal website untuk instalasi yang sudah berjalan (update tidak menjalankan db:seed).
return new class extends Migration
{
    public function up(): void
    {
        (new WebsiteSeeder)->run();
    }
};
