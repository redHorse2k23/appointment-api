<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ownerAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
         $owner =  User::create([
                'name' => 'Owner',
                'email' => 'owner@gmail.com',
                'password' => Hash::make('owner123'),
                'role' => 'owner'
            ]);

        $owner->save();
    }
}
