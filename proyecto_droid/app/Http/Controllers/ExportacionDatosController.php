<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegistroActividadFisica;
use App\Models\RegistroConsumo;
use App\Models\ExportacionDatos;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;

class ExportacionDatosController extends Controller
{
    /**
     * Exportar registros de actividad física a PDF
     */
    public function exportarActividadPDF(Request $request): JsonResponse
    {
        try {
            $idUsuario = $request->id_usuario ?? 1;
            $registros = RegistroActividadFisica::with(['tipoEjercicio', 'rutinaEjercicio'])
                ->where('id_usuario', $idUsuario)
                ->orderBy('fecha_actividad', 'desc')
                ->orderBy('hora_inicio', 'desc')
                ->get();

            $totalCaloriasQuemadas = $registros->sum('calorias_quemadas');
            $totalMinutos = $registros->sum('duracion_minutos');
            $totalRegistros = $registros->count();

            $html = view('pdf.registros-actividad', [
                'registros' => $registros,
                'totalCaloriasQuemadas' => $totalCaloriasQuemadas,
                'totalMinutos' => $totalMinutos,
                'totalRegistros' => $totalRegistros,
                'fechaExportacion' => now()->format('d/m/Y H:i:s')
            ])->render();

            $pdf = Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'portrait');

            $filename = 'registros_actividad_' . now()->format('Y-m-d_H-i-s') . '.pdf';
            $pdfContent = $pdf->output();

            ExportacionDatos::create([
                'id_usuario' => $idUsuario,
                'tipo_exportacion' => 'actividad_pdf',
                'fecha_exportacion' => now(),
                'fecha_inicio_datos' => $registros->min('fecha_actividad') ?: now()->toDateString(),
                'fecha_fin_datos' => $registros->max('fecha_actividad') ?: now()->toDateString(),
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
            \Log::error("Error al exportar PDF de actividad: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al generar PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exportar registros de consumo a PDF
     */
    public function exportarConsumoPDF(Request $request): JsonResponse
    {
        try {
            $idUsuario = $request->id_usuario ?? 1;
            $registros = RegistroConsumo::with(['plato.lugar', 'plato.categoria'])
                ->where('id_usuario', $idUsuario)
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

            $pdf = Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'portrait');

            $filename = 'registros_consumo_' . now()->format('Y-m-d_H-i-s') . '.pdf';
            $pdfContent = $pdf->output();

            ExportacionDatos::create([
                'id_usuario' => $idUsuario,
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
