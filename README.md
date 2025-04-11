# Sistema de Gerenciamento de Estacionamento

O Sistema de Gerenciamento de Estacionamento é uma plataforma web que fornece endpoints para gerenciar eficientemente um estacionamento. Este sistema permite que os usuários acessem informações sobre vagas disponíveis, registrem suas entradas e saídas da área de estacionamento e realizem pagamentos. A aplicação foi desenvolvida com base no framework Laravel, garantindo alto desempenho e confiabilidade.

![Selo de Desenvolvimento](http://img.shields.io/static/v1?label=STATUS&message=EM%20DESENVOLVIMENTO&color=GREEN&style=for-the-badge)

| :placard: Vitrine.Dev | [Visite Meu Perfil](https://cursos.alura.com.br/vitrinedev/igor01silveira) |
| -------------  | --- |
| :sparkles: Nome        | **Sistema de Gerenciamento de Estacionamento** |
| :label: Tecnologias | php, laravel |

![Sistema de Gerenciamento de Estacionamento](https://github.com/catevildev/estacionamento-deploy/blob/master/cover.png?raw=true#vitrinedev)

## Detalhes do Projeto
✔️ Técnicas e Tecnologias Utilizadas
- `Laravel`
- `PHP`
- `MySQL`
- `Visual Studio Code`
- `Orientação a Objetos`
- `PHPUnit`

## Rotas de Depoimentos

![Rotas de Depoimentos](https://github.com/catevildev)

# Documentação das Rotas do Estacionamento

## Redirecionamento para o Painel

Redireciona a página inicial para o painel.

- **URL:** `/`
- **Método:** `GET`
- **Resposta:**
  - **Código de Status:** 302 Found
  - **Localização:** `/painel`

## Painel

Exibe o painel de gerenciamento do estacionamento.

- **URL:** `/painel`
- **Método:** `GET`
- **Resposta:**
  - **Código de Status:** 200 OK
  - **Conteúdo:** Página HTML com informações e recursos do painel.

## Gerenciar Carros

Gerencia informações dos carros no sistema de estacionamento.

- **URL:** `/painel/cars`
- **Método:** `GET`
- **Resposta:**
  - **Código de Status:** 200 OK
  - **Conteúdo:** Página HTML listando carros registrados.

- **URL:** `/painel/cars/{car}`
- **Método:** `GET`
- **Resposta:**
  - **Código de Status:** 200 OK
  - **Conteúdo:** Página HTML exibindo detalhes específicos do carro.

- **URL:** `/painel/cars/showmodal/{car}`
- **Método:** `GET`
- **Resposta:**
  - **Código de Status:** 200 OK
  - **Conteúdo:** Modal HTML exibindo informações adicionais do carro.

## Imprimir Recibo de Pagamento

Permite imprimir um recibo de pagamento para um carro específico.

- **URL:** `/painel/pembayaran/print`
- **Método:** `POST`
- **Parâmetros de Dados:** Objeto JSON contendo dados do carro para o qual o recibo será impresso.
- **Resposta:**
  - **Código de Status:** 200 OK
  - **Conteúdo:** Página HTML com o recibo de pagamento pronto para impressão.

## Configurações

Gerencia as configurações do sistema no estacionamento.

- **URL:** `/painel/settings`
- **Método:** `GET`
- **Resposta:**
  - **Código de Status:** 200 OK
  - **Conteúdo:** Página HTML com configurações e opções do sistema.

---

**Códigos de Status:**

- 200 OK: A requisição foi bem-sucedida.
- 302 Found: A requisição foi redirecionada para outra página.

Observe que esta documentação assume o uso dos nomes de rotas e controladores fornecidos (por exemplo, `EstacionamentoController`, `CarsController`, `PembayaranController`, `SettingController`). Certifique-se de substituí-los pelos nomes reais usados em sua aplicação Laravel.

## Acesso ao Projeto

Você pode acessar o código-fonte completo do projeto no [GitHub](https://github.com/catevildev/estacionamento-deploy).

## Como Executar o Projeto

### Pré-requisitos

Antes de prosseguir, certifique-se de ter as seguintes tecnologias instaladas em seu ambiente de desenvolvimento:

- [PHP 7.4](https://www.php.net/)
- [Composer](https://getcomposer.org/)
- [MySQL 8.0](https://www.mysql.com/)
- [Visual Studio Code](https://code.visualstudio.com/) (ou sua IDE preferida)

### Passo 1: Clonar o Repositório

Clone o repositório do projeto para seu ambiente local usando o seguinte comando Git:

```bash
git clone https://github.com/catevildev/estacionamento-deploy.git
```

### Passo 2: Instalar as Dependências

Navegue até o diretório do projeto e instale as dependências do Composer executando:

```bash
cd estacionamento
composer install
```

### Passo 3: Configurar o Ambiente

Faça uma cópia do arquivo .env.example e renomeie para .env. Em seguida, atualize as configurações do banco de dados no arquivo .env com suas credenciais locais:

```bash
DB_CONNECTION=mysql
DB_HOST=seu-host
DB_PORT=seu-port
DB_DATABASE=seu-database
DB_USERNAME=seu-usuario
DB_PASSWORD=sua-senha
```

### Passo 4: Executar as Migrações

Com o ambiente configurado, crie as tabelas necessárias no banco de dados executando as migrações:

```bash
php artisan migrate
```

### Passo 5: Executar o Servidor

Por fim, inicie o servidor de desenvolvimento local com o comando:

```bash
php artisan serve
```

O projeto estará disponível em http://localhost:8000.

Agora você pode acessar e testar o Sistema de Gerenciamento de Estacionamento localmente.