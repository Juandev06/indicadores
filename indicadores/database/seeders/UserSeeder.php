<?php

namespace Database\Seeders;
use App\Models\User;


use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        User::create([
            'name'=> 'Yenny',
            'email' => 'pruebadmin@gmail.com',
            'password' => bcrypt('test123'),
            'lastName' => 'Perea',
            'address'=> 'Cll 13 #10-10',
            'phone'=> '321478596',
            'status' => 1,
            'id_area' => 2,
            'id_rol'=>1,
            'identification'=> '4789922'
        ]);

        User::create([
            'name'=> 'Greta',
            'email' => 'pruebauser@gmail.com',
            'password' => bcrypt('test123'),
            'lastName' => 'Murillo',
            'address'=> 'Cll 13 #10-20',
            'phone'=> '321478598',
            'status' => 1,
            'id_area' => 1,
            'id_rol'=>2,
            'identification'=> '47899228965210'
        ]);
    }
}
