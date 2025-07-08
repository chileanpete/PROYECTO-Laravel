<?php

namespace App\Http\Controllers;

use App\Models\RegistroConsumo;
use App\Models\Plato;
use App\Models\ExportacionDatos;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Usuario;

class RegistroConsumoController extends Controller
{
    /**
     * Obtener todos los registros de consumo
     */
    public function index(Request $request): JsonResponse
    {
        $query = RegistroConsumo::with([
            'usuario', 'plato.lugar', 'plato.categoria'
        ]);

        // Filtrar por usuario
        if ($request->has('id_usuario')) {
            $query->where('id_usuario', $request->id_usuario);
        }

        // Filtrar por fecha
        if ($request->has('fecha_inicio')) {
            $query->where('fecha_consumo', '>=', $request->fecha_inicio);
        }

        if ($request->has('fecha_fin')) {
            $query->where('fecha_consumo', '<=', $request->fecha_fin);
        }

        // Filtrar por tipo de comida
        if ($request->has('tipo_comida')) {
            $query->where('tipo_comida', $request->tipo_comida);
        }

        $registros = $query->orderBy('fecha_consumo', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $registros
        ]);
    }

    /**
     * Obtener un registro específico
     */
    public function show(int $id): JsonResponse
    {
        $registro = RegistroConsumo::with([
            'usuario', 'plato.lugar', 'plato.categoria'
        ])->find($id);

        if (!$registro) {
            return response()->json([
                'success' => false,
                'message' => 'Registro de consumo no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $registro
        ]);
    }

    /**
     * Crear un nuevo registro de consumo
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_usuario' => 'required|exists:usuarios,id_usuario',
            'id_plato' => 'required|exists:platos,id_plato',
            'fecha_consumo' => 'required|date',
            'hora_consumo' => 'required|date_format:H:i',
            'tipo_comida' => 'required|in:desayuno,almuerzo,cena,refrigerio',
            'porcion_consumida' => 'required|numeric|min:0.1|max:10',
            'calorias_consumidas' => 'required|integer|min:0',
            'comentario' => 'nullable|string',
            'satisfaccion' => 'integer|min:1|max:5'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        $registro = RegistroConsumo::create([
            'id_usuario' => $request->id_usuario,
            'id_plato' => $request->id_plato,
            'fecha_consumo' => $request->fecha_consumo,
            'hora_consumo' => $request->hora_consumo,
            'tipo_comida' => $request->tipo_comida,
            'porcion_consumida' => $request->porcion_consumida,
            'calorias_consumidas' => $request->calorias_consumidas,
            'comentario' => $request->comentario,
            'satisfaccion' => $request->satisfaccion ?? 3
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registro de consumo creado exitosamente',
            'data' => $registro->load(['usuario', 'plato.lugar', 'plato.categoria'])
        ], 201);
    }

    /**
     * Actualizar un registro de consumo
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $registro = RegistroConsumo::find($id);

        if (!$registro) {
            return response()->json([
                'success' => false,
                'message' => 'Registro de consumo no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_plato' => 'exists:platos,id_plato',
            'fecha_consumo' => 'date',
            'hora_consumo' => 'date_format:H:i',
            'tipo_comida' => 'in:desayuno,almuerzo,cena,refrigerio',
            'porcion_consumida' => 'numeric|min:0.1|max:10',
            'calorias_consumidas' => 'integer|min:0',
            'comentario' => 'nullable|string',
            'satisfaccion' => 'integer|min:1|max:5'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        $registro->update($request->only([
            'id_plato', 'fecha_consumo', 'hora_consumo', 'tipo_comida',
            'porcion_consumida', 'calorias_consumidas', 'comentario', 'satisfaccion'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Registro de consumo actualizado exitosamente',
            'data' => $registro->load(['usuario', 'plato.lugar', 'plato.categoria'])
        ]);
    }

    /**
     * Eliminar un registro de consumo
     */
    public function destroy(int $id): JsonResponse
    {
        $registro = RegistroConsumo::find($id);

        if (!$registro) {
            return response()->json([
                'success' => false,
                'message' => 'Registro de consumo no encontrado'
            ], 404);
        }

        $registro->delete();

        return response()->json([
            'success' => true,
            'message' => 'Registro de consumo eliminado exitosamente'
        ]);
    }

    /**
     * Obtener estadísticas de consumo de un usuario
     */
    public function estadisticas(int $idUsuario): JsonResponse
    {
        $usuario = Usuario::find($idUsuario);

        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        $registros = $usuario->registrosConsumo();

        $estadisticas = [
            'total_consumos' => $registros->count(),
            'total_calorias' => $registros->sum('calorias_consumidas'),
            'promedio_satisfaccion' => round($registros->avg('satisfaccion'), 2),
            'consumos_ultima_semana' => $registros->where('fecha_consumo', '>=', now()->subWeek())->count(),
            'consumos_ultimo_mes' => $registros->where('fecha_consumo', '>=', now()->subMonth())->count(),
            'por_tipo_comida' => $registros->selectRaw('tipo_comida, COUNT(*) as total, SUM(calorias_consumidas) as calorias_totales')
                ->groupBy('tipo_comida')
                ->get(),
            'platos_mas_consumidos' => $registros->with('plato')
                ->selectRaw('id_plato, COUNT(*) as total')
                ->groupBy('id_plato')
                ->orderBy('total', 'desc')
                ->limit(5)
                ->get(),
            'promedio_calorias_diarias' => $registros->where('fecha_consumo', '>=', now()->subWeek())
                ->selectRaw('fecha_consumo, SUM(calorias_consumidas) as calorias_dia')
                ->groupBy('fecha_consumo')
                ->avg('calorias_dia')
        ];

        return response()->json([
            'success' => true,
            'data' => $estadisticas
        ]);
    }

    /**
     * Exportar registros de consumo a PDF
     */
    public function exportarPDF(): JsonResponse
    {
        try {
            $registros = RegistroConsumo::with(['plato.lugar', 'plato.categoria'])
                ->where('id_usuario', 1)
                ->orderBy('fecha_consumo', 'desc')
                ->orderBy('hora_consumo', 'desc')
                ->get();

            $totalCalorias = $registros->sum('calorias_totales');
            $totalRegistros = $registros->count();

            $html = view('pdf.registros-consumo', [
                'registros' => $registros,
                'totalCalorias' => $totalCalorias,
                'totalRegistros' => $totalRegistros,
                'fechaExportacion' => now()->format('d/m/Y H:i:s')
            ])->render();

            $pdf = PDF::loadHTML($html);
            $pdf->setPaper('A4', 'portrait');

            $filename = 'registros_consumo_' . now()->format('Y-m-d_H-i-s') . '.pdf';
            $pdfContent = $pdf->output();
            
            // Registrar la exportación en la base de datos
            ExportacionDatos::create([
                'id_usuario' => 1,
                'tipo_exportacion' => 'consumo_pdf',
                'fecha_exportacion' => now(),
                'fecha_inicio_datos' => $registros->min('fecha_consumo') ?: now()->toDateString(),
                'fecha_fin_datos' => $registros->max('fecha_consumo') ?: now()->toDateString(),
                'archivo_generado' => $filename,
                'compartido' => false
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'PDF generado exitosamente',
                'data' => [
                    'filename' => $filename,
                    'content' => base64_encode($pdfContent)
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error("Error al exportar PDF de consumo: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al generar PDF: ' . $e->getMessage()
            ], 500);
        }
    }
}
