FROM edilsonss123/php:8.2

# Atualizar pacotes e instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Definir o diretório de trabalho dentro do container
WORKDIR /var/www/apps/api-travel-orders

# Agora copiamos o restante do código do projeto
COPY ./api-travel-orders /var/www/apps/api-travel-orders

# Instalar as dependências do Composer (não é necessário rodar toda vez que o código mudar)
RUN composer install --no-interaction --prefer-dist

# Copiar o entrypoint script
COPY ./data/api/entrypoint-prod.sh /var/www/apps/api-travel-orders/entrypoint.sh
RUN chmod +x /var/www/apps/api-travel-orders/entrypoint.sh

# Gerar as chaves e limpar cache (agora dentro do entrypoint, como discutido anteriormente)
RUN echo "Gerando chave APP_KEY se necessário..." && \
    if [ -z "$(grep -o 'APP_KEY=.*' .env)" ]; then php artisan key:generate --no-interaction; else echo 'Chave APP_KEY já definida no .env.'; fi && \
    echo "Gerando chave JWT se necessário..." && \
    if [ -z "$(grep -o 'JWT_SECRET=.*' .env)" ]; then php artisan jwt:secret --no-interaction; else echo 'Chave JWT já definida no .env.'; fi && \
    echo "Limpando cache de configuração, rotas e views..." && \
    php artisan config:clear && php artisan route:clear && php artisan view:clear

# Expor a porta para acesso
EXPOSE 8000

# Definir o entrypoint (onde as migrations e seeders serão rodados na inicialização)
CMD ["/bin/bash", "/var/www/apps/api-travel-orders/entrypoint.sh"]
