# DESAFIO FULLSTACK - SESAB
Aplicação composta por uma API em Laravel e um frontend em Vue.js. O sistema permite a gestão de usuários e perfis, utilizando Docker para orquestração e SQLite como banco de dados local.


## Requisitos
- PHP v8.2+

- Node.js v22+

- Docker Engine & Docker Compose V2

## Iniciando a aplicação
**Antes de tudo temos que iniciar a clonagem do repositorio.**

`git clone https://github.com/alisomuniz-dev/desafio-sesab.git`

Após realizar a clonagem do projeto em sua máquina, o desenvolvedor poderá inicializar a aplicação tanto de maneira "manual" ou através do Docker.

## Iniciar a aplicação em ambiente Docker (Recomendado)

Neste processo não é necessario criar o .env manualmente o script docker-run.sh gera o .env de forma automatica. 

Acesse a pasta do Projeto

`cd desafio-sesab`

Dê permissão de execução para o arquivo docker-run.sh, ele é responsável por buildar a imagem e rodar o contêiner

`chmod +x docker-run.sh`

Execute o arquivo para iniciar o contêiner

`sudo ./docker-run.sh`

## Iniciar a aplicação sem utilização do Docker

Antes de seguir com o passo a passo a seguir é necessário configurar as variáveis de ambiente.

Configurando o .env

    Crie uma cópia do arquivo ".env.example" para ".env" na mesma pasta onde o ".env.example" se encontra. 

### Backend (Laravel):

Entre na pasta onde se encontra o projeto back-end: 
    
`cd backend`

Instale as dependências: 
        
`composer install`

Gere a chave: 
        
`php artisan key:generate`

Execute as migrações: 
    
`php artisan migrate:fresh --seed`

Inicie o servidor: 
        
`php artisan serve`

Agora basta acessar o backend em http://localhost:8000

### Frontend (Vue):

* Entre na pasta: 
    
    `cd frontend`

* Instale as dependências: 
    
    `npm install`

* Inicie o servidor de desenvolvimento: 
    
    `npm run dev`

Agora basta acessar o frontend em http://localhost:5173

## Rotas do Sistema

| Serviço              | URL                                            | Porta         |
| -------------------- | ---------------------------------------------- |---------------|
| Frontend (Vue)       | [http://localhost:5173](http://localhost:5173) |5173           |
| Backend (API)        | [http://localhost:8000](http://localhost:8000) |8000           |

    Caso as portas default utilizadas pela aplicação estejam sendo utilizadas, será necessario encerrar o 
    processo que utiliza a porta(recomendado), ou mudar a configuração de portas já impostas no código,
    nos arquivos docker-compose.yml, api.js, Dockerfile(backend), nginx-backend.conf e docker-run.sh.

### Possíveis correções de erros

* **Permissão de Escrita (Linux/Docker):**
  Se houver erro de `Permission Denied` no Laravel ou no banco SQLite, execute:
  
  `docker compose exec backend chown -R www-data:www-data /var/www/storage /var/www/database`

* **Porta 5173 ou 8000 ocupada:**
  Para encerrar processos que estejam utilizando as portas necessárias:
  
  `sudo fuser -k 5173/tcp`
  
  `sudo fuser -k 8000/tcp`

## Tecnologias Utilizadas

### Backend

* **Framework:** Laravel 12 (API REST)
* **Banco de Dados:** SQLite
* **Servidor Web:** Nginx


### Frontend

* **Framework:** Vue.js 3 (javascript)
* **Build Tool:** Vite
* **Estilização:** Tailwind CSS 3.4
* **HTTP Client:** Axios


## 📂 Estrutura de Pastas

    ```text
    .
    ├── backend/            # Aplicação Laravel (API)
    ├── frontend/           # Aplicação Vue.js (Vite)
    ├── docker-config/      # Configurações de servidor (Nginx)
    ├── docker-compose.yml  # Orquestração de containers
    ├── docker-run.sh       # Script de automação (Setup)
    └── README.md           # Documentação do projeto

## Desenvolvedor

Desenvolvido por **Alison Muniz**
