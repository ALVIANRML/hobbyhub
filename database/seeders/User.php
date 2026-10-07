<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class User extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          $sql = file_get_contents('database/seeders/sql/user.sql');
            DB::insert($sql,[
            Hash::make('password123'),
            Hash::make('user123'),
            Hash::make('sri123'),
            Hash::make('Rian123'),
            Hash::make('Citra123'),
            ]);
    }
}
