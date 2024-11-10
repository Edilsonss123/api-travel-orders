
# Travel Order API

A Travel Order API permite gerenciar e controlar solicitações de viagem, com funcionalidades para listar, criar, recuperar e atualizar informações das ordens de viagem.

## Requisitos
- [Docker](https://docs.docker.com/compose/install/) e [Docker Composer](https://docs.docker.com/compose/install/standalone/)


## Rodando localmente

Clone o projeto

```bash
git clone https://github.com/Edilsonss123/travel-order.git travel-order
```

Entre no diretório do projeto
```bash
cd travel-order
```

Entre no diretório do projeto ``api-travel-orders`` e configure o arquivo ``.env``, utilize o ``.env.example`` como modelo, substituindo a configuração de conexão com o banco de dadoo
```bash
cd travel-order/api-travel-orders

DB_CONNECTION=mysql
DB_HOST=db-travel-orders
DB_PORT=3306
DB_DATABASE=travel-orders
DB_USERNAME=travel-user
DB_PASSWORD="8teste0rd&"
```

Antes de iniciar o container, será preciso tornar o script de inicialização da aplicação executável

```bash
chmod +x travel-order/data/api/entrypoint.sh
```
O bash executa os comandos abaixo, ele possui um sleep para garantir que o banco de dados já estará disponível para utilização.
```bash
# Rodar composer install
echo "Instalando dependências do Composer..."
composer install --no-interaction --prefer-dist

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
```

Inicie o contêiner com o docker, nesse primeiro momento vai demorar um pouco quando executado pela primeira vez.

```bash
docker-compose up -d
```
Ao iniciar o container as depências, migrations e seeders serão invocadas atraves do script bash, que tem como ultima ação subir o a aplicação na porta 8000 do container, que é mapeada para a rede host na porta 2050.


Acessando o serviço de api

```
Acesse a api através da porta 2050 do localhost: https://localhost:2050/
```

## Stack utilizada
- **Ambiente desenvolvimento:** Docker
- **Abordagem arquitetônica:** Microsserviço
- **Back-end:** Laravel
    - Inversão de dependência 
    - Repositório para acesso a camada de dados
    - Revisionable para revisão de alteração através dos logs gravados
- **Autenticação:** JWT
- **Banco De Dados:** MySQL


## Funcionalidades API
 - Viagem:
    - Lista de status
    - Lista de solicitações de viagem
    - Lista de uma Solicitação
    - Solicitação de uma nova viagem
    - Atualização de status de uma solicitação de viagem
 - Autenticação:
    - Criação de usuário
    - Geração do token de acesso do usuário
    - Invalidação do token de acesso do usuário
Para acesso a documentação completa da API consulte a [documentação Postman](https://documenter.getpostman.com/view/5807678/2sAY52dKUX)
