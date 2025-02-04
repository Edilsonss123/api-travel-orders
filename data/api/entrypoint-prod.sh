#!/bin/bash
# Impede que o script seja executado em caso de erro
set -e
LARAVEL_DIR="/var/www/apps/api-travel-orders"
cd $LARAVEL_DIR

if [ -z "$(grep -o 'APP_KEY=.*' .env)" ]; then
    php artisan key:generate --no-interaction
else
    echo "Chave APP_KEY já definida no .env."
fi

echo "Gerando chave JWT se necessário..."
if [ -z "$(grep -o 'JWT_SECRET=.*' .env)" ]; then
    php artisan jwt:secret --no-interaction
else
    echo "Chave JWT já definida no .env."
fi

echo "Limpando cache de configuração, rotas e views..."
php artisan config:clear
php artisan route:clear
php artisan view:clear


# Rodar as migrations do banco de dados
echo "Rodando as migrations..."
php artisan migrate --no-interaction --force

# Rodar os seeders (se necessário)
echo "Rodando os seeders..."
php artisan db:seed --no-interaction


# Iniciar o servidor Laravel:
echo "Iniciando o servidor Laravel..."
php artisan serve --host 0.0.0.0 --port 8000
