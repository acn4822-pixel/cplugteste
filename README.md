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
4. **Execute o seeder: (popular com dados mocados)**
    ```bash
    php artisan db:seed
    ```

---

## Passo 4: Execução do Projeto
Inicie o servidor de desenvolvimento do Laravel para executar o projeto:

```bash
php artisan serve
