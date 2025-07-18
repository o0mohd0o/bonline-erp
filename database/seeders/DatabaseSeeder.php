<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\ServiceTemplateSeeder;
use Database\Seeders\HostingServiceSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ServiceTemplateSeeder::class,
            HostingServiceSeeder::class,
        ]);

        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Mohamed',
            'email' => 'mohamed',
            'password' => bcrypt('LFmo1Z+R1z_W'),
        ]);
    }
}
