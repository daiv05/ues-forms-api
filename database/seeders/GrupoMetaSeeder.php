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
                'activo' => true,
            ],
            [
                'id_usuario' => 1,
                'nombre' => 'Salud',
                'descripcion' => 'Frente de salud y bienestar',
                'activo' => true,
            ],
            [
                'id_usuario' => 2,
                'nombre' => 'Economía',
                'descripcion' => 'Frente de economía y desarrollo',
                'activo' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Medio Ambiente',
                'descripcion' => 'Frente de medio ambiente y sostenibilidad',
                'activo' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Cultura',
                'descripcion' => 'Frente de cultura y patrimonio',
                'activo' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Tecnología',
                'descripcion' => 'Frente de tecnología e innovación',
                'activo' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Seguridad',
                'descripcion' => 'Frente de seguridad y justicia',
                'activo' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Transporte',
                'descripcion' => 'Frente de transporte y movilidad',
                'activo' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Infraestructura',
                'descripcion' => 'Frente de infraestructura y desarrollo urbano',
                'activo' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Turismo',
                'descripcion' => 'Frente de turismo y desarrollo local',
                'activo' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Deporte',
                'descripcion' => 'Frente de deporte y recreación',
                'activo' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Ciencia',
                'descripcion' => 'Frente de ciencia y tecnología',
                'activo' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Derechos Humanos',
                'descripcion' => 'Frente de derechos humanos y equidad',
                'activo' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Participación Ciudadana',
                'descripcion' => 'Frente de participación ciudadana y gobernanza',
                'activo' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Desarrollo Social',
                'descripcion' => 'Frente de desarrollo social y comunitario',
                'activo' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Pobreza',
                'descripcion' => 'Frente de pobreza y exclusión social',
                'activo' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Desarrollo Económico',
                'descripcion' => 'Frente de desarrollo económico y empleo',
                'activo' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Desarrollo Rural',
                'descripcion' => 'Frente de desarrollo rural y agricultura',
                'activo' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Desarrollo Urbano',
                'descripcion' => 'Frente de desarrollo urbano y vivienda',
                'activo' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Desarrollo Sostenible',
                'descripcion' => 'Frente de desarrollo sostenible y medio ambiente',
                'activo' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Desarrollo Humano',
                'descripcion' => 'Frente de desarrollo humano y bienestar social',
                'activo' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Desarrollo Cultural',
                'descripcion' => 'Frente de desarrollo cultural y patrimonio',
                'activo' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Desarrollo Tecnológico',
                'descripcion' => 'Frente de desarrollo tecnológico y digitalización',
                'activo' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Desarrollo Empresarial',
                'descripcion' => 'Frente de desarrollo empresarial y emprendimiento',
                'activo' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Desarrollo Comunitario',
                'descripcion' => 'Frente de desarrollo comunitario y participación ciudadana',
                'activo' => true,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Desarrollo Social Integral',
                'descripcion' => 'Frente de desarrollo social integral y bienestar comunitario',
                'activo' => true,
            ],
        ];

        foreach ($gruposMeta as $grupoMeta) {
            \App\Models\Encuesta\GrupoMeta::create($grupoMeta);
        }

    }
}
