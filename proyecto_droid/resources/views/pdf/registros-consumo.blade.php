<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Registros de Consumo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 14px;
            color: #666;
        }
        .summary {
            background-color: #f5f5f5;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .summary-item {
            display: inline-block;
            margin-right: 20px;
        }
        .summary-label {
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #333;
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 6px 8px;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Registros de Consumo Alimentario</div>
        <div class="subtitle">Reporte generado el {{ $fechaExportacion }}</div>
    </div>

    <div class="summary">
        <div class="summary-item">
            <span class="summary-label">Total de registros:</span> {{ $totalRegistros }}
        </div>
        <div class="summary-item">
            <span class="summary-label">Total de calorías:</span> {{ number_format($totalCalorias) }} kcal
        </div>
    </div>

    @if($registros->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Plato</th>
                    <th>Porciones</th>
                    <th>Calorías</th>
                    <th>Valoración</th>
                    <th>Comentario</th>
                </tr>
            </thead>
            <tbody>
                @foreach($registros as $registro)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($registro->fecha_consumo)->format('d/m/Y') }}</td>
                        <td>{{ $registro->hora_consumo }}</td>
                        <td>
                            {{ $registro->plato->nombre }}
                            @if($registro->plato->lugar)
                                <br><small>({{ $registro->plato->lugar->nombre }})</small>
                            @endif
                        </td>
                        <td>{{ $registro->porciones }}</td>
                        <td>{{ $registro->calorias_totales }} kcal</td>
                        <td>
                            @if($registro->valoracion)
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $registro->valoracion)
                                        ★
                                    @else
                                        ☆
                                    @endif
                                @endfor
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $registro->comentario ?: '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            No hay registros de consumo para mostrar.
        </div>
    @endif

    <div class="footer">
        <p>Este reporte fue generado automáticamente por el sistema de seguimiento nutricional.</p>
        <p>Página 1 de 1</p>
    </div>
</body>
</html> 