# Guia de Instalação do Projeto
Este guia o ajudará a configurar e executar o projeto em uma máquina Windows.

## Pré-requisitos
Você precisará de um ambiente de desenvolvimento que inclua PHP, Git e Composer.
A forma mais fácil de obter tudo é usando o Laragon ou o XAMPP.

* **Laragon** (recomendado): Baixe em [https://laragon.org/](https://laragon.org/)
* **XAMPP**: Baixe em [https://www.apachefriends.org/pt_br/index.html](https://www.apachefriends.org/pt_br/index.html)

---

## Passo 1: Configuração do Ambiente
1.  **Inicie o Servidor:**
    * **Laragon:** Abra o Laragon e clique em `Start All`.
    * **XAMPP:** Abra o `XAMPP Control Panel` e inicie o `Apache` e o `MySQL`.

2.  **Abra o Terminal:**
    * No Laragon, clique no botão `Terminal`.
    * No XAMPP, use o `Git Bash` ou o `PowerShell` do Windows.

---

## Passo 2: Clonagem e Configuração do Projeto
1.  **Clone o Repositório:**
    ```bash
    git clone <URL_DO_SEU_REPOSITÓRIO>
    ```

2.  **Acesse a Pasta do Projeto:**
    ```bash
    cd <NOME_DA_PASTA_DO_PROJETO>
    ```

3.  **Instale as Dependências do Composer:**
    ```bash
    composer install
    ```

4.  **Crie o Arquivo de Ambiente:**
    ```bash
    cp .env.example .env
    ```

5.  **Gere a Chave da Aplicação:**
    ```bash
    php artisan key:generate
    ```

---

## Passo 3: Configuração e Migração do Banco de Dados SQLite
O SQLite já está incluso nas instalações do PHP do Laragon e XAMPP.

1.  **Configure o `.env` para usar SQLite:** Abra o arquivo `.env` e ajuste as configurações do banco de dados para o seguinte:
    ```ini
    DB_CONNECTION=sqlite
    # Remova ou comente as linhas do MySQL
    ```

2.  **Crie o Arquivo do Banco de Dados:**
    ```bash
    touch database/database.sqlite
    ```

3.  **Execute as Migrações:**
    ```bash
    php artisan migrate
    ```
4. **Execute o seeder (popular com dados mocados):**
    ```bash
    php artisan db:seed
    ```

---

## Passo 4: Execução do Projeto

1. **Inicie o servidor de desenvolvimento do Laravel para executar o projeto:**
    ```bash
    php artisan serve
    ```
---

## Passo 5: Url's para execução das api's

1. **Listar o Inventário (GET):**
   ```bash
    curl -X GET http://127.0.0.1:8000/api/inventory
   ```
2. **Registrar uma Entrada de Inventário (POST)**
   ```bash
    curl -X POST http://127.0.0.1:8000/api/inventory \
    -H "Content-Type: application/json" \
    -d '{"sku": "<SKU_DO_PRODUTO>", "quantity": 10}'
   ```
3. **Limpeza do inventário (POST) - Executando como api**
   ```bash
    curl -X POST http://127.0.0.1:8000/api/inventory/clean
   ```
4. **Registrar uma nova Venda (POST)**
   ```bash
    curl -X POST http://127.0.0.1:8000/api/sale \
    -H "Content-Type: application/json" \
    -d '{"items": [{"sku":"<SKU_DO_PRODUTO>", "quantity": 1}]}'
   ```
5. **Consultar uma Venda (GET)**
   ```bash
    curl -X GET http://127.0.0.1:8000/api/sale/<ID_DA_VENDA>
   ```
6. **Processa Vendas pendentes - Baixa estoque (POST) - Executando como api**
   ```bash
   curl -X POST http://127.0.0.1:8000/api/sale/process-pending
   ```
7. **Relatório de Vendas (GET)**
   ```bash
    curl -X GET "http://127.0.0.1:8000/api/reports/sales?start_date=<DATA_INICIAL>&end_date=<DATA_FINAL>&product_sku=<SKU_DO_PRODUTO>"
    ```
---

## Passo 6: Executar os jobs

1. **Inicie o servidor de desenvolvimento do Laravel para executar o projeto:**
    ```bash
    php artisan serve
    ```
1. **Iniciar a execução dos Jobs**  
    Estão para executar a cada minuto como teste configurados em routes/console.php.  
    Abra um terminal novo e separado.  
    Rode o comando abaixo e mantenha-o rodando:  
    ```bash
    php artisan optimize:clear
    php artisan queue:work
    ```    
    Agora, no seu outro terminal, rode o comando do agendador:  
    Ele pode ser adicionado ao cron do sistema operacional  
    ```bash
    php artisan schedule:run
    ```    
