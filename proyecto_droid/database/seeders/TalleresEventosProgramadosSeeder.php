<?php

namespace Database\Seeders;

use App\Models\TallerRecreativo;
use App\Models\EventoAcademico;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TalleresEventosProgramadosSeeder extends Seeder
{
    /**
     * Seed talleres y eventos programados relacionados con salud física y alimentaria
     * 
     * Este seeder está especializado en actividades del área de salud:
     * - Talleres: Enfocados en salud física, terapia y rehabilitación
     * - Eventos: Enfocados en nutrición, alimentación saludable y cocina terapéutica
     * 
     * Los datos simulan información que posteriormente se cargaría desde una API
     * de servicios de salud, centros médicos o plataformas de bienestar.
     */
    public function run(): void
    {
        // Limpiar datos existentes si es necesario
        // TallerRecreativo::truncate();
        // EventoAcademico::truncate();

        $this->seedTalleresProgramados();
        $this->seedEventosProgramados();
    }

    /**
     * Seed talleres recreativos programados
     */
    public function seedTalleresProgramados(): void
    {
        $talleresProgramados = [
            // Talleres de la próxima semana - Salud Física
            [
                'nombre' => 'Yoga Terapéutico',
                'descripcion' => 'Clase de yoga enfocada en la rehabilitación y fortalecimiento muscular. Dirigida a personas con lesiones menores o dolor crónico.',
                'instructor' => 'Fisioterapeuta Carmen Mendoza',
                'categoria' => 'salud_fisica',
                'duracion_minutos' => 75,
                'nivel_dificultad' => 1,
                'cupo_maximo' => 15,
                'costo' => 20.00,
                'ubicacion' => 'Salón de Rehabilitación - Centro Médico',
                'fecha_inicio' => Carbon::now()->addDays(3)->setTime(7, 0, 0),
                'fecha_fin' => Carbon::now()->addDays(3)->setTime(8, 15, 0),
                'activo' => true,
                'imagen_url' => 'https://api.example.com/images/yoga-terapeutico.jpg',
                'requisitos' => 'Certificado médico, colchoneta y ropa cómoda'
            ],
            [
                'nombre' => 'Hidroterapia y Ejercicios Acuáticos',
                'descripcion' => 'Sesión de ejercicios terapéuticos en agua para mejorar la movilidad, fortalecer músculos y reducir dolor articular.',
                'instructor' => 'Lic. Kinesiología Miguel Torres',
                'categoria' => 'salud_fisica',
                'duracion_minutos' => 60,
                'nivel_dificultad' => 2,
                'cupo_maximo' => 12,
                'costo' => 25.00,
                'ubicacion' => 'Piscina Terapéutica - Centro de Rehabilitación',
                'fecha_inicio' => Carbon::now()->addDays(5)->setTime(16, 30, 0),
                'fecha_fin' => Carbon::now()->addDays(5)->setTime(17, 30, 0),
                'activo' => true,
                'imagen_url' => 'https://api.example.com/images/hidroterapia.jpg',
                'requisitos' => 'Traje de baño, toalla y evaluación médica previa'
            ],
            [
                'nombre' => 'Entrenamiento Funcional para Adultos Mayores',
                'descripcion' => 'Rutina de ejercicios funcionales diseñada para mejorar la fuerza, equilibrio y coordinación en adultos mayores.',
                'instructor' => 'Educador Físico Carlos Ramírez',
                'categoria' => 'salud_fisica',
                'duracion_minutos' => 90,
                'nivel_dificultad' => 1,
                'cupo_maximo' => 18,
                'costo' => 15.00,
                'ubicacion' => 'Gimnasio Geriátrico - Piso 3',
                'fecha_inicio' => Carbon::now()->addDays(7)->setTime(14, 0, 0),
                'fecha_fin' => Carbon::now()->addDays(7)->setTime(15, 30, 0),
                'activo' => true,
                'imagen_url' => 'https://api.example.com/images/entrenamiento-adultos-mayores.jpg',
                'requisitos' => 'Autorización médica y ropa deportiva'
            ],
            
            // Talleres de la próxima quincena - Salud Física
            [
                'nombre' => 'Pilates Clínico',
                'descripcion' => 'Sesión de pilates orientada a la corrección postural y fortalecimiento del core para prevenir lesiones.',
                'instructor' => 'Fisioterapeuta especializada Ana Rivera',
                'categoria' => 'salud_fisica',
                'duracion_minutos' => 120,
                'nivel_dificultad' => 2,
                'cupo_maximo' => 14,
                'costo' => 30.00,
                'ubicacion' => 'Estudio de Pilates - Centro de Bienestar',
                'fecha_inicio' => Carbon::now()->addDays(10)->setTime(9, 0, 0),
                'fecha_fin' => Carbon::now()->addDays(10)->setTime(11, 0, 0),
                'activo' => true,
                'imagen_url' => 'https://api.example.com/images/pilates-clinico.jpg',
                'requisitos' => 'Evaluación postural previa y mat de pilates'
            ],
            [
                'nombre' => 'Cardio Rehabilitación',
                'descripcion' => 'Programa de ejercicios cardiovasculares supervisados para pacientes con condiciones cardíacas estables.',
                'instructor' => 'Cardiólogo Dr. María González',
                'categoria' => 'salud_fisica',
                'duracion_minutos' => 90,
                'nivel_dificultad' => 2,
                'cupo_maximo' => 10,
                'costo' => 40.00,
                'ubicacion' => 'Unidad de Cardiología - Hospital',
                'fecha_inicio' => Carbon::now()->addDays(12)->setTime(8, 0, 0),
                'fecha_fin' => Carbon::now()->addDays(12)->setTime(9, 30, 0),
                'activo' => true,
                'imagen_url' => 'https://api.example.com/images/cardio-rehabilitacion.jpg',
                'requisitos' => 'Autorización cardiológica obligatoria y monitoreo continuo'
            ],
            [
                'nombre' => 'Terapia de Movimiento y Danza',
                'descripcion' => 'Sesión terapéutica que combina danza y movimiento para mejorar la coordinación y bienestar emocional.',
                'instructor' => 'Terapeuta de Danza Patricia Campos',
                'categoria' => 'salud_fisica',
                'duracion_minutos' => 105,
                'nivel_dificultad' => 1,
                'cupo_maximo' => 16,
                'costo' => 22.00,
                'ubicacion' => 'Sala de Terapias Alternativas',
                'fecha_inicio' => Carbon::now()->addDays(14)->setTime(10, 0, 0),
                'fecha_fin' => Carbon::now()->addDays(14)->setTime(11, 45, 0),
                'activo' => true,
                'imagen_url' => 'https://api.example.com/images/terapia-movimiento.jpg',
                'requisitos' => 'Ropa cómoda y zapatos flexibles'
            ],
            
            // Talleres del próximo mes - Salud Física
            [
                'nombre' => 'Mindfulness y Relajación Muscular',
                'descripcion' => 'Técnicas de mindfulness combinadas con relajación muscular progresiva para el manejo del estrés y dolor crónico.',
                'instructor' => 'Psicóloga Clínica Roberto Paz',
                'categoria' => 'salud_fisica',
                'duracion_minutos' => 75,
                'nivel_dificultad' => 1,
                'cupo_maximo' => 20,
                'costo' => 0.00,
                'ubicacion' => 'Sala de Meditación - Centro de Bienestar',
                'fecha_inicio' => Carbon::now()->addDays(21)->setTime(18, 0, 0),
                'fecha_fin' => Carbon::now()->addDays(21)->setTime(19, 15, 0),
                'activo' => true,
                'imagen_url' => 'https://api.example.com/images/mindfulness-relajacion.jpg',
                'requisitos' => 'Ropa cómoda, manta y cojín opcional'
            ],
            [
                'nombre' => 'Prevención de Lesiones en el Trabajo',
                'descripcion' => 'Taller práctico sobre ergonomía laboral y ejercicios preventivos para evitar lesiones ocupacionales.',
                'instructor' => 'Especialista en Salud Ocupacional Andrea López',
                'categoria' => 'salud_fisica',
                'duracion_minutos' => 180,
                'nivel_dificultad' => 1,
                'cupo_maximo' => 25,
                'costo' => 35.00,
                'ubicacion' => 'Aula de Capacitación - Medicina Laboral',
                'fecha_inicio' => Carbon::now()->addDays(28)->setTime(13, 0, 0),
                'fecha_fin' => Carbon::now()->addDays(28)->setTime(16, 0, 0),
                'activo' => true,
                'imagen_url' => 'https://api.example.com/images/prevencion-lesiones.jpg',
                'requisitos' => 'Ropa de trabajo habitual para evaluación postural'
            ]
        ];

        foreach ($talleresProgramados as $taller) {
            TallerRecreativo::create($taller);
        }
    }

    /**
     * Seed eventos académicos programados
     */
    public function seedEventosProgramados(): void
    {
        $eventosProgramados = [
            // Eventos de la próxima semana - Salud Nutricional
            [
                'titulo' => 'Conferencia: Nutrición Clínica y Enfermedades Crónicas',
                'descripcion' => 'Conferencia magistral sobre el papel de la nutrición en la prevención y tratamiento de enfermedades crónicas como diabetes, hipertensión y obesidad.',
                'tipo_evento' => 'conferencia',
                'fecha_inicio' => Carbon::now()->addDays(4)->setTime(16, 0, 0),
                'fecha_fin' => Carbon::now()->addDays(4)->setTime(18, 0, 0),
                'ubicacion' => 'Auditorio Médico - Hospital Universitario',
                'organizador' => 'Dra. Nutricionista Silvia Herrera',
                'cupos_disponibles' => 120,
                'inscripcion_requerida' => true,
                'url_inscripcion' => 'https://api.example.com/inscripcion/nutricion-clinica',
                'activo' => true,
                'imagen_url' => 'https://api.example.com/images/nutricion-clinica.jpg'
            ],
            [
                'titulo' => 'Webinar: Micronutrientes y Sistema Inmunológico',
                'descripcion' => 'Evento virtual sobre el papel de vitaminas y minerales en el fortalecimiento del sistema inmunológico y prevención de enfermedades.',
                'tipo_evento' => 'webinar',
                'fecha_inicio' => Carbon::now()->addDays(6)->setTime(11, 0, 0),
                'fecha_fin' => Carbon::now()->addDays(6)->setTime(12, 30, 0),
                'ubicacion' => 'Plataforma Virtual - Centro de Nutrición',
                'organizador' => 'Dr. Inmunólogo Carlos Mendez',
                'cupos_disponibles' => 250,
                'inscripcion_requerida' => true,
                'url_inscripcion' => 'https://api.example.com/inscripcion/micronutrientes-inmunologia',
                'activo' => true,
                'imagen_url' => 'https://api.example.com/images/micronutrientes-inmunologia.jpg'
            ],
            
            // Eventos de la próxima quincena - Alimentación Saludable
            [
                'titulo' => 'Simposio: Alimentación Funcional y Superalimentos',
                'descripcion' => 'Simposio académico sobre alimentos funcionales, superalimentos y su impacto en la salud preventiva.',
                'tipo_evento' => 'simposio',
                'fecha_inicio' => Carbon::now()->addDays(11)->setTime(9, 0, 0),
                'fecha_fin' => Carbon::now()->addDays(11)->setTime(17, 0, 0),
                'ubicacion' => 'Centro de Investigación Nutricional',
                'organizador' => 'Instituto de Ciencias de la Alimentación',
                'cupos_disponibles' => 180,
                'inscripcion_requerida' => true,
                'url_inscripcion' => 'https://api.example.com/inscripcion/alimentacion-funcional',
                'activo' => true,
                'imagen_url' => 'https://api.example.com/images/alimentacion-funcional.jpg'
            ],
            [
                'titulo' => 'Mesa Redonda: Planificación de Menús Terapéuticos',
                'descripcion' => 'Debate entre nutricionistas sobre la planificación de menús especializados para diferentes patologías y condiciones de salud.',
                'tipo_evento' => 'mesa_redonda',
                'fecha_inicio' => Carbon::now()->addDays(13)->setTime(15, 0, 0),
                'fecha_fin' => Carbon::now()->addDays(13)->setTime(17, 0, 0),
                'ubicacion' => 'Sala de Conferencias - Facultad de Nutrición',
                'organizador' => 'Colegio de Nutricionistas Profesionales',
                'cupos_disponibles' => 60,
                'inscripcion_requerida' => true,
                'url_inscripcion' => 'https://api.example.com/inscripcion/menus-terapeuticos',
                'activo' => true,
                'imagen_url' => 'https://api.example.com/images/menus-terapeuticos.jpg'
            ],
            
            // Eventos del próximo mes - Salud Integral
            [
                'titulo' => 'Congreso: Nutrición Deportiva y Rendimiento',
                'descripcion' => 'Congreso internacional sobre nutrición deportiva, suplementación y optimización del rendimiento físico.',
                'tipo_evento' => 'congreso',
                'fecha_inicio' => Carbon::now()->addDays(25)->setTime(8, 0, 0),
                'fecha_fin' => Carbon::now()->addDays(27)->setTime(18, 0, 0),
                'ubicacion' => 'Centro de Medicina Deportiva',
                'organizador' => 'Federación Internacional de Nutrición Deportiva',
                'cupos_disponibles' => 400,
                'inscripcion_requerida' => true,
                'url_inscripcion' => 'https://api.example.com/inscripcion/nutricion-deportiva',
                'activo' => true,
                'imagen_url' => 'https://api.example.com/images/nutricion-deportiva.jpg'
            ],
            [
                'titulo' => 'Masterclass: Cocina Terapéutica y Recetas Medicinales',
                'descripcion' => 'Masterclass práctica sobre preparación de alimentos con propiedades terapéuticas y recetas para condiciones específicas de salud.',
                'tipo_evento' => 'masterclass',
                'fecha_inicio' => Carbon::now()->addDays(30)->setTime(10, 0, 0),
                'fecha_fin' => Carbon::now()->addDays(30)->setTime(16, 0, 0),
                'ubicacion' => 'Cocina Experimental - Instituto Gastronómico',
                'organizador' => 'Chef Especializada en Nutrición Elena Morales',
                'cupos_disponibles' => 30,
                'inscripcion_requerida' => true,
                'url_inscripcion' => 'https://api.example.com/inscripcion/cocina-terapeutica',
                'activo' => true,
                'imagen_url' => 'https://api.example.com/images/cocina-terapeutica.jpg'
            ]
        ];

        foreach ($eventosProgramados as $evento) {
            EventoAcademico::create($evento);
        }
    }
} 