<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class taskTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('task')->insert([
            'name' => 'Laravel Programming',
            'priority' => 0,
            'project_id' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('task')->insert([
            'name' => 'VueJS Programming',
            'priority' => 1,
            'project_id' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('task')->insert([
            'name' => 'AngularJS Programming',
            'priority' => 2,
            'project_id' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('task')->insert([
            'name' => 'Testing',
            'priority' => 3,
            'project_id' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('task')->insert([
            'name' => 'Python Programming',
            'priority' => 0,
            'project_id' => 2,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('task')->insert([
            'name' => 'C# Programming',
            'priority' => 1,
            'project_id' => 2,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('task')->insert([
            'name' => 'Java Programming',
            'priority' => 2,
            'project_id' => 2,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('task')->insert([
            'name' => '.Net Programming',
            'priority' => 3,
            'project_id' => 2,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('task')->insert([
            'name' => 'Photo Manipulation',
            'priority' => 0,
            'project_id' => 3,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('task')->insert([
            'name' => '3D Modeling',
            'priority' => 1,
            'project_id' => 3,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('task')->insert([
            'name' => 'Graphic Design',
            'priority' => 2,
            'project_id' => 3,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('task')->insert([
            'name' => 'Website Mock Up',
            'priority' => 3,
            'project_id' => 3,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
