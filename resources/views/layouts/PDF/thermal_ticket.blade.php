<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ticket de Estacionamento</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 10px;
            width: 98mm; /* Largura padrão para impressora térmica */
            font-size: 12px;
        }
        .ticket-header {
            text-align: center;
            margin-bottom: 10px;
        }
        .ticket-header img {
            display: block;
            max-width: 70mm;
            height: auto;
            margin: 0 auto 10px auto;
        }
        .ticket-header h2 {
            margin: 5px 0;
            font-size: 16px;
            font-weight: bold;
        }
        .company-info {
            text-align: center;
            font-size: 12px;
            margin-bottom: 10px;
            width: 100%;
        }
        .company-info p {
            margin: 3px 0;
        }
        .ticket-info {
            font-size: 12px;
            margin-bottom: 10px;
            padding: 0 5px;
        }
        .ticket-info p {
            margin: 5px 0;
        }
        .ticket-footer {
            text-align: center;
            font-size: 11px;
            margin-top: 10px;
            border-top: 1px dashed #000;
            padding-top: 5px;
            width: 100%;
        }
        .ticket-footer p {
            margin: 3px 0;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 10px auto;
            width: 100%;
        }
        strong {
            font-weight: bold;
        }
        .logo-container {
            width: 100%;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="ticket-header">
        <div class="logo-container">
            @if(file_exists(public_path('img/LogoEstacionamento.png')))
                <img src="{{ public_path('img/LogoEstacionamento.png') }}" alt="Logo">
            @endif
        </div>
        <h2>{{ $settings->nome_da_empresa ?? 'Estacionamento' }}</h2>
    </div>

    <div class="company-info">
        <p>{{ $settings->endereco ?? '' }}</p>
        <p>{{ $settings->cidade ?? '' }} - {{ $settings->estado ?? '' }}</p>
        <p>CNPJ/CPF: {{ $settings->cnpj_cpf_da_empresa ?? '' }}</p>
        <p>Tel: {{ $settings->telefone_da_empresa ?? '' }}</p>
    </div>

    <div class="divider"></div>

    <div class="ticket-info">
        <p><strong>Ticket Nº:</strong> {{ str_pad($car->id ?? 'N/A', 6, '0', STR_PAD_LEFT) }}</p>
        <p><strong>Data/Hora Entrada:</strong> {{ isset($car->entrada) ? Carbon\Carbon::parse($car->entrada)->format('d/m/Y H:i:s') : now()->format('d/m/Y H:i:s') }}</p>
        <p><strong>Placa:</strong> {{ strtoupper($car->placa ?? 'N/A') }}</p>
        <p><strong>Modelo:</strong> {{ $car->modelo ?? 'N/A' }}</p>
        <p><strong>Tipo:</strong> {{ ucfirst($car->tipo_car ?? 'N/A') }}</p>
    </div>

    <div class="divider"></div>

    <div class="ticket-info">
        <p><strong>Valor por Hora:</strong> R$ {{ number_format($price->valorHora ?? 0, 2, ',', '.') }}</p>
        <p><strong>Valor Mínimo:</strong> R$ {{ number_format($price->valorMinimo ?? 0, 2, ',', '.') }}</p>
    </div>

    <div class="divider"></div>

    <div class="ticket-footer">
        <p>Guarde este ticket</p>
        <p>Não deixe-o no interior do veículo</p>
        <p>O veículo será entregue ao portador</p>
        <p>Seg a Sex das 08:00 as 19:30</p>
        <p>Sábado das 08:00 as 18:00</p>
        <p>{{ date('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html> 