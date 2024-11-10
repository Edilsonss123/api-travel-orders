#!/bin/bash

# Impede que o script seja executado em caso de erro
set -e
LARAVEL_DIR="/var/www/apps/api-travel-orders"
cd $LARAVEL_DIR

# Rodar composer install
echo "Instalando dependências do Composer..."
composer install --no-interaction --prefer-dist

# Gerar a chave de aplicação do Laravel
echo "Gerando chave de aplicação..."
php artisan key:generate --no-interaction

# Gerar a chave JWT (se necessário)
echo "Gerando chave JWT..."
php artisan jwt:secret --no-interaction

# Rodar as migrations do banco de dados
echo "Rodando as migrations..."
php artisan migrate --no-interaction --force

# Rodar os seeders (se necessário)
echo "Rodando os seeders..."
php artisan db:seed --no-interaction

# Iniciar o servidor Laravel:
php artisan serve --host 0.0.0.0 --port 8000
