<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admins = [
            [
                'id' => 1,
                'name' => 'Jonathon Lunardi',
                'email' => 'alumnimatchco@gmail.com',
                'role' => 1,
                'password' => Hash::make('J0n@th0N')
            ],
            [
                'id' => 2,
                'name' => 'Mark Berlon Pating',
                'email' => 'mbpating@gmail.com',
                'role' => 1,
                'password' => Hash::make('M@rkB3rl0N')
            ]
        ];

        foreach ($admins as $admin){
            \App\Models\Admin::create($admin);
        }
    }
}
