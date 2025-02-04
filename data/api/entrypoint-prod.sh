#!/bin/bash
# Impede que o script seja executado em caso de erro
set -e
LARAVEL_DIR="/var/www/apps/api-travel-orders"
cd $LARAVEL_DIR

# Rodar as migrations do banco de dados
echo "Rodando as migrations..."
php artisan migrate --no-interaction --force

# Rodar os seeders (se necessário)
echo "Rodando os seeders..."
php artisan db:seed --no-interaction


# Iniciar o servidor Laravel:
echo "Iniciando o servidor Laravel..."
php artisan serve --host 0.0.0.0 --port 8000
