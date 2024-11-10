#!/bin/bash
#os sleep é a primeira vez que o container do banco subir, ks
sleep 15;
# Impede que o script seja executado em caso de erro
set -e
LARAVEL_DIR="/var/www/apps/api-travel-orders"
cd $LARAVEL_DIR

# Rodar composer install
echo "Instalando dependências do Composer..."
composer install --no-interaction --prefer-dist



sleep 5;

# Gerar a chave JWT e APP_KEY(se necessário)
echo "Gerando chave JWT & APP_KEY..."
php artisan key:generate --no-interaction
php artisan jwt:secret --no-interaction


# Rodar as migrations do banco de dados
echo "Rodando as migrations..."
php artisan migrate --no-interaction --force

# Rodar os seeders (se necessário)
echo "Rodando os seeders..."
php artisan db:seed --no-interaction

# Limpar cache de configuração, rotas e views
echo "Limpando cache de configuração, rotas e views..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Iniciar o servidor Laravel:
echo "Iniciando o servidor Laravel..."
php artisan serve --host 0.0.0.0 --port 8000
