<?php

namespace App\Http\Controllers;

use App\Models\Estacionamento;
use App\Models\Settings;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

class PembayaranController extends Controller
{
    public function print(Request $request)
    {
        $car = $request->all();
        $settings = Settings::find(1);

        Log::info('Dados recebidos do carro:', $car);

        // Formatar a data para o formato Y-m-d H:i:s
        $dataHora = Carbon::createFromFormat('d/m/Y H:i:s', $car['data'] . ' ' . $car['hora'])
            ->format('Y-m-d H:i:s');

        // Configurar dados para o PDF
        $data = [
            'settings' => $settings,
            'car' => (object)[
                'id' => $car['id'] ?? null,
                'entrada' => $dataHora,
                'placa' => $car['placa'] ?? '',
                'modelo' => $car['modelo'] ?? '',
                'tipo_car' => $car['tipo_car'] ?? ''
            ],
            'price' => (object)[
                'valorHora' => $car['valorHora'] ?? 0,
                'valorMinimo' => $car['valorMinimo'] ?? 0
            ]
        ];

        Log::info('Dados formatados para o PDF:', $data);

        try {
            // Gerar PDF com tamanho de papel personalizado para impressora térmica
            $pdf = PDF::loadView('layouts.PDF.thermal_ticket', $data)
                      ->setPaper([0, 0, 280, 600]) // Ajustando largura para 98mm (280pt) e altura para 600pt
                      ->setOptions([
                          'dpi' => 203,
                          'defaultFont' => 'sans-serif',
                          'isHtml5ParserEnabled' => true,
                          'isRemoteEnabled' => true,
                          'margin-top' => 5,
                          'margin-right' => 5,
                          'margin-bottom' => 5,
                          'margin-left' => 5
                      ]);

            return $pdf->stream('ticket.pdf');
        } catch(Exception $e) {
            Log::error("Não foi possível gerar o PDF: " . $e->getMessage());
            return redirect()->route('cars.index')->with('delete_car', 'Não foi possível gerar o PDF: ' . $e->getMessage());
        }
    }

    public function printTicket(Request $request)
    {
        $car = $request->all();
        $estacionamento = Settings::find(1);

        // Configurar dados para o ticket
        $data = [
            'empresa' => $estacionamento->nome_da_empresa,
            'endereco' => $estacionamento->endereco,
            'cnpj_cpf' => $estacionamento->cnpj_Cpf_da_empresa,
            'telefone' => $estacionamento->telefone_da_empresa,
            'data' => $car['data'],
            'hora' => $car['hora'],
            'tipo_car' => $car['tipo_car'],
            'placa' => $car['placa'],
            'entrada' => $car['data'] . ' ' . $car['hora']
        ];

        try {
            // Conectando à impressora
            // Use o conector apropriado para sua impressora: WindowsPrintConnector, FilePrintConnector ou NetworkPrintConnector
            $connector = new WindowsPrintConnector("PDF Architect 9");
            // Para impressora de rede
            // $connector = new NetworkPrintConnector("192.168.1.100", 9100);

            $printer = new Printer($connector);

            // Imprimindo o ticket
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("------------------------------------------\n");
            $printer->text($data['empresa'] . "\n");
            $printer->text("Endereço: " . $data['endereco'] . "\n");
            $printer->text("CNPJ: " . $data['cnpj_cpf'] . "\n");
            $printer->text("Telefone: " . $data['telefone'] . "\n");
            $printer->text("Data: " . $data['data'] . "   Hora: " . $data['hora'] . "\n");
            $printer->text("------------------------------------------\n");
            $printer->text("Veículo:  " . $data['tipo_car'] . "\n");
            $printer->text("Placa:    " . $data['placa'] . "\n");
            $printer->text("Entrada:  " . $data['data'] . "   Hora: " . $data['hora'] . "\n");
            $printer->text("------------------------------------------\n");
            $printer->text("Guarde este ticket consigo.\n");
            $printer->text("Não deixe-o no interior do veículo.\n");
            $printer->text("O veículo será entregue ao portador.\n");
            $printer->text("Seg a Sex das 08:00 as 19:30\n");
            $printer->text("Sábado das 08:00 as 18:00\n");
            $printer->text("\n");
            $printer->cut();

            // Fechar a conexão com a impressora
            $printer->close();
            Log::info("Realizando a impressão nesta impressora: ");
            return redirect()->route('cars.index')->with('create', 'Ticket Impresso com sucesso!');
        } catch (Exception $e) {
            Log::error("Não foi possível imprimir nesta impressora: " . $e->getMessage());
            return redirect()->route('cars.index')->with('delete_car', 'Não foi possível imprimir nesta impressora: ' . $e->getMessage());
        }
    }
}
