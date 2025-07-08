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

class RegistroConsumoController extends Controller
{
    /**
     * Obtener todos los registros de consumo del usuario autenticado
     */
    public function index(): JsonResponse
    {
        $registros = RegistroConsumo::with(['plato.lugar', 'plato.categoria'])
            ->where('id_usuario', 1) // Fijo para pruebas sin login
            ->orderBy('fecha_consumo', 'desc')
            ->orderBy('hora_consumo', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $registros
        ]);
    }

    /**
     * Obtener un registro específico
     */
    public function show($id): JsonResponse
    {
        $registro = RegistroConsumo::with(['plato.lugar', 'plato.categoria'])
            ->where('id_usuario', 1)
            ->find($id);

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
     * Crear un nuevo registro de consumo
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_plato' => 'required|exists:platos,id_plato',
            'fecha_consumo' => 'required|date',
            'hora_consumo' => 'required|date_format:H:i',
            'porciones' => 'required|numeric|min:0.1|max:10',
            'valoracion' => 'nullable|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $plato = Plato::find($request->id_plato);
        $calorias_totales = $plato->calorias_por_porcion * $request->porciones;

        $registro = RegistroConsumo::create([
            'id_usuario' => 1, // Asignación fija para pruebas sin login
            'id_plato' => $request->id_plato,
            'fecha_consumo' => $request->fecha_consumo,
            'hora_consumo' => $request->hora_consumo,
            'porciones' => $request->porciones,
            'calorias_totales' => $calorias_totales,
            'valoracion' => $request->valoracion,
            'comentario' => $request->comentario,
            'puntos_obtenidos' => 10 // Puntos por registrar consumo
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registro de consumo creado exitosamente',
            'data' => $registro->load(['plato.lugar', 'plato.categoria'])
        ], 201);
    }

    /**
     * Actualizar un registro de consumo
     */
    public function update(Request $request, $id): JsonResponse
    {
        $registro = RegistroConsumo::where('id_usuario', 1)->find($id);

        if (!$registro) {
            return response()->json([
                'success' => false,
                'message' => 'Registro no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_plato' => 'sometimes|exists:platos,id_plato',
            'fecha_consumo' => 'sometimes|date',
            'hora_consumo' => 'sometimes|date_format:H:i',
            'porciones' => 'sometimes|numeric|min:0.1|max:10',
            'valoracion' => 'nullable|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only(['fecha_consumo', 'hora_consumo', 'porciones', 'valoracion', 'comentario']);
        
        if ($request->has('id_plato')) {
            $data['id_plato'] = $request->id_plato;
            $plato = Plato::find($request->id_plato);
            $data['calorias_totales'] = $plato->calorias_por_porcion * ($request->porciones ?? $registro->porciones);
        }

        $registro->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Registro de consumo actualizado exitosamente',
            'data' => $registro->load(['plato.lugar', 'plato.categoria'])
        ]);
    }

    /**
     * Eliminar un registro de consumo
     */
    public function destroy($id): JsonResponse
    {
        try {
            \Log::info("Intentando eliminar registro de consumo con ID: $id");
            
            $registro = RegistroConsumo::where('id_usuario', 1)->find($id);
            
            \Log::info("Registro encontrado: " . ($registro ? 'Sí' : 'No'));

            if (!$registro) {
                \Log::warning("Registro no encontrado con ID: $id");
                return response()->json([
                    'success' => false,
                    'message' => 'Registro no encontrado'
                ], 404);
            }

            \Log::info("Eliminando registro con ID: " . $registro->id_consumo);
            $registro->delete();
            \Log::info("Registro eliminado exitosamente");

            return response()->json([
                'success' => true,
                'message' => 'Registro de consumo eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            \Log::error("Error al eliminar registro de consumo: " . $e->getMessage());
            \Log::error("Stack trace: " . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de consumo del usuario
     */
    public function estadisticas(): JsonResponse
    {
        $hoy = now()->toDateString();
        $semana = now()->subDays(7)->toDateString();
        $mes = now()->subDays(30)->toDateString();

        $estadisticas = [
            'hoy' => [
                'total_calorias' => RegistroConsumo::where('id_usuario', 1)
                    ->where('fecha_consumo', $hoy)
                    ->sum('calorias_totales'),
                'total_registros' => RegistroConsumo::where('id_usuario', 1)
                    ->where('fecha_consumo', $hoy)
                    ->count()
            ],
            'semana' => [
                'total_calorias' => RegistroConsumo::where('id_usuario', 1)
                    ->whereBetween('fecha_consumo', [$semana, $hoy])
                    ->sum('calorias_totales'),
                'total_registros' => RegistroConsumo::where('id_usuario', 1)
                    ->whereBetween('fecha_consumo', [$semana, $hoy])
                    ->count()
            ],
            'mes' => [
                'total_calorias' => RegistroConsumo::where('id_usuario', 1)
                    ->whereBetween('fecha_consumo', [$mes, $hoy])
                    ->sum('calorias_totales'),
                'total_registros' => RegistroConsumo::where('id_usuario', 1)
                    ->whereBetween('fecha_consumo', [$mes, $hoy])
                    ->count()
            ]
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
