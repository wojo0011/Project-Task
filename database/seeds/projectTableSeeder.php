<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class projectTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('project')->insert([
            'name' => 'Project 1',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('project')->insert([
            'name' => 'Project 2',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('project')->insert([
            'name' => 'Project 3',
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
