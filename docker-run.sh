#!/bin/bash
export UID=$(id -u)
export GID=$(id -g)

# Cores para o terminal
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # Sem cor

echo -e "${BLUE}--- Iniciando Automação Docker (Laravel + Vue) ---${NC}"

# 1. Verificar e criar o arquivo .env no Backend
if [ ! -f "backend/.env" ]; then
    if [ -f "backend/.env.example" ]; then
        echo -e "${YELLOW}Criando .env a partir do .env.example...${NC}"
        cp backend/.env.example backend/.env
    fi
fi

# 2. Garantir que o banco SQLite existe
if [ ! -f "backend/database/database.sqlite" ]; then
    echo -e "${BLUE}Criando database.sqlite...${NC}"
    touch backend/database/database.sqlite
fi

echo -e "${BLUE}Ajustando permissões do banco de dados...${NC}"
chmod -R 777 backend/database # No host, para garantir que o container consiga ler/escrever

# 3. Subir os containers usando o comando moderno (docker compose)
echo -e "${BLUE}Construindo e subindo os containers...${NC}"

docker compose up -d --build

# 4. Comandos do Laravel
echo -e "${BLUE}Finalizando configuração do Laravel...${NC}"

echo -e "${BLUE}Ajustando permissões de storage...${NC}"
docker compose exec backend chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
docker compose exec backend chmod -R 775 /var/www/storage /var/www/bootstrap/cache

docker compose exec backend php artisan key:generate --ansi
docker compose exec backend php artisan migrate:fresh --seed

echo -e "${GREEN}--- Tudo pronto! ---${NC}"
echo -e "Frontend: http://localhost:5173"
echo -e "Backend:  http://localhost:8000"