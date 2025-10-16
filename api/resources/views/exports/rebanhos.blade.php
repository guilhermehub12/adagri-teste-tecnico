<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Rebanhos</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #2c3e50;
        }

        .header h1 {
            font-size: 24px;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .header .subtitle {
            font-size: 12px;
            color: #7f8c8d;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        thead {
            background-color: #34495e;
            color: white;
        }

        thead th {
            padding: 12px 8px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        tbody td {
            padding: 10px 8px;
            border-bottom: 1px solid #ecf0f1;
        }

        tbody tr:hover {
            background-color: #f8f9fa;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #2c3e50;
            text-align: center;
            font-size: 10px;
            color: #7f8c8d;
        }

        .total-section {
            margin-top: 20px;
            padding: 15px;
            background-color: #ecf0f1;
            border-radius: 5px;
        }

        .total-section strong {
            font-size: 14px;
            color: #2c3e50;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #95a5a6;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Relatório de Rebanhos</h1>
        <p class="subtitle">Gerado em: {{ now()->format('d/m/Y H:i:s') }}</p>
        @if($filtros)
            <p class="subtitle">
                Filtros aplicados:
                @if(isset($filtros['especie']))
                    Espécie: {{ $filtros['especie'] }}
                @endif
                @if(isset($filtros['propriedade_id']))
                    @if(isset($filtros['especie'])) | @endif
                    Propriedade ID: {{ $filtros['propriedade_id'] }}
                @endif
                @if(isset($filtros['produtor_id']))
                    @if(isset($filtros['especie']) || isset($filtros['propriedade_id'])) | @endif
                    Produtor ID: {{ $filtros['produtor_id'] }}
                @endif
            </p>
        @endif
    </div>

    @if($rebanhos->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 8%">ID</th>
                    <th style="width: 15%">Espécie</th>
                    <th style="width: 10%">Quantidade</th>
                    <th style="width: 15%">Finalidade</th>
                    <th style="width: 25%">Propriedade</th>
                    <th style="width: 17%">Município</th>
                    <th style="width: 10%">Data Atual.</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rebanhos as $rebanho)
                    <tr>
                        <td>{{ $rebanho->id }}</td>
                        <td><strong>{{ $rebanho->especie }}</strong></td>
                        <td>{{ number_format($rebanho->quantidade, 0, ',', '.') }}</td>
                        <td>{{ $rebanho->finalidade ?? 'N/A' }}</td>
                        <td>{{ $rebanho->propriedade->nome ?? 'N/A' }}</td>
                        <td>{{ $rebanho->propriedade->municipio ?? 'N/A' }}</td>
                        <td>{{ $rebanho->data_atualizacao ? $rebanho->data_atualizacao->format('d/m/Y') : 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section">
            <strong>Total de animais:</strong> {{ number_format($rebanhos->sum('quantidade'), 0, ',', '.') }} animais
            <br>
            <strong>Total de rebanhos:</strong> {{ $rebanhos->count() }} rebanho(s)
        </div>
    @else
        <div class="empty-state">
            <p>Nenhum rebanho encontrado com os filtros aplicados.</p>
        </div>
    @endif

    <div class="footer">
        <p>Sistema de Gestão Agropecuária - ADAGRI</p>
        <p>Relatório gerado automaticamente</p>
    </div>
</body>
</html>
