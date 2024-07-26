<?php

namespace Database\Seeders;
use App\Models\Code;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Code::factory(50)->create();
        Code::factory()->count(50)->create();
    }
}
