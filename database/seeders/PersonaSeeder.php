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
                'nombre' => 'MISAEL',
                'apellido' => 'GOMEZ',
                'fecha_nacimiento' => '2001-08-04',
                'telefono' => '74641460',
            ],
            [
                'nombre' => 'LEONARDO',
                'apellido' => 'EFIGENIO',
                'fecha_nacimiento' => '2003-11-01',
                'telefono' => '74641460',
            ],
            [
                'nombre' => 'BRYAN',
                'apellido' => 'MARROQUIN',
                'fecha_nacimiento' => '2002-02-11',
                'telefono' => '74641460',
            ],
            [
                'nombre' => 'JUAN',
                'apellido' => 'LANDAVERDE',
                'fecha_nacimiento' => '2002-10-14',
                'telefono' => '74641460',
            ],
            [
                'nombre' => 'LUIS',
                'apellido' => 'MARTINEZ',
                'fecha_nacimiento' => '2002-02-11',
                'telefono' => '74641460',
            ],
            [
                'nombre' => 'RODRIGO',
                'apellido' => 'PALMERA',
                'fecha_nacimiento' => '2002-10-14',
                'telefono' => '74641460',
            ],
            [
                'nombre' => 'MARIA',
                'apellido' => 'PAZ',
                'fecha_nacimiento' => '2002-02-11',
                'telefono' => '74641460',
            ],
            [
                'nombre' => 'PEDRO',
                'apellido' => 'PEREZ',
                'fecha_nacimiento' => '2002-10-14',
                'telefono' => '74641460',
            ],
            [
                'nombre' => 'ALEX',
                'apellido' => 'EMILIO',
                'fecha_nacimiento' => '2002-02-11',
                'telefono' => '74641460',
            ],
            [
                'nombre' => 'CRISTIAN',
                'apellido' => 'MEJIA',
                'fecha_nacimiento' => '2002-10-14',
                'telefono' => '74641460',
            ],
            [
                'nombre' => 'DIEGO',
                'apellido' => 'HIDALGO',
                'fecha_nacimiento' => '2002-02-11',
                'telefono' => '74641460',
            ],
            [
                'nombre' => 'ENRIQUE',
                'apellido' => 'CANALES',
                'fecha_nacimiento' => '2002-10-14',
                'telefono' => '74641460',
            ],
            [
                'nombre' => 'BRAULIO',
                'apellido' => 'SANDOVAL',
                'fecha_nacimiento' => '2002-10-14',
                'telefono' => '74641460',
            ]
        ];

        foreach ($personas as $persona) {
            Persona::create($persona);
        }
    }
}
