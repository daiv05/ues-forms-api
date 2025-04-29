<?php

namespace Database\Seeders;

use App\Models\Seguridad\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = [
            [
                'id_persona' => 1,
                'username' => 'administrador',
                'email' => 'administrador@yopmail.com',
                'password' => bcrypt('pass123'),
                'role' => 'ADMINISTRADOR',
            ],
            [
                'id_persona' => 2,
                'username' => 'encuestador1',
                'email' => 'encuestador1@yopmail.com',
                'password' => bcrypt('pass123'),
                'role' => 'ENCUESTADOR',
            ],
            [
                'id_persona' => 3,
                'username' => 'encuestador2',
                'email' => 'encuestador2@yopmail.com',
                'password' => bcrypt('pass123'),
                'role' => 'ENCUESTADOR'
            ],
        ];

        foreach ($usuarios as $usuario) {
            $user = User::create(
                [
                    'id_persona' => $usuario['id_persona'],
                    'username' => $usuario['username'],
                    'email' => $usuario['email'],
                    'password' => $usuario['password'],
                    'email_verified_at' => Carbon::now()
                ]
            );

            $user->assignRole($usuario['role']);
        }
    }
}
