<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Desafio;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UsuarioDesafioController extends Controller
{
    public function sugeridos($id)
    {
        $usuario = Usuario::findOrFail($id);

        // Calcular IMC actual
        $alturaM = $usuario->altura_cm / 100;
        $imc = round($usuario->peso_kg / ($alturaM * $alturaM), 2);

        // Obtener objetivo actual (puedes mejorarlo para buscar solo los activos y vigentes)
        $objetivo = $usuario->objetivosAlimentacion()
                            ->where('activo', true)
                            ->latest('fecha_inicio')
                            ->first()?->tipo_objetivo ?? 'mantener peso';

        // Obtener desafíos activos
        $hoy = Carbon::today();
        $desafios = Desafio::where('activo', true)
            ->whereDate('fecha_inicio', '<=', $hoy)
            ->whereDate('fecha_fin', '>=', $hoy)
            ->get();

        // Filtrar desafíos en base al IMC y objetivo
        $filtrados = $desafios->filter(function ($desafio) use ($imc, $objetivo) {
            if ($objetivo === 'bajar peso') {
                return $desafio->categoria === 'actividad' && $desafio->tipo_desafio === 'diario';
            } elseif ($objetivo === 'ganar masa') {
                return $desafio->categoria === 'alimentacion';
            } elseif ($imc >= 25) {
                return $desafio->categoria === 'actividad';
            } elseif ($imc < 18.5) {
                return $desafio->categoria === 'alimentacion';
            }
            return true; // por defecto
        });

        return response()->json([
            'usuario_id' => $usuario->id_usuario,
            'imc' => $imc,
            'objetivo' => $objetivo,
            'desafios_sugeridos' => $filtrados->values()
        ]);
    }
}
