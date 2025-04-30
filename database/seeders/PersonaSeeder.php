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
                'identificacion' => '24242432',
            ],
            [
                'nombre' => 'EDENILSON',
                'apellido' => 'ROSALES',
                'identificacion' => '77643535',
            ],
            [
                'nombre' => 'GABRIELA',
                'apellido' => 'MIRANDA',
                'identificacion' => '35282255',
            ],
        ];

        foreach ($personas as $persona) {
            Persona::create($persona);
        }
    }
}
