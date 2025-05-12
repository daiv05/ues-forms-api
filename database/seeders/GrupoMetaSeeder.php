<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GrupoMetaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gruposMeta = [
            [
                'id_usuario' => 1,
                'nombre' => 'Educación',
                'descripcion' => 'Frente de educación y formación',
                'estado' => true,
            ],
            [
                'id_usuario' => 1,
                'nombre' => 'Salud',
                'descripcion' => 'Frente de salud y bienestar',
                'estado' => true,
            ],
            [
                'id_usuario' => 2,
                'nombre' => 'Economía',
                'descripcion' => 'Frente de economía y desarrollo',
                'estado' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Medio Ambiente',
                'descripcion' => 'Frente de medio ambiente y sostenibilidad',
                'estado' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Cultura',
                'descripcion' => 'Frente de cultura y patrimonio',
                'estado' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Tecnología',
                'descripcion' => 'Frente de tecnología e innovación',
                'estado' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Seguridad',
                'descripcion' => 'Frente de seguridad y justicia',
                'estado' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Transporte',
                'descripcion' => 'Frente de transporte y movilidad',
                'estado' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Infraestructura',
                'descripcion' => 'Frente de infraestructura y desarrollo urbano',
                'estado' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Turismo',
                'descripcion' => 'Frente de turismo y desarrollo local',
                'estado' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Deporte',
                'descripcion' => 'Frente de deporte y recreación',
                'estado' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Ciencia',
                'descripcion' => 'Frente de ciencia y tecnología',
                'estado' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Derechos Humanos',
                'descripcion' => 'Frente de derechos humanos y equidad',
                'estado' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Participación Ciudadana',
                'descripcion' => 'Frente de participación ciudadana y gobernanza',
                'estado' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Desarrollo Social',
                'descripcion' => 'Frente de desarrollo social y comunitario',
                'estado' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Pobreza',
                'descripcion' => 'Frente de pobreza y exclusión social',
                'estado' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Desarrollo Económico',
                'descripcion' => 'Frente de desarrollo económico y empleo',
                'estado' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Desarrollo Rural',
                'descripcion' => 'Frente de desarrollo rural y agricultura',
                'estado' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Desarrollo Urbano',
                'descripcion' => 'Frente de desarrollo urbano y vivienda',
                'estado' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Desarrollo Sostenible',
                'descripcion' => 'Frente de desarrollo sostenible y medio ambiente',
                'estado' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Desarrollo Humano',
                'descripcion' => 'Frente de desarrollo humano y bienestar social',
                'estado' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Desarrollo Cultural',
                'descripcion' => 'Frente de desarrollo cultural y patrimonio',
                'estado' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Desarrollo Tecnológico',
                'descripcion' => 'Frente de desarrollo tecnológico y digitalización',
                'estado' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Desarrollo Empresarial',
                'descripcion' => 'Frente de desarrollo empresarial y emprendimiento',
                'estado' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Desarrollo Comunitario',
                'descripcion' => 'Frente de desarrollo comunitario y participación ciudadana',
                'estado' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Desarrollo Social Integral',
                'descripcion' => 'Frente de desarrollo social integral y bienestar comunitario',
                'estado' => true,
            ],
        ];

        foreach ($gruposMeta as $grupoMeta) {
            \App\Models\Encuesta\GrupoMeta::create($grupoMeta);
        }

    }
}
