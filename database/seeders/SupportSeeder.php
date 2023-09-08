<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SupportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Support::factory()->count(10)->create();
    }
}
