<?php

namespace App\Http\Controllers;

use App\Models\HistorialPesoImc;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class HistorialPesoImcController extends Controller
{
    /**
     * Obtener historial de un usuario
     */
    public function porUsuario(int $idUsuario): JsonResponse
    {
        $historial = HistorialPesoImc::with('usuario')
            ->where('id_usuario', $idUsuario)
            ->orderBy('fecha_registro', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $historial
        ]);
    }

    /**
     * Obtener un registro específico
     */
    public function show(int $id): JsonResponse
    {
        $registro = HistorialPesoImc::with('usuario')->find($id);

        if (!$registro) {
            return response()->json([
                'success' => false,
                'message' => 'Registro no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $registro
        ]);
    }

    /**
     * Crear un nuevo registro
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_usuario' => 'required|exists:usuarios,id_usuario',
            'fecha_registro' => 'required|date',
            'peso_kg' => 'required|numeric|min:20|max:300',
            'altura_cm' => 'required|integer|min:100|max:250',
            'notas' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        $registro = HistorialPesoImc::create([
            'id_usuario' => $request->id_usuario,
            'fecha_registro' => $request->fecha_registro,
            'peso_kg' => $request->peso_kg,
            'altura_cm' => $request->altura_cm,
            'notas' => $request->notas
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registro creado exitosamente',
            'data' => $registro->load('usuario')
        ], 201);
    }

    /**
     * Actualizar un registro
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $registro = HistorialPesoImc::find($id);

        if (!$registro) {
            return response()->json([
                'success' => false,
                'message' => 'Registro no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'fecha_registro' => 'date',
            'peso_kg' => 'numeric|min:20|max:300',
            'altura_cm' => 'integer|min:100|max:250',
            'notas' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        $registro->update($request->only(['fecha_registro', 'peso_kg', 'altura_cm', 'notas']));

        return response()->json([
            'success' => true,
            'message' => 'Registro actualizado exitosamente',
            'data' => $registro->load('usuario')
        ]);
    }

    /**
     * Eliminar un registro
     */
    public function destroy(int $id): JsonResponse
    {
        $registro = HistorialPesoImc::find($id);

        if (!$registro) {
            return response()->json([
                'success' => false,
                'message' => 'Registro no encontrado'
            ], 404);
        }

        $registro->delete();

        return response()->json([
            'success' => true,
            'message' => 'Registro eliminado exitosamente'
        ]);
    }

    /**
     * Obtener último registro
     */
    public function ultimoRegistro(int $idUsuario): JsonResponse
    {
        $registro = HistorialPesoImc::with('usuario')
            ->where('id_usuario', $idUsuario)
            ->orderBy('fecha_registro', 'desc')
            ->first();

        if (!$registro) {
            return response()->json([
                'success' => false,
                'message' => 'No hay registros para este usuario'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $registro
        ]);
    }

    /**
     * Obtener registros por rango de fechas
     */
    public function porRangoFechas(Request $request, int $idUsuario): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Fechas inválidas',
                'errors' => $validator->errors()
            ], 422);
        }

        $registros = HistorialPesoImc::with('usuario')
            ->where('id_usuario', $idUsuario)
            ->whereBetween('fecha_registro', [$request->fecha_inicio, $request->fecha_fin])
            ->orderBy('fecha_registro', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $registros
        ]);
    }

    /**
     * Obtener estadísticas de peso e IMC
     */
    public function estadisticas(int $idUsuario): JsonResponse
    {
        $registros = HistorialPesoImc::where('id_usuario', $idUsuario)
            ->orderBy('fecha_registro', 'asc')
            ->get();

        if ($registros->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No hay registros para calcular estadísticas'
            ], 404);
        }

        $primerRegistro = $registros->first();
        $ultimoRegistro = $registros->last();
        $pesoMaximo = $registros->max('peso_kg');
        $pesoMinimo = $registros->min('peso_kg');
        $imcMaximo = $registros->max('imc');
        $imcMinimo = $registros->min('imc');

        $diferenciaPeso = $ultimoRegistro->peso_kg - $primerRegistro->peso_kg;
        $diferenciaImc = $ultimoRegistro->imc - $primerRegistro->imc;

        $estadisticas = [
            'total_registros' => $registros->count(),
            'primer_registro' => $primerRegistro,
            'ultimo_registro' => $ultimoRegistro,
            'peso_actual' => $ultimoRegistro->peso_kg,
            'imc_actual' => $ultimoRegistro->imc,
            'categoria_imc_actual' => $ultimoRegistro->categoria_imc,
            'peso_maximo' => $pesoMaximo,
            'peso_minimo' => $pesoMinimo,
            'imc_maximo' => $imcMaximo,
            'imc_minimo' => $imcMinimo,
            'diferencia_peso_total' => round($diferenciaPeso, 2),
            'diferencia_imc_total' => round($diferenciaImc, 2),
            'promedio_peso' => round($registros->avg('peso_kg'), 2),
            'promedio_imc' => round($registros->avg('imc'), 2),
            'por_categoria_imc' => $registros->groupBy('categoria_imc')->map(function($grupo) {
                return [
                    'cantidad' => $grupo->count(),
                    'porcentaje' => round(($grupo->count() / $registros->count()) * 100, 2)
                ];
            })
        ];

        return response()->json([
            'success' => true,
            'data' => $estadisticas
        ]);
    }

    /**
     * Obtener progreso de peso
     */
    public function progresoPeso(int $idUsuario, int $dias = 30): JsonResponse
    {
        $fechaLimite = now()->subDays($dias);

        $registros = HistorialPesoImc::where('id_usuario', $idUsuario)
            ->where('fecha_registro', '>=', $fechaLimite)
            ->orderBy('fecha_registro', 'asc')
            ->get();

        if ($registros->count() < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Se necesitan al menos 2 registros para calcular el progreso'
            ], 400);
        }

        $primerRegistro = $registros->first();
        $ultimoRegistro = $registros->last();
        $diferenciaPeso = $ultimoRegistro->peso_kg - $primerRegistro->peso_kg;
        $diferenciaDias = $primerRegistro->fecha_registro->diffInDays($ultimoRegistro->fecha_registro);
        $promedioCambioDiario = $diferenciaDias > 0 ? $diferenciaPeso / $diferenciaDias : 0;

        $progreso = [
            'periodo_dias' => $dias,
            'registros_analizados' => $registros->count(),
            'peso_inicial' => $primerRegistro->peso_kg,
            'peso_final' => $ultimoRegistro->peso_kg,
            'diferencia_total' => round($diferenciaPeso, 2),
            'diferencia_dias' => $diferenciaDias,
            'promedio_cambio_diario' => round($promedioCambioDiario, 3),
            'tendencia' => $diferenciaPeso > 0 ? 'aumento' : ($diferenciaPeso < 0 ? 'disminucion' : 'estable'),
            'registros' => $registros
        ];

        return response()->json([
            'success' => true,
            'data' => $progreso
        ]);
    }
}
