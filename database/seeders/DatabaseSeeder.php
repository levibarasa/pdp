<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();


        $user = \App\Models\User::create([
            'name' => 'Cleophas Okioi',
            'email' => 'cleophas.okioi@gmail.com',
            'password' => \Hash::make('password'),
            'docnumber'=>123456789
        ]);
        $user->assignRole("adminpdp");







    }
}
