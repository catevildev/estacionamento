<?php

namespace App\Http\Controllers;

use App\Models\Cars;
use App\Models\PriceCar;
use App\Models\PriceMotorcycle;
use App\Models\PriceTruck;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CarsController extends Controller
{

    public function price($id)
    {
        $car = Cars::findOrFail($id);

        // Se o carro já foi finalizado, retornar o preço salvo
        if ($car->status === 'finalizado') {
            return $car->preco;
        }

        // Calcular o tempo de permanência
        $entrada = Carbon::parse($car->created_at);
        $saida = Carbon::now();
        $tempo = $saida->diff($entrada);

        // Determinar o modelo de preço com base no tipo do veículo
        $precos = [
            'carro' => PriceCar::first(),
            'moto' => PriceMotorcycle::first(),
            'caminhonete' => PriceTruck::first()
        ];

        if (!isset($precos[$car->tipo_car])) {
            Log::warning("Tipo de veículo desconhecido: {$car->tipo_car}");
            return 0;
        }

        $price = $precos[$car->tipo_car];

        // Calcular o valor com base no tempo e nos preços
        return $this->calculatePrice($tempo, $price, $id);
    }

    private function calculatePrice($tempo, $price, $id)
    {
        $valor = 0;

        // Tempo de permanência
        $minuto = $tempo->i;
        $hora = $tempo->h;
        $dia = $tempo->d;
        $mes = $tempo->m;

        // Tarifas
        $valorMinimo = $price->valorMinimo;
        $valorHora = $price->valorHora;
        $valorDiaria = $price->valorDiaria;
        $taxaMensal = $price->taxaMensal;
        $taxaAdicional = $price->taxaAdicional;

        // Se o tempo for maior que um mês, aplica taxa mensal
        if ($mes >= 1) {
            $valor += $mes * $taxaMensal;
        }

        // Se passou de um dia, cobra diárias e considera horas adicionais
        if ($dia >= 1) {
            $valor += $dia * $valorDiaria;

            // Se houver horas adicionais, cobra a taxa de hora proporcional
            if ($hora > 0) {
                $valor += ($hora * $valorHora) + ($hora > 1 ? $taxaAdicional : 0);
            }
        }
        // Se não passou de um dia, cobra por hora ou tarifa mínima
        else {
            if ($hora >= 1) {
                $valor += ($hora * $valorHora) + ($hora > 1 ? $taxaAdicional : 0);
            } elseif ($minuto <= 30) {
                $valor += $valorMinimo;
            } else {
                $valor += $valorHora;
            }
        }

        Log::info("Cálculo de preço para o veículo $id: $mes mês(es), $dia dia(s), $hora hora(s), $minuto minuto(s) - Total: R$ $valor");

        return $valor;
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [];

        // Pega o parâmetro de pesquisa da URL, se houver
        $search = $request->get('search', '');

        // Realiza a busca filtrada por 'placa' com base na pesquisa, se houver
        // $cars = Cars::where('status', '=', null)
        //             ->where('placa', 'LIKE', '%' . $search . '%')
        //             ->orderBy('created_at', 'desc');
        $cars = Cars::orderBy('created_at', 'asc')->get();

        // Calcula o preço para cada carro
        foreach ($cars as $car) {
            $car['price'] = $this->price($car->id);
        }

        // Preserva o parâmetro de pesquisa na URL da paginação
        // $cars->appends(['search' => $search]);

        $data['cars'] = $cars;
        return view('cars', $data);
    }

    public function search(Request $req)
    {
        $data = $req->only([
            'search'
        ]);

        if (empty($data['search'])) {
            return redirect(route('cars.index'))->withInput();
        }

        $validator = Validator::make($data, [
            'search' => ['string', 'max:8'],
        ], [
            'max' => 'O campo :attribute deve ter no máximo :max caracteres.',
        ]);

        if ($validator->fails()) {
            Log::error('Failed search cars', ['validator' => $validator]);
            return redirect(route('cars.index'))->withErrors($validator)->withInput();
        }

        // Obter a consulta digitada pelo usuário
        $search = $data['search'];

        // Realizar a lógica de pesquisa no seu modelo ou na fonte de dados desejada
        $cars = Cars::where('placa', 'LIKE', '%' . $search . '%')->where('status', '=', null);

        if ($cars->isEmpty()) {
            Log::warning("Nenhum carro encontrado para a pesquisa: $search");
            return redirect(route('cars.index'))->withErrors(['error' => "Carros Não Localizados contendo $search na Placa"]);
        }

        foreach ($cars as $car) {
            $car['price'] = $this->price($car->id);
        }

        // Preserva o parâmetro de pesquisa na URL de paginação
        // $cars->appends(['search' => $search]);

        $data['cars'] = $cars;

        session()->flash('create', "Carros Localizados com sucesso contendo $search na Placa");

        return view('cars', $data)->with('create', "Carros Localizados contendo $search na Placa");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('cars_add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->only([
            'modelo',
            'placa',
            'entrada',
            'tipo_car'
        ]);

        $validator = Validator::make($data, [
            'modelo' => ['required', 'string', 'max:64'],
            'placa' => ['required', 'string', 'max:8', 'regex:/^[A-Z0-9]{3}-[A-Z0-9]{4}$/i'],
            'entrada' => ['required', 'date'],
            'tipo_car' => ['required', 'string', Rule::in(['carro', 'moto', 'caminhonete'])],
        ], [
            'placa.regex' => 'A placa deve estar no formato AAA-1234.',
        ]);

        if ($validator->fails()) {
            return redirect(route('cars.create'))->withErrors($validator)->withInput();
        }

        $car = new Cars();
        $car->modelo = $data['modelo'];
        $car->placa = $data['placa'];
        $car->entrada = Carbon::parse($data['entrada'])->format('Y-m-d H:i:s');
        $car->tipo_car = $data['tipo_car'];
        $car->preco = 0;
        $car->save();

        Log::info("Carro Adicionado com sucesso: " . $car->placa);

        return redirect(route('cars.index'))->with('create', 'Carro adicionado com sucesso');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $car = Cars::find($id);

        $car['price'] = $this->price($car->id);
        $car['entrada'] = new DateTime($car->created_at);

        if ($car) {
            return response()->json(['success' => true, 'html' => view('modal_cars_edit', ['car' => $car])->render()]);
        }
        Log::error("Carro não encontrado com o ID: $id");
        return redirect(route('cars.index'));
    }

    public function showModal($id)
    {
        $car = Cars::find($id);

        date_default_timezone_set('America/Sao_Paulo');
        $saida = new DateTime();
        $entrada = new DateTime($car->created_at);
        $tempo = date_diff($entrada, $saida);

        $hora = $tempo->h;
        $minuto = $tempo->i;
        $dia = $tempo->d;
        $mes = $tempo->m;

        $car['price'] = $this->price($id);
        $car['entrada'] = Carbon::parse($car->created_at)->format('d/m/Y H:i:s');
        $car['horaT'] = $hora;
        $car['minutoT'] = $minuto;
        $car['diaT'] = $dia;
        $car['mesT'] = $mes;

        Log::info("Modal do carro $id exibido com sucesso");

        return response()->json($car);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $car = Cars::find($id);

        if ($car) {
            $car['preco2'] = number_format($car['preco'], 2, ',', '.');
            $car['price'] = $this->price($car->id);
            return view('cars_edit', ['car' => $car]);
        }

        return redirect(route('cars.index'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $car = Cars::find($id);
        if ($car) {
            // Calcula o preço final antes de finalizar
            $precoFinal = $this->price($car->id);
            
            $car->status = 'finalizado';
            $car->saida = now();
            $car->preco = $precoFinal;
            $car->save();
            
            Log::info("Carro finalizado com sucesso: {$car->placa} - Preço final: R$ {$precoFinal}");
            return response()->json(['success' => true]);
        }
        
        Log::error("Erro ao finalizar carro ID: $id");
        return response()->json(['success' => false], 404);
    }
}
