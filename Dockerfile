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
# RUN composer install --no-interaction --prefer-dist

RUN ls -la 

# Copiar o entrypoint script
COPY ./data/api/entrypoint-prod.sh /var/www/apps/api-travel-orders/entrypoint.sh
RUN chmod +x /var/www/apps/api-travel-orders/entrypoint.sh

# Expor a porta para acesso
EXPOSE 8000

# Definir o entrypoint (onde as migrations e seeders serão rodados na inicialização)
CMD ["/bin/bash", "/var/www/apps/api-travel-orders/entrypoint.sh"]
