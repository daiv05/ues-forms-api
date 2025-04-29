<?php

namespace Database\Seeders;

use App\Models\Registro\Persona;
use Illuminate\Database\Seeder;

class PersonaSeeder extends Seeder
{
    public function run(): void
    {
        $personas = [
            [
                'nombre' => 'DAVID',
                'apellido' => 'DERAS',
                'fecha_nacimiento' => '2001-08-04',
                'telefono' => '74641460',
            ],
            [
                'nombre' => 'EDENILSON',
                'apellido' => 'ROSALES',
                'fecha_nacimiento' => '2001-08-04',
                'telefono' => '74641460',
            ],
            [
                'nombre' => 'GABRIELA',
                'apellido' => 'MIRANDA',
                'fecha_nacimiento' => '2003-11-01',
                'telefono' => '74641460',
            ],
        ];

        foreach ($personas as $persona) {
            Persona::create($persona);
        }
    }
}
